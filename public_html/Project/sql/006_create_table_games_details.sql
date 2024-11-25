CREATE TABLE IF NOT EXISTS `Games_details` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `game_id` INT,
    `game_name` VARCHAR(100) NOT NULL,
    `price` VARCHAR(15),
    `release_date` DATE, 
    `developer_name` VARCHAR(50) NOT NULL,
    `publisher_name` VARCHAR(50),
    `franchise_name` VARCHAR(50),
    PRIMARY KEY (`id`),
    UNIQUE (`game_id`)
)