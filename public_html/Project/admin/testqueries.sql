with combined_tags as (
    select gd.game_id, gd.game_name, group_concat(gt.tag) as tags from Games_details gd, Game_tags gt
where gd.game_id = gt.game_id
group by gd.game_id
)
select gd1.game_id, gd1.game_name, ct.tags, gd1.created, gd1.modified, gd1.from_api from Games_details gd1 left join combined_tags ct
on gd1.game_id = ct.game_id;


select gd.game_name, gr.requirement_type, gr.os_version, gr.processor, gr.graphics, gr.memory, gr.storage from Games_details gd, Game_requirements gr
where gd.game_id = gr.game_id;