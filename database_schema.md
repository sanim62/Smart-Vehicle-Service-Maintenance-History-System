# Database Schema — Vehicle Service Management System

> This schema represents the updated application tables reflecting the new Interactive Map Finder, Platform Payments & 2.5% Commission Split, and targeted Warnings & Compliance features.

---

## 1. `users`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | **PK**, AUTO_INCREMENT |
| `name` | VARCHAR(255) | NOT NULL |
| `email` | VARCHAR(255) | NOT NULL, **UNIQUE** |
| `role` | VARCHAR(255) | NOT NULL, DEFAULT `'owner'` — values: `owner`, `workshop`, `admin` |
| `phone` | VARCHAR(255) | NULLABLE |
| `email_verified_at` | TIMESTAMP | NULLABLE |
| `password` | VARCHAR(255) | NOT NULL |
| `remember_token` | VARCHAR(100) | NULLABLE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 2. `vehicles`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | **PK**, AUTO_INCREMENT |
| `user_id` | BIGINT UNSIGNED | **FK** → `users.id` ON DELETE CASCADE |
| `make` | VARCHAR(255) | NOT NULL |
| `model` | VARCHAR(255) | NOT NULL |
| `year` | YEAR | NOT NULL |
| `registration_number` | VARCHAR(255) | NOT NULL, **UNIQUE** |
| `chassis_number` | VARCHAR(255) | NOT NULL, **UNIQUE** |
| `color` | VARCHAR(255) | NULLABLE |
| `fuel_type` | VARCHAR(255) | NOT NULL, DEFAULT `'petrol'` — values: `petrol`, `diesel`, `electric`, `hybrid` |
| `mileage` | INT | NOT NULL, DEFAULT `0` |
| `status` | VARCHAR(255) | NOT NULL, DEFAULT `'active'` — values: `active`, `inactive` |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 3. `workshops`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | **PK**, AUTO_INCREMENT |
| `user_id` | BIGINT UNSIGNED | **FK** → `users.id` ON DELETE CASCADE |
| `name` | VARCHAR(255) | NOT NULL |
| `owner_name` | VARCHAR(255) | NOT NULL |
| `phone` | VARCHAR(255) | NOT NULL |
| `email` | VARCHAR(255) | NULLABLE |
| `address` | TEXT | NOT NULL |
| `latitude` | DECIMAL(10,7) | NULLABLE — for Leaflet.js nearby map mapping |
| `longitude` | DECIMAL(10,7) | NULLABLE — for Leaflet.js nearby map mapping |
| `city` | VARCHAR(255) | NOT NULL |
| `license_number` | VARCHAR(255) | NULLABLE, **UNIQUE** |
| `service_categories` | TEXT | NOT NULL — JSON or comma-separated |
| `status` | VARCHAR(255) | NOT NULL, DEFAULT `'active'` — values: `active`, `inactive`, `suspended` |
| `description` | TEXT | NULLABLE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 4. `bookings`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | **PK**, AUTO_INCREMENT |
| `user_id` | BIGINT UNSIGNED | **FK** → `users.id` ON DELETE CASCADE |
| `vehicle_id` | BIGINT UNSIGNED | **FK** → `vehicles.id` ON DELETE CASCADE |
| `workshop_id` | BIGINT UNSIGNED | **FK** → `workshops.id` ON DELETE CASCADE |
| `booking_date` | DATE | NOT NULL |
| `booking_time` | TIME | NULLABLE |
| `service_type` | VARCHAR(255) | NOT NULL |
| `problem_description` | TEXT | NULLABLE |
| `status` | VARCHAR(255) | NOT NULL, DEFAULT `'pending'` — values: `pending`, `approved`, `in_progress`, `completed`, `cancelled` |
| `notes` | TEXT | NULLABLE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 5. `services`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | **PK**, AUTO_INCREMENT |
| `booking_id` | BIGINT UNSIGNED | **FK** → `bookings.id` ON DELETE CASCADE |
| `vehicle_id` | BIGINT UNSIGNED | **FK** → `vehicles.id` ON DELETE CASCADE |
| `workshop_id` | BIGINT UNSIGNED | **FK** → `workshops.id` ON DELETE CASCADE |
| `service_date` | DATE | NOT NULL |
| `issue_description` | TEXT | NOT NULL |
| `repair_details` | TEXT | NOT NULL |
| `labor_cost` | DECIMAL(10,2) | NOT NULL, DEFAULT `0.00` |
| `parts_cost` | DECIMAL(10,2) | NOT NULL, DEFAULT `0.00` |
| `total_cost` | DECIMAL(10,2) | NOT NULL, DEFAULT `0.00` |
| `mileage_at_service` | INT | NULLABLE |
| `next_service_date` | DATE | NULLABLE |
| `technician_name` | VARCHAR(255) | NULLABLE |
| `status` | VARCHAR(255) | NOT NULL, DEFAULT `'completed'` |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 6. `parts`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | **PK**, AUTO_INCREMENT |
| `name` | VARCHAR(255) | NOT NULL |
| `part_number` | VARCHAR(255) | NULLABLE |
| `category` | VARCHAR(255) | NULLABLE — values: `engine`, `brakes`, `electrical`, etc. |
| `unit_price` | DECIMAL(10,2) | NOT NULL, DEFAULT `0.00` |
| `unit` | VARCHAR(255) | NOT NULL, DEFAULT `'piece'` — values: `piece`, `liter`, `kg` |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 7. `service_parts` *(Pivot / Junction Table)*

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | **PK**, AUTO_INCREMENT |
| `service_id` | BIGINT UNSIGNED | **FK** → `services.id` ON DELETE CASCADE |
| `part_id` | BIGINT UNSIGNED | **FK** → `parts.id` ON DELETE CASCADE |
| `quantity` | INT | NOT NULL, DEFAULT `1` |
| `unit_price` | DECIMAL(10,2) | NOT NULL — price at time of service |
| `total_price` | DECIMAL(10,2) | NOT NULL — quantity × unit_price |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 8. `complaints`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | **PK**, AUTO_INCREMENT |
| `user_id` | BIGINT UNSIGNED | **FK** → `users.id` ON DELETE CASCADE |
| `workshop_id` | BIGINT UNSIGNED | NULLABLE, **FK** → `workshops.id` ON DELETE SET NULL |
| `subject` | VARCHAR(255) | NOT NULL |
| `message` | TEXT | NOT NULL |
| `type` | ENUM | NOT NULL, DEFAULT `'complaint'` — values: `complaint`, `demand`, `request`, `feedback` |
| `status` | ENUM | NOT NULL, DEFAULT `'open'` — values: `open`, `in_review`, `resolved`, `closed` |
| `admin_reply` | TEXT | NULLABLE |
| `replied_at` | TIMESTAMP | NULLABLE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 9. `payments`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | **PK**, AUTO_INCREMENT |
| `user_id` | BIGINT UNSIGNED | **FK** → `users.id` ON DELETE CASCADE |
| `service_id` | BIGINT UNSIGNED | **FK** → `services.id` ON DELETE CASCADE |
| `workshop_id` | BIGINT UNSIGNED | **FK** → `workshops.id` ON DELETE CASCADE |
| `total_amount` | DECIMAL(10,2) | NOT NULL |
| `commission_rate` | DECIMAL(5,2) | NOT NULL, DEFAULT `2.50` |
| `commission_amount` | DECIMAL(10,2) | NOT NULL — 2.5% of total_amount |
| `workshop_amount` | DECIMAL(10,2) | NOT NULL — 97.5% of total_amount |
| `payment_method` | VARCHAR(255) | NOT NULL, DEFAULT `'card'` |
| `transaction_id` | VARCHAR(255) | NOT NULL, **UNIQUE** |
| `status` | ENUM | NOT NULL, DEFAULT `'completed'` — values: `completed`, `pending`, `failed`, `refunded` |
| `paid_at` | TIMESTAMP | NULLABLE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 10. `warnings`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | **PK**, AUTO_INCREMENT |
| `workshop_id` | BIGINT UNSIGNED | **FK** → `workshops.id` ON DELETE CASCADE |
| `complaint_id` | BIGINT UNSIGNED | NULLABLE, **FK** → `complaints.id` ON DELETE SET NULL |
| `admin_id` | BIGINT UNSIGNED | **FK** → `users.id` ON DELETE CASCADE |
| `subject` | VARCHAR(255) | NOT NULL |
| `warning_message` | TEXT | NOT NULL |
| `severity` | ENUM | NOT NULL, DEFAULT `'medium'` — values: `low`, `medium`, `high`, `critical` |
| `status` | ENUM | NOT NULL, DEFAULT `'active'` — values: `active`, `acknowledged`, `resolved` |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 11. `audit_logs`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | **PK**, AUTO_INCREMENT |
| `user_id` | BIGINT UNSIGNED | NULLABLE, **FK** → `users.id` ON DELETE SET NULL |
| `action` | VARCHAR(255) | NOT NULL — values: `created`, `updated`, `deleted` |
| `model_type` | VARCHAR(255) | NOT NULL — e.g., `Vehicle`, `Service`, `Booking`, `Payment`, `Warning` |
| `model_id` | BIGINT UNSIGNED | NULLABLE |
| `old_values` | JSON | NULLABLE |
| `new_values` | JSON | NULLABLE |
| `ip_address` | VARCHAR(255) | NULLABLE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 12. `sessions`

