-- ====================================================
-- Oracle PL/SQL Package: pkg_vehicle_service
-- Groups service logic under a single namespace
-- ====================================================

-- 1. PACKAGE SPECIFICATION
CREATE OR REPLACE PACKAGE pkg_vehicle_service AS

    -- Public Function: Check workshop capacity limit (max 5) on a given date
    FUNCTION check_workshop_capacity (
        p_workshop_id IN NUMBER,
        p_date IN DATE
    ) RETURN NUMBER;

    -- Public Procedure: Create booking with rules check
    PROCEDURE create_booking (
        p_user_id IN NUMBER,
        p_vehicle_id IN NUMBER,
        p_workshop_id IN NUMBER,
        p_booking_date IN DATE,
        p_booking_time IN VARCHAR2,
        p_service_type IN VARCHAR2,
        p_problem_description IN CLOB,
        p_notes IN CLOB,
        o_booking_id OUT NUMBER
    );

    -- Public Procedure: Complete service and adjust vehicle mileage & next service date
    PROCEDURE complete_service (
        p_service_id IN NUMBER,
        p_mileage IN NUMBER,
        p_technician IN VARCHAR2,
        p_repair_details IN CLOB
    );

    -- Public Function: Sum all costs spent on completed services for a vehicle
    FUNCTION vehicle_total_spent (
        p_vehicle_id IN NUMBER
    ) RETURN NUMBER;

    -- Public Function: Get aggregate authority commission revenue (2.5%)
    FUNCTION get_commission_revenue RETURN NUMBER;

    -- Public Function: Count active warnings for a workshop
    FUNCTION check_warnings_count (
        p_workshop_id IN NUMBER
    ) RETURN NUMBER;

    -- Public Procedure: Dispatch warning to workshop
    PROCEDURE issue_warning_to_owner (
        p_workshop_id IN NUMBER,
        p_complaint_id IN NUMBER,
        p_admin_id IN NUMBER,
        p_subject IN VARCHAR2,
        p_message IN CLOB,
        p_severity IN VARCHAR2,
        o_warning_id OUT NUMBER
    );

END pkg_vehicle_service;
/

