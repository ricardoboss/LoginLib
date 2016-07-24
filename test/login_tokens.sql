SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

DROP TABLE IF EXISTS `loginlib`.`login_tokens`;

CREATE TABLE `loginlib`.`login_tokens` (
  `id` bigint(255) UNSIGNED NOT NULL,
  `account_id` bigint(255) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logged_out` timestamp NULL DEFAULT NULL
);

ALTER TABLE `loginlib`.`login_tokens` ADD PRIMARY KEY (`id`);

ALTER TABLE `loginlib`.`login_tokens` MODIFY `id` bigint(255) UNSIGNED NOT NULL AUTO_INCREMENT;