CREATE TABLE IF NOT EXISTS `ratings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NULL,
  `user_id` INT NULL,
  `rating`  INT,
  `comment` text,
  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 
)