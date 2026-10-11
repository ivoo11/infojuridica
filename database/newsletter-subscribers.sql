CREATE TABLE IF NOT EXISTS newsletter_subscribers (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 email VARCHAR(190) NOT NULL UNIQUE,
 status VARCHAR(20) NOT NULL DEFAULT 'pending',
 requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 confirmed_at DATETIME NULL,
 unsubscribed_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS newsletter_consents (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 subscriber_id BIGINT UNSIGNED NOT NULL,
 consent_version VARCHAR(30) NOT NULL,
 consent_text TEXT NOT NULL,
 source VARCHAR(30) NOT NULL,
 accepted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (subscriber_id) REFERENCES newsletter_subscribers(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS newsletter_request_limits (
 key_hash CHAR(64) PRIMARY KEY,
 attempts INT UNSIGNED NOT NULL DEFAULT 0,
 window_start DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS newsletter_unsubscribe_tokens (
 token_hash CHAR(64) PRIMARY KEY,
 subscriber_id BIGINT UNSIGNED NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 used_at DATETIME NULL,
 FOREIGN KEY (subscriber_id) REFERENCES newsletter_subscribers(id),
 INDEX subscriber_tokens(subscriber_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
