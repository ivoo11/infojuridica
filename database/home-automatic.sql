CREATE TABLE IF NOT EXISTS home_slot_pins (
 slot_key VARCHAR(20) PRIMARY KEY,
 FOREIGN KEY (slot_key) REFERENCES home_slots(slot_key) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS site_settings (
 setting_key VARCHAR(60) PRIMARY KEY,
 setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
