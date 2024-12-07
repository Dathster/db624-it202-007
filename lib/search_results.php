<?php 
    function returnSearchResults($search, $tag_search, $num_records, $order_column, $order, $api_filter, &$total_records, $offset=0){
        $query_games_details = 
            "with `ct` as (
            select `game_id`, group_concat(`tag` separator ', ') as `combined_tags` from `Game_tags`
            group by `game_id`
            )
            select 
            `gd`.`game_id`,
            `gd`.`game_name`,
            `gd`.`release_date`,
            `gd`.`developer_name`,
            `ct`.`combined_tags`,
            case when `gd`.`price` = 0.00 then 'Free To Play'
            else concat('$', `gd`.`price`) end as `price`,
            if(`gd`.`from_api`, 'true', 'false') as `from_api`
            from `Games_details` `gd` left join `ct` on `gd`.`game_id` = `ct`.`game_id` where 1";
        
            if($search){//db624 it202-007 11/28/24
                $query_games_details .= " and `gd`.`game_name` like '%$search%'";
            }
        
            if($api_filter != "Both"){
                $query_games_details .= " and `gd`.`from_api` = ";
                $query_games_details .= ($api_filter == "Manual")?"0":"1";
            }
        
            if($tag_search){
                $query_games_details .= " and exists (select 1 from `Game_tags` `gt` where `gd`.`game_id` = `gt`.`game_id` and `gt`.`tag` like '%$tag_search%')";
            }
            
            //Get total number results for pagination
            $total_records = potentialTotalRecords($query_games_details);

            $query_games_details .= " order by `gd`.`$order_column` $order limit $offset, $num_records";
        
            $results = [];
            $results_games_details = select($query_games_details);
        
            if($results_games_details){
                foreach($results_games_details as $record){
                    $game_id = $record["game_id"];
        
                    $query_game_media = "select `url` from `Game_media` where `type` = 'screenshot' and `game_id` = $game_id limit 1";
                    $results_game_media = select($query_game_media);
        
                    $game_media_arr = (empty($results_game_media))?[]:$results_game_media;
                    //db624 it202-007 11/28/24
                    $query_game_description="select `description` from `Game_descriptions` where `game_id` = $game_id";
                    $result_game_description=select($query_game_description);
                    $game_description = (empty($result_game_description))?"":se($result_game_description[0],'description', "", false);
                    
                   
                    
                    $single_result = [
                        "game_id"=>$game_id,
                        "game_name"=>$record["game_name"],
                        "price"=>$record["price"],
                        "release_date"=>$record["release_date"],
                        "developer_name"=>$record["developer_name"],
                        "from_api"=>$record["from_api"],
                        "combined_tags"=>$record["combined_tags"],
                        "screenshots"=>$game_media_arr,
                        "about"=>$game_description,
                        "view_url"=>get_url("single_game_view.php")
                    ]; 
                    if(is_logged_in()){
                         //Check if the game has been saved by the user by getting their user ID and game ID and checking game associations table
                        $user_id = get_user_id();
                        $query_game_associations = "select * from `Game_associations` where `user_id` = $user_id and `game_id` = $game_id";
                        $result_game_associations = select($query_game_associations);
                        $saved = (count($result_game_associations))?1:0;
                        $single_result["save_url"]=get_url("game_save.php");
                        $single_result["saved"]=$saved;
                        $single_result["query_string"]=se($_SERVER, "QUERY_STRING", "", false);
                    }
                    if(has_role("Admin")){
                        $single_result["edit_url"]=get_url("admin/game_edit.php");
                        $single_result["delete_url"]=get_url("admin/game_delete.php");
                        $single_result["query_string"]=se($_SERVER, "QUERY_STRING", "", false);
                    }
                    $results[] = $single_result;
                }
                    
                unset($record);
        
                
            }  
            return $results;
    }

    function potentialTotalRecords($query){
        return count(select($query));
    }
?>

