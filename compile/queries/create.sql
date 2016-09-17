CREATE TABLE travis.`accounts` (
  `id` bigint(255) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `registered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE travis.`login_tokens` (
  `id` bigint(255) UNSIGNED NOT NULL,
  `account_id` bigint(255) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logged_out` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE travis.`accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE travis.`login_tokens`
  ADD PRIMARY KEY (`id`);


ALTER TABLE travis.`accounts`
  MODIFY `id` bigint(255) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE travis.`login_tokens`
  MODIFY `id` bigint(255) UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO travis.`accounts` VALUES (0, 'test', 'test@email.com', '12345', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
INSERT INTO travis.`login_tokens` VALUES (0, 0, '12345', CURRENT_TIMESTAMP, NULL);