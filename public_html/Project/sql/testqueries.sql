with combined_tags as (
    select gd.game_id, gd.game_name, group_concat(gt.tag) as tags from Games_details gd, Game_tags gt
where gd.game_id = gt.game_id
group by gd.game_id
)
select gd1.game_id, gd1.game_name, gd1.price,



ct.tags, gd1.created, gd1.modified, gd1.from_api from Games_details gd1 left join combined_tags ct
on gd1.game_id = ct.game_id;


with `game_reqs` as (
select `gd`.`game_id`, `gd`.`game_name`, `gr`.`requirement_type`, `gr`.`os_version`, `gr`.`processor`, `gr`.`graphics`, `gr`.`memory`, `gr`.`storage`, `gr`.`created`, `gr`.`modified` from `Games_details` `gd`, `Game_requirements` `gr`
where `gd`.`game_id` = `gr`.`game_id`
)
select * from `game_reqs`
order by `game_reqs`.`created`;

select game_id, type, group_concat(url) from Game_media
group by id, type;


with `concat_ss` as(
select `game_id`, group_concat(`url`) as `urls`, `type` from `Game_media` WHERE
`type` = 'screenshot'
group by `game_id`
),
`concat_vid` as (select `game_id`, group_concat(`url`) as `urls`, `type` from `Game_media` WHERE
`type` = 'video'
group by `game_id`),
`combined_vid_ss` as (
select * from `concat_ss` union 
select * from `concat_vid`
),
`grouped_game_media` as (
select `gd`.`game_id`, `gd`.`game_name`, `cvs`.`urls`, `cvs`.`type` from `Games_details` `gd`, `combined_vid_ss` `cvs`
where `gd`.`game_id` = `cvs`.`game_id`
order by `gd`.`game_name`
)
select * from `grouped_game_media`;


with `joined_name_urls` as (
    select `gm`.`game_id`, `gd`.`game_name`, `gm`.`url`, `gm`.`type` from `Games_details` `gd`, `Game_media` `gm`
    where `gm`.`game_id` = `gd`.`game_id`
)
select * from `joined_name_urls`;



select game_name, price, release_date, developer_name, IF(from_api, 'true', 'false') as from_api from Games_details;

with ct as (
    select game_id, group_concat(`tag` SEPARATOR ', ') as combined_tags from Game_tags
    group by game_id
)
select `ct`.`combined_tags` from `ct` where `ct`.`game_id` = 1085660; 

select url from Game_media where type = 'screenshot';

select * from Game_requirements;


select `game_id`, group_concat(`tag` SEPARATOR ', ') as `combined_tags` from `Game_tags` group by `game_id` where `game_id`=1085660;

select `url` from `Game_media` where `type` = 'screenshot' and `game_id` = 1085660


select * from Games_details order by developer_name asc;

select * from Games_details order by price desc;

select 
from Games_details;




with ct as (
    select game_id, group_concat(`tag` SEPARATOR ', ') as combined_tags from Game_tags
    group by game_id
)
select Games_details.`game_id`, `game_name`, CASE 
WHEN price = 0.00 THEN 'Free To Play'
        ELSE CONCAT('$', FORMAT(price, 2))
    END AS `price`, `release_date`, `developer_name`, IF(`from_api`, 'true', 'false') as `from_api`, `ct`.combined_tags from `Games_details`, ct where Games_details.game_id = ct.game_id;

with `ct` as (
    select `game_id`, group_concat(`tag` separator ', ') as `combined_tags` from `Game_tags`
    group by `game_id`
),
details as ( 
select 
`gd`.`game_id`,
`gd`.`game_name`,
`gd`.`release_date`,
`gd`.`developer_name`,
`ct`.`combined_tags`,
case when `gd`.`price` = 0.00 then 'Free To Play'
else concat('$', `gd`.`price`) end as `price`,
if(`gd`.`from_api`, 'true', 'false') as `from_api`
 from `Games_details` `gd`, `ct`
where `gd`.`game_id` = `ct`.`game_id`)
select * from details where details.game_name like '%jfldjal%';

-- and `gd`.`game_name` like '%des%';


-- and exists (
--     select 1 from `Game_tags` `gt`
--     where `gt`.`game_id` = `gd`.`game_id`
--     and `gt`.`tag` = "Racing"
-- );

select url from Game_media where type = 'screenshot' limit 1;