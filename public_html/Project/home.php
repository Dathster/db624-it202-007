<?php
require(__DIR__ . "/../../partials/nav.php");
?>


<?php

if (is_logged_in(true)) {
    //comment this out if you don't want to see the session variables
    error_log("Session data: " . var_export($_SESSION, true));
}
?>


<?php
    $user_id = se($_GET, "user_id", get_user_id(), false);
    if(empty($user_id)){
        $user_id = get_user_id();
    }
    
    if(isset($_GET["remove_all"])){//db624 it202 12/11/24
        $query_remove_all_game_associations = "delete from `Game_associations` where `user_id` = $user_id";
        $db = getDB();
        $stmt = $db->prepare($query_remove_all_game_associations);
        try{
            $stmt->execute();
            if($stmt->rowCount()>0){
                flash("All games removed successfully", "success");
            }else{
                flash("Games were not able to be removed from account", "warning");
            }
        }catch (PDOException $e){
            flash("A database error occured, please try again later", "danger");
            error_log(var_export($e, true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");
        }catch (Exception $e){
            flash("An unknown error has occured", "danger");
            error_log(var_export($e, true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");
        }
        unset($_GET["remove_all"]);
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
    $results = returnSearchResults($search, $tag_search, $num_records, $order_column, $order, $api_filter, $total, $offset, "saved", $user_id);
    
    //echo $total;
    //db624 it202 12/11/24
    $query_num_saved_games = "select count(*) as `ct` from `Game_associations` where `user_id`=$user_id";
    $num_saved_games = exec_query($query_num_saved_games)[0]["ct"];
?>

<div class='container-fluid'>
    <h1><?php echo get_username($user_id) ?>'s games</h1>  
</div>

<div class="container-fluid">
    <h3 class='mt-3 mb-3'>There are a total of <?php echo $num_saved_games; ?> games saved</h3>
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
                <?php render_input(["type"=>"hidden", "name"=>"user_id", "value"=>$user_id]); ?>
                <?php render_button(["text" => "Filter", "type" => "submit"]); ?>
            </div>
        </div>
    </form>
    
    <div class="col-3">
        <form method="GET">
            <?php render_input(["type"=>"hidden", "name"=>"reset", "value"=>"1"]); ?>
            <?php render_input(["type"=>"hidden", "name"=>"user_id", "value"=>$user_id]); ?>
            <?php render_button(["text" => "Reset", "type" => "submit"]); ?>
        </form>
    </div>
    <div class="col-3">
        <form method="GET"> <!-- db624 it202 12/11/24 -->
            <?php render_input(["type"=>"hidden", "name"=>"remove_all", "value"=>"1"]); ?>
            <?php render_button(["text" => "Remove all", "type" => "submit", "color"=>"danger"]); ?>
        </form>
    </div>
    
    <h4>Number of games meeting search criteria: <?php echo $total ?></h4>
    <hr class='mt-3 mb-3'>
    <?php if(empty($results) && $num_saved_games):?>
        <?php flash("No games were found meeting search criteria", "warning"); ?>
    <?php endif ?>
    <div class="row d-flex">
            <?php foreach ($results as $games): ?>
                <div class="col-3" style='flex: 1 1 calc(33.333% - 1rem); max-width: calc(33.333% - 1rem);'>
                    <?php render_card($games); ?> <!-- db624 it202-007 11/28/24 -->
                </div>
            <?php endforeach; ?>
    </div>
</div>

<?php
require_once(__DIR__ . "/../../partials/pagination_nav.php");
?> 

<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>  
