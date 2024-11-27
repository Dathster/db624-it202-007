<?php
    //note we need to go up 1 more directory
    require(__DIR__ . "/../../../partials/nav.php");

    if (!has_role("Admin")) {
        flash("You don't have permission to view this page", "warning");
        die(header("Location: $BASE_PATH" . "/home.php"));
    }



    // $query_games_details = "SELECT * FROM `Games_details` ORDER BY created DESC LIMIT 25";
    $query_games_details = "SELECT * FROM `Games_details`";
    $query_games_details = "with `combined_tags` as (
    select `gd`.`game_id`, `gd`.`game_name`, group_concat(`gt`.`tag`) as `tags` from `Games_details` `gd`, `Game_tags` `gt`
where `gd`.`game_id` = `gt`.`game_id`
group by `gd`.`game_id`
)
select `gd1`.`game_id`, `gd1`.`game_name`, `ct`.`tags`, `gd1`.`created`, `gd1`.`modified`, `gd1`.`from_api` from `Games_details` `gd1` left join `combined_tags` `ct`
on `gd1`.`game_id` = `ct`.`game_id`";
    
    $query_game_media = "with `joined_name_urls` as (
    select `gd`.`game_id`, `gd`.`game_name`, `gm`.`url`, `gm`.`type` from `Games_details` `gd`, `Game_media` `gm`
    where `gm`.`game_id` = `gd`.`game_id`
)
select * from `joined_name_urls` `jnu`";
    
    
    $query_game_requirements = "with `game_reqs` as (
select `gd`.`game_id`, `gd`.`game_name`, `gr`.`requirement_type`, `gr`.`os_version`, `gr`.`processor`, `gr`.`graphics`, `gr`.`memory`, `gr`.`storage`, `gr`.`created`, `gr`.`modified` from `Games_details` `gd`, `Game_requirements` `gr`
where `gd`.`game_id` = `gr`.`game_id`
)
select * from `game_reqs`";
    
    $results_games_details = [];
    $results_game_media = [];
    $results_game_requirements = [];

    $search = "";
    $num_records_details = (isset($_POST["limit_for_results"]) && !empty($_POST["limit_for_results"])
    && (int)$_POST["limit_for_results"] >=1 && (int)$_POST["limit_for_results"] <= 100)?$_POST["limit_for_results"]:10;

    $db = getDB();
    $desc_query = "ORDER BY `gd1`.`created` desc LIMIT $num_records_details";
    $game_id_shortlist = null;
    
    if(isset($_POST["game_search"])){
        $search = se($_POST,"game_search", "", false);
        $query_games_details .= " WHERE `gd1`.`game_name` LIKE \"%$search%\"";
    }

    $query_games_details .= $desc_query; 
    $stmt = $db->prepare($query_games_details);
    
    try {
        $stmt->execute();
        $r = $stmt->fetchAll();
        if ($r) {
            $results_games_details = $r;
        }
        // echo "result: " . var_export($r);
    } catch (PDOException $e) {
        error_log("Error fetching game details " . var_export($e, true));
        flash("Unhandled error occurred", "danger");
    }
    if($results_games_details){
        foreach ($results_games_details as $record){
            $game_id_shortlist[] = $record["game_id"];
        }
        unset($record);
        
        $game_id_shortlist = implode(",", $game_id_shortlist);

        // echo var_export($results_games_details);
        // echo "<br>";
        // echo $game_id_shortlist;
        // $in_query = "WHERE game_id in ($game_id_shortlist)";

        $query_game_media .= " where `jnu`.`game_id` in ($game_id_shortlist)";
        $query_game_requirements .= " where `game_reqs`.`game_id` in ($game_id_shortlist)";

        $query_game_media .= " order by `jnu`.`game_name` limit $num_records_details"; 
        $stmt = $db->prepare($query_game_media);
        try {
            $stmt->execute();
            $r = $stmt->fetchAll();
            if ($r) {
                $results_game_media = $r;
            }
        } catch (PDOException $e) {
            error_log("Error fetching game media " . var_export($e, true));
            flash("Unhandled error occurred", "danger");
        }

        $query_game_requirements .= "  order by `game_reqs`.`created` limit $num_records_details"; 
        $stmt = $db->prepare($query_game_requirements);
        try {
            $stmt->execute();
            $r = $stmt->fetchAll();
            if ($r) {
                $results_game_requirements = $r;
            }
        } catch (PDOException $e) {
            error_log("Error fetching game requirements " . var_export($e, true));
            flash("Unhandled error occurred", "danger");
        }
    }

    $games_details_table = ["data" => $results_games_details, "title" => "Game Details", "ignored_columns" => ["id","game_id"], "edit_url" => get_url("admin/games_edit.php")];
    $game_media_table = ["data" => $results_game_media, "title" => "Game Media", "ignored_columns" => ["id","game_id"], "edit_url" => get_url("admin/games_edit.php")];
    $game_requirements_table = ["data" => $results_game_requirements, "title" => "Game Requirements", "ignored_columns" => ["id","game_id"], "edit_url" => get_url("admin/games_edit.php")];


    


?>
<div class="container-fluid">
    <h3 class='mt-3 mb-3'>Steam Game Data</h3>
    <hr class='mt-3 mb-3'>
    <h3 class='mt-3 mb-3'>Filter Results</h3>
    <form method="POST">
        <?php render_input(["type" => "search", "label"=>"Search game by name", "name" => "game_search", "placeholder" => "Game name filer", "value"=>$search]);/*lazy value to check if form submitted, not ideal*/ ?>
        <?php render_input(["type" => "number", "label"=>"Maximum number of records to be returned", "name" => "limit_for_results", "placeholder" => "10", "value"=>$num_records_details, "rules"=>["min"=>1, "max"=>100]]);/*lazy value to check if form submitted, not ideal*/ ?>
        <?php //render_input(["name" => "order", "label" => "Order", "type" => "select", "options" => [["asc" => "asc"], ["desc" => "desc"]]]); ?>
        <?php render_button(["text" => "Search", "type" => "submit"]); ?>
    </form>
    <hr class='mt-3 mb-3'>
    <?php render_table($games_details_table); ?>
    <hr class='border-2'>
    <?php render_table($game_media_table); ?>
    <hr class='border-2'>
    <?php render_table($game_requirements_table); ?>    
</div>