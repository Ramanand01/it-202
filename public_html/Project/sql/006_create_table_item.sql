CREATE TABLE IF NOT EXISTS `Products`(
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(20) NOT NULL UNIQUE,
    `description` text,
    `category` VARCHAR(50) default '',
    `stock` INT DEFAULT 0, 
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `unit_price` INT,
    `visibility` BOOLEAN,
    CHECK (`stock` >= 0)
)