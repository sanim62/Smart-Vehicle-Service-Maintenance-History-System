<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SqlSetupSeeder extends Seeder
{
    public function run(): void
    {
        // ── Drop and recreate the VIEW (Oracle-compatible syntax) ──────────

        // Oracle does not support DROP VIEW IF EXISTS — use exception handling
        try {
            DB::statement('DROP VIEW vehicle_service_history');
        } catch (\Exception $e) {
            // View didn't exist yet — safe to ignore ORA-00942
        }

        DB::statement("
            CREATE VIEW vehicle_service_history AS
            SELECT
                v.id               AS vehicle_id,
                v.registration_number,
                v.make,
                v.model,
                v.year,
                u.name             AS owner_name,
                s.id               AS service_id,
                s.service_date,
                s.issue_description,
                s.repair_details,
                s.labor_cost,
                s.parts_cost,
                s.total_cost,
                s.mileage_at_service,
                s.next_service_date,
                w.name             AS workshop_name,
                w.city             AS workshop_city,
                b.service_type,
                b.booking_date
            FROM vehicles v
            JOIN users u     ON v.user_id    = u.id
            JOIN services s  ON s.vehicle_id = v.id
            JOIN bookings b  ON s.booking_id = b.id
            JOIN workshops w ON s.workshop_id = w.id
        ");

        // ── Oracle Triggers (PL/SQL syntax) ──────────────────────────────
        // Note: Oracle triggers are already defined in database/plsql/triggers.sql.
        // The Laravel application relies on Eloquent observers for cost updates,
        // so we skip re-creating PL/SQL triggers here to avoid duplication.
        // The triggers.sql file is the authoritative Oracle trigger source.

        $this->command->info('SQL View (vehicle_service_history) created successfully on Oracle!');
    }
}
