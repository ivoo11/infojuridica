CREATE TABLE IF NOT EXISTS site_settings (
 setting_key VARCHAR(60) PRIMARY KEY,
 setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
