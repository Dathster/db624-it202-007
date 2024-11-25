CREATE TABLE IF NOT EXISTS `Game_requirements` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `game_id` INT NOT NULL,
    `requirement_type` ENUM('min', 'recom'),
    `os_version` VARCHAR(100),
    `processor` VARCHAR(100),
    `graphics` VARCHAR(100),
    `memory` VARCHAR(5),
    `storage` VARCHAR(6),
    PRIMARY KEY (`id`),
    FOREIGN KEY (`game_id`) REFERENCES `Games_details`(`game_id`) ON DELETE CASCADE
)
