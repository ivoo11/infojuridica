CREATE TABLE IF NOT EXISTS newsletter_campaigns (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 edition_id BIGINT UNSIGNED NOT NULL UNIQUE,
 snapshot LONGTEXT NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (edition_id) REFERENCES newsletters(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS newsletter_mail_jobs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 campaign_id BIGINT UNSIGNED NULL,
 subscriber_id BIGINT UNSIGNED NULL,
 kind VARCHAR(20) NOT NULL,
 recipient VARCHAR(190) NOT NULL,
 subject VARCHAR(190) NOT NULL DEFAULT '',
 html MEDIUMTEXT NULL,
 status VARCHAR(20) NOT NULL DEFAULT 'queued',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 reserved_at DATETIME NULL,
 sent_at DATETIME NULL,
 last_error VARCHAR(240) NOT NULL DEFAULT '',
 FOREIGN KEY (campaign_id) REFERENCES newsletter_campaigns(id),
 FOREIGN KEY (subscriber_id) REFERENCES newsletter_subscribers(id),
 UNIQUE campaign_subscriber(campaign_id,subscriber_id),
 INDEX mail_queue(status,id), INDEX mail_attempts(reserved_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
