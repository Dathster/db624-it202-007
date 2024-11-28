<?php
    //note we need to go up 1 more directory
    require(__DIR__ . "/../../../partials/nav.php");

    if (!has_role("Admin")) {
        flash("You don't have permission to view this page", "warning");
        die(header("Location: $BASE_PATH" . "/home.php"));
    }

    if(isset($_POST["reset"])){
        $_POST = [];
    }

    $search = (isset($_POST["game_search"]))?se($_POST,"game_search","",false):NULL;
    $tag_search = (isset($_POST["tag_filter"]))?se($_POST,"tag_filter","",false):NULL;
    $num_records = (isset($_POST["num_records"]))?$_POST["num_records"]:10;
    $order_column = (isset($_POST["order_columns"]))?$_POST["order_columns"]:"game_name";
    $order = (isset($_POST["order"]))?$_POST["order"]:"asc";
    $api_filter = (isset($_POST["api_filter"]))?$_POST["api_filter"]:"Both";

    // echo $search . "<br>" . $tag_search . "<br>" . $num_records . "<br>" . $order_column . "<br>" . $order . "<br>"; 
    $query_games_details = "with `ct` as (
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
 from `Games_details` `gd`, `ct`
where `gd`.`game_id` = `ct`.`game_id`";

    if($search){
        $query_games_details .= " and `gd`.`game_name` like '%$search%'";
    }

    if($api_filter != "Both"){
        $query_games_details .= " and `gd`.`from_api` = ";
        $query_games_details .= ($api_filter == "Manual")?"0":"1";
    }

    if($tag_search){
        $query_games_details .= " and exists (select 1 from `Game_tags` `gt` where `gd`.`game_id` = `gt`.`game_id` and `gt`.`tag` like '%$tag_search%')";
    }

    $query_games_details .= " order by `gd`.`$order_column` $order limit $num_records";

    $results = [];
    $results_games_details = select($query_games_details);
    //echo var_export($query_games_details) . "<br>";
    //echo var_export($results_games_details);

    if($results_games_details){
        foreach($results_games_details as $record){
            // echo var_export($record) . "<br><br>";
            $game_id = $record["game_id"];

            $query_game_media = "select `url` from `Game_media` where `type` = 'screenshot' and `game_id` = $game_id limit 1";
            $results_game_media = select($query_game_media);

            $game_media_arr = (empty($results_game_media))?[]:$results_game_media;

            $results[] = [
                                "game_id"=>$game_id,
                                "game_name"=>$record["game_name"],
                                "price"=>$record["price"],
                                "release_date"=>$record["release_date"],
                                "developer_name"=>$record["developer_name"],
                                "from_api"=>$record["from_api"],
                                "combined_tags"=>$record["combined_tags"],
                                "screenshots"=>$game_media_arr,
                                "view_url"=>get_url("admin/single_game_view.php"),
                                "edit_url"=>get_url("admin/game_edit.php"),
                                "delete_url"=>get_url("admin/game_delete.php")
                            ];
        }
        unset($record);
    }  
?>
<div class="container-fluid">
    <h3 class='mt-3 mb-3'>Steam Game Data</h3>
    <hr class='mt-3 mb-3'>
    <h3 class='mt-3 mb-3'>Filter Results</h3>
    <form method="POST">
        <div class="row">
            <div class="col-3">
            <?php render_input(["type" => "search", "label"=>"Search game by name", "name" => "game_search", "placeholder" => "Game name filer", "value"=>$search]);/*lazy value to check if form submitted, not ideal*/ ?>
            </div>

            <div class="col-3">
                <?php render_input(["type" => "search", "label"=>"Filter by tag", "name" => "tag_filter", "value"=>$tag_search]);/*lazy value to check if form submitted, not ideal*/ ?>    
            </div>

            <div class="col-2">
            <?php render_input(["type"=>"select", "name"=>"order_columns", "label"=>"Sort Game Details data by", "options"=>["game_name", "price", "release_date", "developer_name"], "selected"=>$order_column]); ?>
            </div>

            <div class="col-2">
                <?php render_input(["type"=>"select", "name"=>"order", "label"=>"Order data", "options"=>["asc", "desc"], "selected"=>$order]); ?>
            </div>
            
            <div class="col-1">
            <?php render_input(["type"=>"number", "name"=>"num_records", "label"=>"Limit", "value"=>$num_records, "rules"=>["min"=>1, "max"=>100]]); ?>
            </div>

            <div class="col-2">
                <?php render_input(["type"=>"select", "name"=>"api_filter", "label"=>"Display manually inserted or api data", "options"=>["Both", "Api", "Manual"], "selected"=>$api_filter]); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-1">
                <?php render_button(["text" => "Filter", "type" => "submit"]); ?>
            </div>
        </div>
    </form>
    <div class="col-1">
        <form method="POST">
            <?php render_input(["type"=>"hidden", "name"=>"reset", "value"=>"1"]); ?>
            <?php render_button(["text" => "Reset", "type" => "submit"]); ?>
        </form>
        
    </div>
    
    <hr class='mt-3 mb-3'>
    
    <?php if(empty($results)):?>
        <?php flash("No games were found meeting search criteria", "warning"); ?>
    <?php endif ?>
    <div class="row">
            <?php foreach ($results as $games): ?>
                <div class="col-3">
                    <?php render_card($games); ?>
                </div>
            <?php endforeach; ?>
    </div>
</div>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>  