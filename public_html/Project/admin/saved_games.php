<?php 
    require(__DIR__ . "/../../../partials/nav.php");
?>

<?php 
    if(isset($_GET["reset"])){
        $_GET = [];
    }
    unset($_GET["reset"]);

    $query_total_saved_games = 'select distinct `game_id` from `Game_associations`';
    $total_saved_games = potentialTotalRecords($query_total_saved_games);

    $num_records = se($_GET, "num_records", 10, false);
    $order = se($_GET,"order", "asc", false);
    $user_filter = (isset($_GET["user_filter"]))?se($_GET,"user_filter","",false):"";

    $total = 0;
    $page = (isset($_GET["page"]))?$_GET["page"]-1:0;


    $query_user_filter = "select `username`, `id` from `Users` where `username` like '%$user_filter%'";
    $total = potentialTotalRecords($query_user_filter);
    $offset=$page * $num_records;
    $query_user_filter .=  " order by `username` $order limit $offset, $num_records";
    $result_user_filter = exec_query($query_user_filter);
    $results = [];

    foreach($result_user_filter as $user){
        $user_id = $user["id"];

        $query_user_saved_games = "select `gd`.`game_name`, `gd`.`game_id` from `Games_details` `gd`
                                    where `gd`.`game_id` in (select `ga`.`game_id` from `Game_associations` `ga`
                                                            where `ga`.`user_id` = $user_id)";
        $result_user_saved_games = exec_query($query_user_saved_games);
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
                <?php render_input(["type"=>"select", "name"=>"order", "label"=>"Order users", "options"=>["asc", "desc"], "selected"=>$order]); ?>
            </div>
            <div class="col-1">
            <?php render_input(["type"=>"number", "name"=>"num_records", "label"=>"Limit", "value"=>$num_records, "rules"=>["min"=>1, "max"=>100]]); ?>
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

    <hr class='mt-3 mb-3'>
    <h4>Number of users meeting search criteria: <?php echo $total ?></h4>    
    <div class='row ms-3 me-3 d-flex align-items-start'>

        <?php foreach($results as $result): ?>

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
