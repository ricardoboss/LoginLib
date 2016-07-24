SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

DROP TABLE `loginlib`.`accounts`;

CREATE TABLE `loginlib`.`accounts` (
  `id` bigint(255) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `email` varchar(45) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `registered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE `loginlib`.`accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `loginlib`.`accounts` MODIFY `id` bigint(255) UNSIGNED NOT NULL AUTO_INCREMENT;