| Column | Type | Constraints |
|--------|------|-------------|
| `id` | VARCHAR(255) | **PK** |
| `user_id` | BIGINT UNSIGNED | NULLABLE, **FK** → `users.id` (indexed) |
| `ip_address` | VARCHAR(45) | NULLABLE |
| `user_agent` | TEXT | NULLABLE |
| `payload` | LONGTEXT | NOT NULL |
| `last_activity` | INT | NOT NULL (indexed) |

---

## Foreign Key Summary

| Source Table | FK Column | → Target Table | ON DELETE |
|--------------|-----------|----------------|-----------|
| `vehicles` | `user_id` | `users` | CASCADE |
| `workshops` | `user_id` | `users` | CASCADE |
| `bookings` | `user_id` | `users` | CASCADE |
| `bookings` | `vehicle_id` | `vehicles` | CASCADE |
| `bookings` | `workshop_id` | `workshops` | CASCADE |
| `services` | `booking_id` | `bookings` | CASCADE |
| `services` | `vehicle_id` | `vehicles` | CASCADE |
| `services` | `workshop_id` | `workshops` | CASCADE |
| `service_parts` | `service_id` | `services` | CASCADE |
| `service_parts` | `part_id` | `parts` | CASCADE |
| `complaints` | `user_id` | `users` | CASCADE |
| `complaints` | `workshop_id` | `workshops` | SET NULL |
| `payments` | `user_id` | `users` | CASCADE |
| `payments` | `service_id` | `services` | CASCADE |
| `payments` | `workshop_id` | `workshops` | CASCADE |
| `warnings` | `workshop_id` | `workshops` | CASCADE |
| `warnings` | `complaint_id` | `complaints` | SET NULL |
| `warnings` | `admin_id` | `users` | CASCADE |
| `audit_logs` | `user_id` | `users` | SET NULL |
| `sessions` | `user_id` | `users` | — |
