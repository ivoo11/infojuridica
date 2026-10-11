CREATE TABLE IF NOT EXISTS event_days (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 event_id BIGINT UNSIGNED NOT NULL,
 event_date DATE NOT NULL,
 start_time TIME NULL,
 end_time TIME NULL,
 FOREIGN KEY (event_id) REFERENCES events(id),
 UNIQUE KEY event_day (event_id,event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
