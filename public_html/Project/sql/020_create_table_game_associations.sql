CREATE TABLE IF NOT EXISTS  `Game_associations`
(
    `id`         int auto_increment not null,
    `user_id`    int,
    `game_id`  int,
    `created`    timestamp default current_timestamp,
    `modified`   timestamp default current_timestamp on update current_timestamp,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES Users(`id`) on delete cascade,
    FOREIGN KEY (`game_id`) REFERENCES Games_details(`game_id`) on delete cascade,
    UNIQUE KEY (`user_id`, `game_id`)
)