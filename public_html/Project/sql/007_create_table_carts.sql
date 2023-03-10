CREATE TABLE IF NOT EXISTS `cart` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NULL DEFAULT NULL,
  `user_id` BIGINT NULL DEFAULT NULL,
  `desired_quantity`INT DEFAULT 1,
  `unit_cost` INT,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY (`user_id`,`product_id`)
)