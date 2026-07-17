# ER Diagram — Vehicle Service Management System

> This ER Diagram is derived from the Eloquent model relationships and migration foreign keys. The `service_parts` table is a **junction/pivot table** that implements the **many-to-many** relationship between `services` and `parts`.

---

## Entity-Relationship Diagram

```mermaid
erDiagram

    users {
        BIGINT id PK
        VARCHAR name
        VARCHAR email UK
        VARCHAR role "owner | workshop | admin"
        VARCHAR phone
        TIMESTAMP email_verified_at
        VARCHAR password
        VARCHAR remember_token
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    vehicles {
        BIGINT id PK
        BIGINT user_id FK
        VARCHAR make
        VARCHAR model
        YEAR year
        VARCHAR registration_number UK
        VARCHAR chassis_number UK
        VARCHAR color
        VARCHAR fuel_type "petrol | diesel | electric | hybrid"
        INT mileage
        VARCHAR status "active | inactive"
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    workshops {
        BIGINT id PK
        BIGINT user_id FK
        VARCHAR name
        VARCHAR owner_name
        VARCHAR phone
        VARCHAR email
        TEXT address
        VARCHAR city
        VARCHAR license_number UK
        TEXT service_categories
        VARCHAR status "active | inactive | suspended"
        TEXT description
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    bookings {
        BIGINT id PK
        BIGINT user_id FK
        BIGINT vehicle_id FK
        BIGINT workshop_id FK
        DATE booking_date
        TIME booking_time
        VARCHAR service_type
        TEXT problem_description
        VARCHAR status "pending | approved | in_progress | completed | cancelled"
        TEXT notes
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    services {
        BIGINT id PK
        BIGINT booking_id FK
        BIGINT vehicle_id FK
        BIGINT workshop_id FK
        DATE service_date
        TEXT issue_description
        TEXT repair_details
        DECIMAL labor_cost
        DECIMAL parts_cost
        DECIMAL total_cost
        INT mileage_at_service
        DATE next_service_date
        VARCHAR technician_name
        VARCHAR status
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    parts {
        BIGINT id PK
        VARCHAR name
        VARCHAR part_number
        VARCHAR category "engine | brakes | electrical"
        DECIMAL unit_price
        VARCHAR unit "piece | liter | kg"
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    service_parts {
        BIGINT id PK
        BIGINT service_id FK
        BIGINT part_id FK
        INT quantity
        DECIMAL unit_price
        DECIMAL total_price
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    complaints {
        BIGINT id PK
        BIGINT user_id FK
        VARCHAR subject
        TEXT message
        ENUM type "complaint | demand | request | feedback"
        ENUM status "open | in_review | resolved | closed"
        TEXT admin_reply
        TIMESTAMP replied_at
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    audit_logs {
        BIGINT id PK
        BIGINT user_id FK "NULLABLE"
        VARCHAR action "created | updated | deleted"
        VARCHAR model_type
        BIGINT model_id
        JSON old_values
        JSON new_values
        VARCHAR ip_address
        TIMESTAMP created_at
        TIMESTAMP updated_at
    }

    sessions {
        VARCHAR id PK
        BIGINT user_id FK "NULLABLE"
        VARCHAR ip_address
        TEXT user_agent
        LONGTEXT payload
        INT last_activity
    }

    %% ─── Relationships ────────────────────────────────────────

    users ||--o{ vehicles : "owns"
    users ||--o{ workshops : "manages"
    users ||--o{ bookings : "creates"
    users ||--o{ complaints : "submits"
    users ||--o{ audit_logs : "generates"
    users ||--o{ sessions : "has"

    vehicles ||--o{ bookings : "is booked in"
    vehicles ||--o{ services : "is serviced in"

    workshops ||--o{ bookings : "receives"
    workshops ||--o{ services : "performs"

    bookings ||--o| services : "results in"

    services ||--o{ service_parts : "uses"
    parts ||--o{ service_parts : "supplied in"
```

---

## Relationship Summary Table

| Relationship | Type | Description |
|---|---|---|
| `users` → `vehicles` | **One-to-Many** | A user (owner) can own many vehicles |
| `users` → `workshops` | **One-to-Many** | A user (workshop role) can manage many workshops |
| `users` → `bookings` | **One-to-Many** | A user can create many bookings |
| `users` → `complaints` | **One-to-Many** | A user can submit many complaints |
| `users` → `audit_logs` | **One-to-Many** | A user can generate many audit log entries |
| `users` → `sessions` | **One-to-Many** | A user can have many active sessions |
| `vehicles` → `bookings` | **One-to-Many** | A vehicle can have many bookings |
| `vehicles` → `services` | **One-to-Many** | A vehicle can have many service records |
| `workshops` → `bookings` | **One-to-Many** | A workshop can receive many bookings |
| `workshops` → `services` | **One-to-Many** | A workshop can perform many services |
| `bookings` → `services` | **One-to-One** | A booking results in at most one service record |
| `services` ↔ `parts` | **Many-to-Many** | A service uses many parts; a part is used in many services (via `service_parts` pivot) |

---

## Cardinality Notation Key

| Symbol | Meaning |
|--------|---------|
| `\|\|` | Exactly one |
| `o\|` | Zero or one |
| `o{` | Zero or many |
| `\|{` | One or many |
