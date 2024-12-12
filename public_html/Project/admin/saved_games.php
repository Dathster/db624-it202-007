<?php 
    require(__DIR__ . "/../../../partials/nav.php");
?>

<?php 
    if (!has_role("Admin")) {
        flash("You don't have permission to view this page", "warning");
        die(header("Location: $BASE_PATH" . "/home.php"));
    }

    if(isset($_GET["reset"])){
        $_GET = [];
    }
    unset($_GET["reset"]);

    $query_total_saved_games = 'select distinct `game_id` from `Game_associations`';
    $total_saved_games = potentialTotalRecords($query_total_saved_games);

    $num_records = se($_GET, "num_records", 10, false);
    $order_users = se($_GET,"order_users", "asc", false);
    $user_filter = (isset($_GET["user_filter"]))?se($_GET,"user_filter","",false):"";

    $total = 0;
    $page = (isset($_GET["page"]))?$_GET["page"]-1:0;


    $query_user_filter = "select `username`, `id` from `Users` where `username` like '%$user_filter%'";
    $total = potentialTotalRecords($query_user_filter);
    $offset=$page * $num_records;
    $query_user_filter .=  " order by `username` $order_users limit $offset, $num_records";
    $result_user_filter = exec_query($query_user_filter);
    $results = [];

    $game_search = (isset($_GET["game_search"]))?se($_GET,"game_search","",false):NULL;
    $tag_search = (isset($_GET["tag_filter"]))?se($_GET,"tag_filter","",false):NULL;
    $num_games = (isset($_GET["num_games"]) && !empty($_GET["num_games"]))?$_GET["num_games"]:10;
    $order_column = (isset($_GET["order_columns"]))?$_GET["order_columns"]:"game_name";
    $order_games = (isset($_GET["order_games"]))?$_GET["order_games"]:"asc";
    $api_filter = (isset($_GET["api_filter"]))?$_GET["api_filter"]:"Both";

    if(isset($_GET["remove_all"])){
        $user_ids = [];
        foreach($result_user_filter as $user){
            $user_ids[] = $user["id"];
        }
        $users_string = implode(",", $user_ids);

        $query_remove_games = "delete from `Game_associations` `ga` where `ga`.`user_id` in ($users_string)";
        $db = getDB();
        $stmt = $db->prepare($query_remove_games);
        try{
            $stmt->execute();
            if($stmt->rowCount()>0){
                flash("All games removed successfully", "success");
            }else{
                flash("Removing games from selected users unsuccessful", "warning");
                error_log(var_export($stmt));
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



    foreach($result_user_filter as $user){
        $user_id = $user["id"];

        // $query_user_saved_games = "select `gd`.`game_name`, `gd`.`game_id` from `Games_details` `gd`
        //                             where `gd`.`game_id` in (select `ga`.`game_id` from `Game_associations` `ga`
        //                                                     where `ga`.`user_id` = $user_id)";
        // $result_user_saved_games = exec_query($query_user_saved_games);
        $num_saved = 0;
        $result_user_saved_games = returnSearchResults($game_search, $tag_search, $num_games, $order_column, $order_games, $api_filter, $num_saved, 0, "saved", $user_id);
        $user["saved_games"] = $result_user_saved_games;
        $results[] = $user;
    }

    $_query_string=se($_SERVER, "QUERY_STRING", "", false);

    $_view_url = get_url("single_game_view.php");
    $_remove_url = get_url("game_save.php");
    
    $_view_label = "View";
    $_remove_label = "Remove";
    
    $_view_classes = "btn btn-primary btn-sm fs-7";
    $_remove_classes = "btn btn-danger btn-sm fs-7";

    
    
?>

<div class='container-fluid mt-3'>
    <h1 class='h1'>All Games Saved By Users</h1>
    <h2 class='h2'>There are <?php echo $total_saved_games ?> games saved by all users</h2>
    <hr class='mt-3 mb-3'>
    <h3 class='h3'>Filter Results</h3>

    <form method='GET'>
        <div class='row'>
            <div class='col-3'>
                <?php render_input(["type"=>"search", "label"=>"Search users", "name"=>"user_filter", "value"=>$user_filter]) ?>
            </div>
            <div class="col-2">
                <?php render_input(["type"=>"select", "name"=>"order_users", "label"=>"Order users", "options"=>["asc", "desc"], "selected"=>$order_users]); ?>
            </div>
            <div class="col-1">
            <?php render_input(["type"=>"number", "name"=>"num_records", "label"=>"Limit", "value"=>$num_records, "rules"=>["min"=>1, "max"=>100]]); ?>
            </div>


            <div class="row"> <!-- db624 it202-007 11/28/24 -->
            <div class="col-3">
            <?php render_input(["type" => "search", "label"=>"Search game by name", "name" => "game_search", "value"=>$game_search]);/*lazy value to check if form submitted, not ideal*/ ?>
            </div>

            <div class="col-3">
                <?php render_input(["type" => "search", "label"=>"Filter by tag", "name" => "tag_filter", "value"=>$tag_search]);/*lazy value to check if form submitted, not ideal*/ ?>    
            </div>

            <div class="col-2">
            <?php render_input(["type"=>"select", "name"=>"order_columns", "label"=>"Sort Game Details data by", "options"=>["game_name", "price", "release_date", "developer_name"], "selected"=>$order_column]); ?>
            </div>

            <div class="col-2">
                <?php render_input(["type"=>"select", "name"=>"order_games", "label"=>"Order games", "options"=>["asc", "desc"], "selected"=>$order_games]); ?>
            </div>
            
            <div class="col-1">
            <?php render_input(["type"=>"number", "name"=>"num_games", "label"=>"Limit", "value"=>$num_games, "rules"=>["min"=>1, "max"=>100]]); ?>
            </div>

            <div class="col-2">
                <?php render_input(["type"=>"select", "name"=>"api_filter", "label"=>"Display manually inserted or api data", "options"=>["Both", "Api", "Manual"], "selected"=>$api_filter]); ?>
            </div>
        </div>
        
    </div>

        <div class="row">
            <div class="col">
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
    <div class="col-3">
        <form method="GET">
            <?php render_input(["type"=>"hidden", "name"=>"remove_all", "value"=>"1"]); ?>
            <?php render_button(["text" => "Remove all", "type" => "submit", "color"=>"danger"]); ?>
        </form>
    </div>

    <hr class='mt-3 mb-3'>
    <h4>Number of users meeting search criteria: <?php echo $total ?></h4>    
    <div class='row ms-3 me-3 d-flex align-items-start'>

        <?php foreach($results as $result): ?>
            <?php if(empty($result["saved_games"])): ?>
            <?php continue; ?>
            <?php endif ?>
            <div class="card col-3 d-flex flex-column" style='flex: 1 1 calc(33.333% - 1rem); max-width: calc(33.333% - 1rem);'>
                <div class="card-body">
                    <h5 class="card-title"><a href=<?php echo get_url("home.php") . "?" . "user_id=" . $result["id"]?> class="link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover"><?php echo $result["username"]; ?></a></h5>
                    <hr>


                    <?php if(!empty($result["saved_games"])): ?>
                    

                        <p class='ms-3'>Saved games:</p>
                        <ul class="list-group list-group-flush bordered-list" style="max-height: 200px; overflow-y: auto;">
                            <?php foreach($result["saved_games"] as $game): ?>
                                <li class="list-group-item">
                                    <?php 
                                        $user_id = $result["id"]; 
                                        $game_id = $game["game_id"];
                                        $game_name = $game["game_name"];
                                    ?>
                                    <?php echo $game_name; ?>
                                    <div class='col'>
                                    <a href="<?php echo $_view_url; ?>?<?php echo "game_id"; ?>=<?php echo $game_id; ?>" class="<?php se($_view_classes); ?>"><?php se($_view_label); ?></a>
                                    <a href="<?php echo $_remove_url; ?>?<?php echo "game_id"; ?>=<?php echo $game_id . "&saved=1&user_id=$user_id&$_query_string"; ?>" class="<?php echo $_remove_classes ?>"><?php echo $_remove_label ?></a>
                                    </div>
                                </li>
                            <?php endforeach ?>
                        </ul>
                        
                    <?php else: ?>
                        <p> No games have been saved by this user </p>
                    <?php endif ?>





                </div>
                
                
                
            
            </div>

        <?php endforeach ?>

    </div>
    
</div>

<?php
    require_once(__DIR__ . "/../../../partials/flash.php");
?>  

<?php
require_once(__DIR__ . "/../../../partials/pagination_nav.php");
?> 