-- 2. PACKAGE BODY
CREATE OR REPLACE PACKAGE BODY pkg_vehicle_service AS

    -- Implementation: check_workshop_capacity
    FUNCTION check_workshop_capacity (
        p_workshop_id IN NUMBER,
        p_date IN DATE
    ) RETURN NUMBER IS
        v_count NUMBER;
    BEGIN
        SELECT COUNT(*) INTO v_count
        FROM bookings
        WHERE workshop_id = p_workshop_id
          AND TRUNC(booking_date) = TRUNC(p_date)
          AND status != 'cancelled';
          
        IF v_count >= 5 THEN
            RETURN 1; -- Capacity limit reached
        ELSE
            RETURN 0; -- Available
        END IF;
    END check_workshop_capacity;

    -- Implementation: create_booking
    PROCEDURE create_booking (
        p_user_id IN NUMBER,
        p_vehicle_id IN NUMBER,
        p_workshop_id IN NUMBER,
        p_booking_date IN DATE,
        p_booking_time IN VARCHAR2,
        p_service_type IN VARCHAR2,
        p_problem_description IN CLOB,
        p_notes IN CLOB,
        o_booking_id OUT NUMBER
    ) IS
        v_workshop_status VARCHAR2(50);
        v_capacity_reached NUMBER;
    BEGIN
        -- 1. Check if booking date is in the future
        IF TRUNC(p_booking_date) <= TRUNC(SYSDATE) THEN
            RAISE_APPLICATION_ERROR(-20001, 'Booking date must be in the future.');
        END IF;

        -- 2. Verify workshop exists and is active
        BEGIN
            SELECT status INTO v_workshop_status
            FROM workshops
            WHERE id = p_workshop_id;
        EXCEPTION
            WHEN NO_DATA_FOUND THEN
                RAISE_APPLICATION_ERROR(-20002, 'Workshop does not exist.');
        END;

        IF v_workshop_status != 'active' THEN
            RAISE_APPLICATION_ERROR(-20003, 'Selected workshop is currently ' || v_workshop_status || ' and cannot accept bookings.');
        END IF;

        -- 3. Check workshop capacity
        v_capacity_reached := check_workshop_capacity(p_workshop_id, p_booking_date);
        IF v_capacity_reached = 1 THEN
            RAISE_APPLICATION_ERROR(-20004, 'Selected workshop has reached its booking capacity limit of 5 for this date.');
        END IF;

        -- 4. Create the booking record
        INSERT INTO bookings (
            user_id,
            vehicle_id,
            workshop_id,
            booking_date,
            booking_time,
            service_type,
            problem_description,
            status,
            notes,
            created_at,
            updated_at
        ) VALUES (
            p_user_id,
            p_vehicle_id,
            p_workshop_id,
            TRUNC(p_booking_date),
            p_booking_time,
            p_service_type,
            p_problem_description,
            'pending',
            p_notes,
            SYSTIMESTAMP,
            SYSTIMESTAMP
        ) RETURNING id INTO o_booking_id;
    END create_booking;

    -- Implementation: complete_service
    PROCEDURE complete_service (
        p_service_id IN NUMBER,
        p_mileage IN NUMBER,
        p_technician IN VARCHAR2,
        p_repair_details IN CLOB
    ) IS
        v_booking_id NUMBER(20);
        v_vehicle_id NUMBER(20);
        v_service_date DATE;
    BEGIN
        -- 1. Fetch related record identifiers and service date
        BEGIN
            SELECT booking_id, vehicle_id, service_date
            INTO v_booking_id, v_vehicle_id, v_service_date
            FROM services
            WHERE id = p_service_id;
        EXCEPTION
            WHEN NO_DATA_FOUND THEN
                RAISE_APPLICATION_ERROR(-20005, 'Service record not found.');
        END;

        -- 2. Update service status details and calculate next service date (+6 months)
        UPDATE services
        SET status = 'completed',
            mileage_at_service = p_mileage,
            technician_name = p_technician,
            repair_details = p_repair_details,
            next_service_date = ADD_MONTHS(v_service_date, 6),
            updated_at = SYSTIMESTAMP
        WHERE id = p_service_id;

        -- 3. Set the booking status to completed
        UPDATE bookings
        SET status = 'completed',
            updated_at = SYSTIMESTAMP
        WHERE id = v_booking_id;

        -- 4. Update the vehicle's current mileage (only if the new mileage is greater)
        UPDATE vehicles
        SET mileage = GREATEST(mileage, NVL(p_mileage, 0)),
            updated_at = SYSTIMESTAMP
        WHERE id = v_vehicle_id;
    END complete_service;

    -- Implementation: vehicle_total_spent
    FUNCTION vehicle_total_spent (
        p_vehicle_id IN NUMBER
    ) RETURN NUMBER IS
        v_total NUMBER(10,2) := 0.00;
    BEGIN
        SELECT COALESCE(SUM(total_cost), 0.00) INTO v_total
        FROM services
        WHERE vehicle_id = p_vehicle_id
          AND status = 'completed';

        RETURN v_total;
    END vehicle_total_spent;

    -- Implementation: get_commission_revenue
    FUNCTION get_commission_revenue RETURN NUMBER IS
        v_revenue NUMBER(10,2) := 0.00;
    BEGIN
        SELECT COALESCE(SUM(commission_amount), 0.00) INTO v_revenue
        FROM payments
        WHERE status = 'completed';

        RETURN v_revenue;
    END get_commission_revenue;

    -- Implementation: check_warnings_count
    FUNCTION check_warnings_count (
        p_workshop_id IN NUMBER
    ) RETURN NUMBER IS
        v_warn_count NUMBER := 0;
    BEGIN
        SELECT COUNT(*) INTO v_warn_count
        FROM warnings
        WHERE workshop_id = p_workshop_id
          AND status = 'active';

        RETURN v_warn_count;
    END check_warnings_count;

    -- Implementation: issue_warning_to_owner
    PROCEDURE issue_warning_to_owner (
        p_workshop_id IN NUMBER,
        p_complaint_id IN NUMBER,
        p_admin_id IN NUMBER,
        p_subject IN VARCHAR2,
        p_message IN CLOB,
        p_severity IN VARCHAR2,
        o_warning_id OUT NUMBER
    ) IS
    BEGIN
        INSERT INTO warnings (
            workshop_id,
            complaint_id,
            admin_id,
            subject,
            warning_message,
            severity,
            status,
            created_at,
            updated_at
        ) VALUES (
            p_workshop_id,
            p_complaint_id,
            p_admin_id,
            p_subject,
            p_message,
            p_severity,
            'active',
            SYSTIMESTAMP,
            SYSTIMESTAMP
        ) RETURNING id INTO o_warning_id;
    END issue_warning_to_owner;

END pkg_vehicle_service;
/
