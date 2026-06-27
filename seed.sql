-- ====================================================
-- Oracle Database Seed Data Script
-- ====================================================

-- Clear existing data (in reverse dependency order to respect FK constraints)
DELETE FROM sessions;
DELETE FROM audit_logs;
DELETE FROM complaints;
DELETE FROM service_parts;
DELETE FROM services;
DELETE FROM bookings;
DELETE FROM workshops;
DELETE FROM vehicles;
DELETE FROM users;
DELETE FROM parts;

-- Seed USERS
INSERT INTO users (id, name, email, password, role, phone)
VALUES (1, 'John Doe', 'john@example.com', '$2y$12$K1r.mZ0wM5F883Yc2a.o.eeG4m2wzF.L1FjGk8nJmS/rB27y8t12S', 'owner', '123-456-7890');

INSERT INTO users (id, name, email, password, role, phone)
VALUES (2, 'Alice Smith', 'alice@example.com', '$2y$12$K1r.mZ0wM5F883Yc2a.o.eeG4m2wzF.L1FjGk8nJmS/rB27y8t12S', 'owner', '098-765-4321');

INSERT INTO users (id, name, email, password, role, phone)
VALUES (3, 'Bob Miller', 'bob@example.com', '$2y$12$K1r.mZ0wM5F883Yc2a.o.eeG4m2wzF.L1FjGk8nJmS/rB27y8t12S', 'workshop', '555-0199');

INSERT INTO users (id, name, email, password, role, phone)
VALUES (4, 'Charlie Brown', 'charlie@example.com', '$2y$12$K1r.mZ0wM5F883Yc2a.o.eeG4m2wzF.L1FjGk8nJmS/rB27y8t12S', 'workshop', '555-0288');

INSERT INTO users (id, name, email, password, role, phone)
VALUES (5, 'System Admin', 'admin@example.com', '$2y$12$K1r.mZ0wM5F883Yc2a.o.eeG4m2wzF.L1FjGk8nJmS/rB27y8t12S', 'admin', '555-1000');


-- Seed VEHICLES
INSERT INTO vehicles (id, user_id, make, model, year, registration_number, chassis_number, color, fuel_type, mileage, status)
VALUES (1, 1, 'Toyota', 'Corolla', 2021, 'NY-12345', '1T9JT1AA3L1234567', 'Silver', 'petrol', 45000, 'active');

INSERT INTO vehicles (id, user_id, make, model, year, registration_number, chassis_number, color, fuel_type, mileage, status)
VALUES (2, 2, 'Honda', 'Civic', 2020, 'CA-98765', '1HGFC2F83L7654321', 'Black', 'petrol', 35000, 'active');

INSERT INTO vehicles (id, user_id, make, model, year, registration_number, chassis_number, color, fuel_type, mileage, status)
VALUES (3, 1, 'Tesla', 'Model 3', 2023, 'TX-55555', '5YJ3E1EA5L9999999', 'White', 'electric', 12000, 'active');


-- Seed WORKSHOPS
INSERT INTO workshops (id, user_id, name, owner_name, phone, email, address, city, license_number, service_categories, status, description)
VALUES (1, 3, 'Bobs Auto Care', 'Bob Miller', '555-0199', 'info@bobsautocare.com', '123 Main St, New York', 'New York', 'LIC-NY-9876', 'oil_change,brakes,tires,engine', 'active', 'Your friendly neighborhood auto repair shop.');

INSERT INTO workshops (id, user_id, name, owner_name, phone, email, address, city, license_number, service_categories, status, description)
VALUES (2, 4, 'Elite Motors', 'Charlie Brown', '555-0288', 'contact@elitemotors.com', '456 Broadway, Los Angeles', 'Los Angeles', 'LIC-CA-5432', 'oil_change,brakes,electrical,battery,suspension', 'active', 'Premium auto service and maintenance solutions.');


-- Seed PARTS
INSERT INTO parts (id, name, part_number, category, unit_price, unit)
VALUES (1, 'Synthetic Motor Oil 5W-30', 'OIL-5W30-4L', 'engine', 39.99, 'liter');

INSERT INTO parts (id, name, part_number, category, unit_price, unit)
VALUES (2, 'Premium Oil Filter', 'FIL-OIL-09', 'engine', 14.50, 'piece');

INSERT INTO parts (id, name, part_number, category, unit_price, unit)
VALUES (3, 'Front Brake Pads (Set)', 'BRK-PAD-F2', 'brakes', 59.99, 'piece');

INSERT INTO parts (id, name, part_number, category, unit_price, unit)
VALUES (4, 'Platinum Spark Plug', 'SPK-PLG-PL1', 'engine', 8.25, 'piece');

INSERT INTO parts (id, name, part_number, category, unit_price, unit)
VALUES (5, '12V Lead Acid Battery', 'BAT-12V-80A', 'electrical', 120.00, 'piece');

-- Commit transaction
COMMIT;
