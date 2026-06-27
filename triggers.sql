-- ==========================================
-- PL/SQL Triggers for Vehicle Service System
-- ==========================================

-- Trigger 1: Auto-calculate service_parts total_price before insert or update
CREATE OR REPLACE TRIGGER trg_service_parts_total
BEFORE INSERT OR UPDATE ON service_parts
FOR EACH ROW
BEGIN
    :new.total_price := :new.quantity * :new.unit_price;
END;
/

-- Trigger 2: Maintain services parts_cost and total_cost when service_parts are modified
-- Uses a running-total update strategy to prevent ORA-04091: Mutating Table Error
CREATE OR REPLACE TRIGGER trg_update_service_cost
AFTER INSERT OR UPDATE OR DELETE ON service_parts
FOR EACH ROW
DECLARE
    v_diff NUMBER(10,2) := 0.00;
    v_service_id NUMBER(20);
BEGIN
    IF INSERTING THEN
        v_diff := :new.total_price;
        v_service_id := :new.service_id;
    ELSIF UPDATING THEN
        v_diff := :new.total_price - :old.total_price;
        v_service_id := :new.service_id;
        
        -- If for some reason the part is reassigned to a different service (extremely rare)
        IF :new.service_id != :old.service_id THEN
            -- Deduct from old service
            UPDATE services 
            SET parts_cost = parts_cost - :old.total_price
            WHERE id = :old.service_id;
            
            -- Set diff to the full price for the new service
            v_diff := :new.total_price;
        END IF;
    ELSIF DELETING THEN
        v_diff := - :old.total_price;
        v_service_id := :old.service_id;
    END IF;

    -- Apply the difference to the target service
    UPDATE services
    SET parts_cost = parts_cost + v_diff
    WHERE id = v_service_id;
END;
/

-- Trigger 3: Automatically compute total_cost in services whenever labor_cost or parts_cost changes
CREATE OR REPLACE TRIGGER trg_calculate_service_total
BEFORE INSERT OR UPDATE OF labor_cost, parts_cost ON services
FOR EACH ROW
BEGIN
    :new.total_cost := :new.labor_cost + :new.parts_cost;
END;
/

-- Trigger 4: Automatically audit operations on the vehicles table
CREATE OR REPLACE TRIGGER trg_audit_vehicles
AFTER INSERT OR UPDATE OR DELETE ON vehicles
FOR EACH ROW
DECLARE
    v_action VARCHAR2(50);
    v_model_id NUMBER(20);
    v_old_json CLOB := NULL;
    v_new_json CLOB := NULL;
BEGIN
    IF INSERTING THEN
        v_action := 'created';
        v_model_id := :new.id;
        v_new_json := JSON_OBJECT(
            'user_id' VALUE :new.user_id,
            'make' VALUE :new.make,
            'model' VALUE :new.model,
            'year' VALUE :new.year,
            'registration_number' VALUE :new.registration_number,
            'chassis_number' VALUE :new.chassis_number,
            'color' VALUE :new.color,
            'fuel_type' VALUE :new.fuel_type,
            'mileage' VALUE :new.mileage,
            'status' VALUE :new.status
        );
    ELSIF UPDATING THEN
        v_action := 'updated';
        v_model_id := :new.id;
        v_old_json := JSON_OBJECT(
            'user_id' VALUE :old.user_id,
            'make' VALUE :old.make,
            'model' VALUE :old.model,
            'year' VALUE :old.year,
            'registration_number' VALUE :old.registration_number,
            'chassis_number' VALUE :old.chassis_number,
            'color' VALUE :old.color,
            'fuel_type' VALUE :old.fuel_type,
            'mileage' VALUE :old.mileage,
            'status' VALUE :old.status
        );
        v_new_json := JSON_OBJECT(
            'user_id' VALUE :new.user_id,
            'make' VALUE :new.make,
            'model' VALUE :new.model,
            'year' VALUE :new.year,
            'registration_number' VALUE :new.registration_number,
            'chassis_number' VALUE :new.chassis_number,
            'color' VALUE :new.color,
            'fuel_type' VALUE :new.fuel_type,
            'mileage' VALUE :new.mileage,
            'status' VALUE :new.status
        );
    ELSIF DELETING THEN
        v_action := 'deleted';
        v_model_id := :old.id;
        v_old_json := JSON_OBJECT(
            'user_id' VALUE :old.user_id,
            'make' VALUE :old.make,
            'model' VALUE :old.model,
            'year' VALUE :old.year,
            'registration_number' VALUE :old.registration_number,
            'chassis_number' VALUE :old.chassis_number,
            'color' VALUE :old.color,
            'fuel_type' VALUE :old.fuel_type,
            'mileage' VALUE :old.mileage,
            'status' VALUE :old.status
        );
    END IF;

    -- Insert record into audit_logs table
    INSERT INTO audit_logs (
        user_id,
        action,
        model_type,
        model_id,
        old_values,
        new_values,
        ip_address,
        created_at,
        updated_at
    ) VALUES (
        COALESCE(SYS_CONTEXT('USERENV', 'CLIENT_IDENTIFIER'), NULL), -- Check context user if available
        v_action,
        'Vehicle',
        v_model_id,
        v_old_json,
        v_new_json,
        SYS_CONTEXT('USERENV', 'IP_ADDRESS'),
        SYSTIMESTAMP,
        SYSTIMESTAMP
    );
END;
/
