alter table `Games_details`
modify column `game_id` int unique not null;

alter table `Games_details`
add column `from_api` boolean default true;

alter table `Games_details`
modify column `game_name` VARCHAR(100) not null unique;

alter table `Games_details`
modify column `price` DECIMAL(9,2);