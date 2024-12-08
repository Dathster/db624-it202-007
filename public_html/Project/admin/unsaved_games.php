<?php
require(__DIR__ . "/../../../partials/nav.php");
?>
<div class='container-fluid'>
    <h1>Unsaved Games</h1>  
</div>

<?php

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
?>


<?php

    $user_id = get_user_id();

    if(isset($_GET["reset"])){
        $_GET = [];
    }

    $search = (isset($_GET["game_search"]))?se($_GET,"game_search","",false):NULL;
    $tag_search = (isset($_GET["tag_filter"]))?se($_GET,"tag_filter","",false):NULL;
    $num_records = (isset($_GET["num_records"]) && !empty($_GET["num_records"]))?$_GET["num_records"]:10;
    $order_column = (isset($_GET["order_columns"]))?$_GET["order_columns"]:"game_name";
    $order = (isset($_GET["order"]))?$_GET["order"]:"asc";
    $api_filter = (isset($_GET["api_filter"]))?$_GET["api_filter"]:"Both";
    $page = (isset($_GET["page"]))?$_GET["page"]-1:0;
    
    $total=0;
    $offset=$page * $num_records;
    //echo $offset . " " . $num_records;
    $results = returnSearchResults($search, $tag_search, $num_records, $order_column, $order, $api_filter, $total, $offset, "unsaved");
    //echo $total;

    $query_num_saved_games = "select count(`game_id`) as `ct` from `Games_details` where `game_id` not in (select `ga`.`game_id` from `Game_associations` `ga`)";
    $num_saved_games = select($query_num_saved_games)[0]["ct"];
?>
<div class="container-fluid">
    <h3 class='mt-3 mb-3'>There are <?php echo $num_saved_games; ?> games not saved by any user</h3>
    <hr class='mt-3 mb-3'>
    <h3 class='mt-3 mb-3'>Filter Results</h3>
    <form method="GET">
        <div class="row"> <!-- db624 it202-007 11/28/24 -->
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
    
    <div class="col-3">
        <form method="GET">
            <?php render_input(["type"=>"hidden", "name"=>"reset", "value"=>"1"]); ?>
            <?php render_button(["text" => "Reset", "type" => "submit"]); ?>
        </form>
    </div>
    
    <h4>Number of games meeting search criteria: <?php echo $total ?></h4>
    <hr class='mt-3 mb-3'>

    <?php if(empty($results)):?>
        <?php flash("No games were found meeting search criteria", "warning"); ?>
    <?php endif ?>
    <div class="row">
            <?php foreach ($results as $games): ?>
                <div class="col-3">
                    <?php render_card($games); ?> <!-- db624 it202-007 11/28/24 -->
                </div>
            <?php endforeach; ?>
    </div>
</div>

<?php
require_once(__DIR__ . "/../../../partials/pagination_nav.php");
?> 

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>  
