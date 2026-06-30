# Oracle PL/SQL Database Layer Guide

This directory contains the Oracle database definitions, triggers, and procedural logic designed for the **Vehicle Service Management System**. These files can be run and tested in **Oracle SQL Developer**, **DBeaver**, or online in **[Oracle Live SQL](https://livesql.oracle.com/)**.

## File Summary

| File | Description |
|------|-------------|
| [schema.sql](file:///c:/Users/user/Herd/database%20project/vehicle-service/database/plsql/schema.sql) | DDL script to safely drop and recreate the 12 application tables with Oracle-compatible data types. |
| [triggers.sql](file:///c:/Users/user/Herd/database%20project/vehicle-service/database/plsql/triggers.sql) | Triggers for calculating part totals, updating service costs (avoiding mutating tables), auditing vehicle updates, and auto-calculating payment commissions. |
| [procedures_functions.sql](file:///c:/Users/user/Herd/database%20project/vehicle-service/database/plsql/procedures_functions.sql) | Standalone procedures and functions for capacity verification, booking, service completion, and price calculations. |
| [package.sql](file:///c:/Users/user/Herd/database%20project/vehicle-service/database/plsql/package.sql) | Groups all system procedures and functions under a single package: `PKG_VEHICLE_SERVICE`. |
| [seed.sql](file:///c:/Users/user/Herd/database%20project/vehicle-service/database/plsql/seed.sql) | Inserts base records (users, workshops, vehicles, parts, complaints, and warnings) to prepare the environment for verification. |

---

## Schema — Table Summary

| # | Table | Key Columns |
|---|-------|-------------|
| 1 | `USERS` | `id`, `name`, `email`, `role` (`owner`/`workshop`/`admin`), `phone`, **`national_id`** |
| 2 | `VEHICLES` | `id`, `user_id`, `make`, `model`, `year`, `registration_number`, `fuel_type`, `mileage` |
| 3 | `WORKSHOPS` | `id`, `user_id`, `name`, `license_number`, `latitude`, `longitude`, **`bank_account`** |
| 4 | `BOOKINGS` | `id`, `user_id`, `vehicle_id`, `workshop_id`, `booking_date`, `status` |
| 5 | `SERVICES` | `id`, `booking_id`, `labor_cost`, `parts_cost` *(auto)*, `total_cost` *(auto)* |
| 6 | `PARTS` | `id`, `name`, `part_number`, `unit_price` |
| 7 | `SERVICE_PARTS` | `id`, `service_id`, `part_id`, `quantity`, `total_price` *(auto)* |
| 8 | `COMPLAINTS` | `id`, `user_id`, `workshop_id`, `type`, `status`, `admin_reply` |
| 9 | `PAYMENTS` | `id`, `service_id`, `total_amount`, `commission_amount` *(auto)*, `workshop_amount` *(auto)* |
| 10 | `WARNINGS` | `id`, `workshop_id`, `complaint_id`, `severity`, `status` |
| 11 | `AUDIT_LOGS` | `id`, `user_id`, `action`, `model_type`, `old_values`, `new_values` |
| 12 | `SESSIONS` | `id`, `user_id`, `payload`, `last_activity` |

---

## Changelog

### 2026-06-29 — Registration Fields Update

Two new columns were added to support the expanded registration forms:

| Table | New Column | Type | Notes |
|-------|-----------|------|-------|
| `USERS` | `national_id` | `VARCHAR2(50) NULL` | National ID / civil registration number required at sign-up |
| `WORKSHOPS` | `bank_account` | `VARCHAR2(255) NULL` | IBAN / bank account number for workshop payout processing |

**For existing Oracle databases** (if you have already run `schema.sql` before this date), apply these `ALTER TABLE` statements instead of re-running the full schema:

```sql
-- Add national_id to USERS table
ALTER TABLE users ADD (national_id VARCHAR2(50) NULL);

-- Add bank_account to WORKSHOPS table
ALTER TABLE workshops ADD (bank_account VARCHAR2(255) NULL);

COMMIT;
```

---

## Order of Execution

Execute the scripts in the following order for a **fresh install**:

1. **`schema.sql`** — Defines all 12 tables and foreign-key constraints.
2. **`triggers.sql`** — Adds automatic data calculations and audit logging.
3. **`procedures_functions.sql`** — Compiles standalone stored tools.
4. **`package.sql`** — Bundles everything into the `PKG_VEHICLE_SERVICE` namespace.
5. **`seed.sql`** — Populates base data for testing and demonstration.

---

## Test Verification Cases

Once compiled and seeded, you can verify each component by running the following PL/SQL verification blocks.

### 0. Verify New Columns (2026-06-29)

```sql
-- Check users have national_id populated
SELECT id, name, phone, national_id FROM users ORDER BY id;

-- Check workshops have bank_account populated
SELECT id, name, license_number, bank_account FROM workshops ORDER BY id;
```

---

### 1. Test Triggers (Auto-Calculation of Costs)

Ensure server output is enabled (useful for SQL Developer/Live SQL):
```sql
SET SERVEROUTPUT ON;
```

**Scenario**: Add a service record, add two service parts to it, and check if the total parts cost and overall service cost update automatically.

```sql
-- Create a dummy service record
INSERT INTO services (id, booking_id, vehicle_id, workshop_id, service_date, issue_description, repair_details, labor_cost)
VALUES (10, 1, 1, 1, DATE '2026-06-20', 'Routine maintenance check', 'None yet', 100.00);

-- Query the service (parts_cost should be 0.00, total_cost should be 100.00)
SELECT labor_cost, parts_cost, total_cost FROM services WHERE id = 10;

-- Insert service parts (Trigger 1: total_price = quantity * unit_price)
-- (Trigger 2: updates services.parts_cost and services.total_cost)
INSERT INTO service_parts (service_id, part_id, quantity, unit_price) VALUES (10, 1, 4, 39.99); -- 4 liters of oil = 159.96
INSERT INTO service_parts (service_id, part_id, quantity, unit_price) VALUES (10, 2, 1, 14.50); -- 1 oil filter = 14.50

-- Query the service table again (parts_cost = 174.46, total_cost = 274.46)
SELECT labor_cost, parts_cost, total_cost FROM services WHERE id = 10;

-- Query the service parts table (total_price auto-calculated)
SELECT part_id, quantity, unit_price, total_price FROM service_parts WHERE service_id = 10;
```

---

### 2. Test Booking Creation & Capacity Validation (`PRC_CREATE_BOOKING`)

**A. Success Case (Future Date & Active Workshop)**
Creates a booking successfully on a future date.

```sql
DECLARE
    v_new_booking_id NUMBER;
BEGIN
    pkg_vehicle_service.create_booking(
        p_user_id            => 1,
        p_vehicle_id         => 1,
        p_workshop_id        => 1,
        p_booking_date       => SYSDATE + 2, -- 2 days in the future
        p_booking_time       => '10:00:00',
        p_service_type       => 'Oil Change',
        p_problem_description => 'Engine service required.',
        p_notes              => 'Owner will wait at the lobby.',
        o_booking_id         => v_new_booking_id
    );
    DBMS_OUTPUT.PUT_LINE('Booking created successfully! ID: ' || v_new_booking_id);
END;
/
```

**B. Failure Case 1 (Date is in the Past)**
Should fail with `ORA-20001: Booking date must be in the future.`

```sql
DECLARE
    v_new_booking_id NUMBER;
BEGIN
    pkg_vehicle_service.create_booking(
        p_user_id            => 1,
        p_vehicle_id         => 1,
        p_workshop_id        => 1,
        p_booking_date       => SYSDATE - 1, -- Yesterday
        p_booking_time       => '10:00:00',
        p_service_type       => 'Brakes Check',
        p_problem_description => 'Squeaking noises.',
        p_notes              => NULL,
        o_booking_id         => v_new_booking_id
    );
END;
/
```

**C. Failure Case 2 (Workshop Overbooked)**
We simulate reaching capacity limit of 5. Should fail with `ORA-20004: Selected workshop has reached its booking capacity limit of 5 for this date.`

```sql
DECLARE
    v_dummy_id NUMBER;
    v_test_date DATE := SYSDATE + 5;
BEGIN
    -- Force insert 5 bookings directly for workshop 1 on this date
    FOR i IN 1..5 LOOP
        INSERT INTO bookings (user_id, vehicle_id, workshop_id, booking_date, service_type, status)
        VALUES (1, 1, 1, v_test_date, 'Routine Check', 'approved');
    END LOOP;
    COMMIT;
    
    -- Try to add a 6th booking via the procedure
    pkg_vehicle_service.create_booking(
        p_user_id            => 1,
        p_vehicle_id         => 1,
        p_workshop_id        => 1,
        p_booking_date       => v_test_date,
        p_booking_time       => '14:00:00',
        p_service_type       => 'Tire Rotation',
        p_problem_description => NULL,
        p_notes              => NULL,
        o_booking_id         => v_dummy_id
    );
END;
/
```

---

### 3. Test Service Completion & Mileage Sync (`PRC_COMPLETE_SERVICE`)

**Scenario**: Complete the service we created (ID 10). It should transition status to `completed`, set the next maintenance date to 6 months from the service date, and raise the vehicle mileage.

```sql
-- Query initial state of vehicle mileage (currently 45000)
SELECT mileage FROM vehicles WHERE id = 1;

-- Complete service 10, updating mileage to 46200
BEGIN
    pkg_vehicle_service.complete_service(
        p_service_id    => 10,
        p_mileage       => 46200,
        p_technician    => 'Dave Martinez',
        p_repair_details => 'Replaced engine oil and filter. Checked fluid levels.'
    );
    COMMIT;
END;
/

-- Verify the service next_service_date is scheduled (+6 months)
SELECT next_service_date, technician_name, status FROM services WHERE id = 10;

-- Verify the vehicle mileage got updated to 46200
SELECT mileage FROM vehicles WHERE id = 1;
```

---

### 4. Test Total Vehicle Expenditure (`FN_VEHICLE_TOTAL_SPENT`)

**Scenario**: Calculate total money spent on vehicle ID 1.

```sql
DECLARE
    v_total NUMBER(10,2);
BEGIN
    v_total := pkg_vehicle_service.vehicle_total_spent(1);
    DBMS_OUTPUT.PUT_LINE('Total repair expenditure for vehicle ID 1: $' || v_total);
END;
/
```

---

### 5. Test Audit Logs Trigger (`TRG_AUDIT_VEHICLES`)

**Scenario**: Make an edit to a vehicle and verify that a row gets automatically added to the `audit_logs` table with old and new state details.

```sql
-- Update the color of vehicle 2
UPDATE vehicles SET color = 'Matte Black' WHERE id = 2;
COMMIT;

-- Query the audit logs table
SELECT action, model_type, model_id, old_values, new_values, ip_address, created_at 
FROM audit_logs 
ORDER BY id DESC;
```

---

### 6. Test Payment Commission Trigger (`TRG_CALCULATE_PAYMENT_COMMISSION`)

**Scenario**: Insert a payment and verify the 2.5% commission is automatically split.

```sql
-- First create a booking and service (if not already done above)
-- Then insert a payment for service 10
INSERT INTO payments (user_id, service_id, workshop_id, total_amount, payment_method, transaction_id, status, paid_at)
VALUES (1, 10, 1, 274.46, 'card', 'TXN-' || TO_CHAR(SYSTIMESTAMP, 'YYYYMMDDHH24MISSFF3'), 'completed', SYSTIMESTAMP);
COMMIT;

-- Check auto-calculated commission and workshop payout
SELECT total_amount, commission_rate, commission_amount, workshop_amount
FROM payments
ORDER BY id DESC
FETCH FIRST 1 ROW ONLY;
-- Expected: commission_amount = 6.86, workshop_amount = 267.60
```
