-- ============================================================
-- PASTE THIS IN: database/seeders/DatabaseSeeder.php
-- OR run manually via: php artisan db:seed --class=SqlSetupSeeder
-- ============================================================

-- ✅ SQL VIEW: Full service history per vehicle
CREATE OR REPLACE VIEW vehicle_service_history AS
SELECT
    v.id AS vehicle_id,
    v.registration_number,
    v.make,
    v.model,
    v.year,
    u.name AS owner_name,
    s.id AS service_id,
    s.service_date,
    s.issue_description,
    s.repair_details,
    s.labor_cost,
    s.parts_cost,
    s.total_cost,
    s.mileage_at_service,
    s.next_service_date,
    w.name AS workshop_name,
    w.city AS workshop_city,
    b.service_type,
    b.booking_date
FROM vehicles v
JOIN users u ON v.user_id = u.id
JOIN services s ON s.vehicle_id = v.id
JOIN bookings b ON s.booking_id = b.id
JOIN workshops w ON s.workshop_id = w.id
ORDER BY s.service_date DESC;


-- ✅ TRIGGER: Auto-update parts_cost and total_cost in services
-- when a service_part is INSERTED
DELIMITER $$

CREATE TRIGGER after_service_part_insert
AFTER INSERT ON service_parts
FOR EACH ROW
BEGIN
    UPDATE services
    SET
        parts_cost = (
            SELECT COALESCE(SUM(total_price), 0)
            FROM service_parts
            WHERE service_id = NEW.service_id
        ),
        total_cost = labor_cost + (
            SELECT COALESCE(SUM(total_price), 0)
            FROM service_parts
            WHERE service_id = NEW.service_id
        )
    WHERE id = NEW.service_id;
END$$

-- ✅ TRIGGER: Auto-update when service_part is UPDATED
CREATE TRIGGER after_service_part_update
AFTER UPDATE ON service_parts
FOR EACH ROW
BEGIN
    UPDATE services
    SET
        parts_cost = (
            SELECT COALESCE(SUM(total_price), 0)
            FROM service_parts
            WHERE service_id = NEW.service_id
        ),
        total_cost = labor_cost + (
            SELECT COALESCE(SUM(total_price), 0)
            FROM service_parts
            WHERE service_id = NEW.service_id
        )
    WHERE id = NEW.service_id;
END$$

-- ✅ TRIGGER: Auto-update when service_part is DELETED
CREATE TRIGGER after_service_part_delete
AFTER DELETE ON service_parts
FOR EACH ROW
BEGIN
    UPDATE services
    SET
        parts_cost = (
            SELECT COALESCE(SUM(total_price), 0)
            FROM service_parts
            WHERE service_id = OLD.service_id
        ),
        total_cost = labor_cost + (
            SELECT COALESCE(SUM(total_price), 0)
            FROM service_parts
            WHERE service_id = OLD.service_id
        )
    WHERE id = OLD.service_id;
END$$

DELIMITER ;
