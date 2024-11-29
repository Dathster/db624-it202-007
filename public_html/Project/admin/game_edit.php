<?php require(__DIR__ . "/../../../partials/nav.php");?>

<?php
    if (!has_role("Admin")) {
        flash("You don't have permission to view this page", "warning");
        die(header("Location: $BASE_PATH" . "/home.php"));
    }
    
    if (!isset($_GET["game_id"])) {
        flash("No Game ID provided", "warning");
        die(header("Location: $BASE_PATH" . "/home.php"));
    }

    $game_id = se($_GET, "game_id", -1, false);


    $query_games_details = "select * from `Games_details` where `game_id` = $game_id";
    $query_game_tags = "select `game_id`, group_concat(`tag`) as `tags` from `Game_tags` where `game_id` = $game_id group by `game_id`";
    $query_game_descriptions = "select * from `Game_descriptions` where `game_id` = $game_id";

    $results_games_details = select($query_games_details);
    $results_game_tags = select($query_game_tags);
    $results_game_descriptions = select($query_game_descriptions);

    $game_name = se($results_games_details[0], "game_name", "", false);
    $price = se($results_games_details[0], "price", "", false);
    $release_date = se($results_games_details[0], "release_date", "", false);
    $developer_name = se($results_games_details[0], "developer_name", "", false);
    $publisher_name = se($results_games_details[0], "publisher_name", "", false);
    $franchise_name = se($results_games_details[0], "franchise_name", "", false);

    $tags = (!isset($results_game_tags[0]))?"":se($results_game_tags[0], "tags", "", false);

    $description = se($results_game_descriptions[0], "description", "", false);
    $about = se($results_game_descriptions[0], "about", "", false);


    if(isset($_POST["action"])){

        if(empty($_POST["name"])){
            flash("Name field must not be empty", "warning");
            $insert = False;
        }
        if(empty($_POST["price"])){
            flash("Price field must not be empty", "warning");
            $insert = False;
        }
        if(empty($_POST["release_date"])){
            flash("Release Date field must not be empty", "warning");
            $insert = False;
        }
        if(empty($_POST["dev_name"])){
            flash("Developer Name field must not be empty", "warning");
            $insert = False;
        }
        
        $game_name = se($_POST, "name", "", false);
        $price = se($_POST, "price", "", false);
        $release_date = $_POST["release_date"];
        $developer_name = se($_POST, "dev_name", "", false);
        $publisher_name = isset($_POST["publisher_name"])?se($_POST, "publisher_name", "", false):NULL;
        $franchise_name = isset($_POST["franchise_name"])?se($_POST, "franchise_name", "", false):NULL;
        $tags = isset($_POST["tags"])?se($_POST, "tags", "", false):NULL;
        $description = se($_POST, "description", "", false);
        $about = se($_POST, "about", "", false);
        
        echo var_export($tags);

        $update = true;
        if(strlen($game_name)>100){
            flash("Game name must be maximum 100 characters long", "warning");
            $updat = False;
        }
        if(strlen($developer_name)>100){
            flash("Developer name must be maximum 50 characters long", "warning");
            $update = False;
        }
        if($publisher_name && strlen($publisher_name)>50){
            flash("Publisher name must be maximum 50 characters long", "warning");
            $update = False;
        }
        if($franchise_name && strlen($franchise_name)>50){
            flash("Franchise name must be maximum 50 characters long", "warning");
            $update = False;
        }
        if(!preg_match("/^[0-9]+\.[0-9][0-9]$/", $price)){
            flash("Price must be in format \$dd.dd", "warning");
            $update = False;
        }
        if($tags && !preg_match("/^([^,\s][^,]*,)*[^,\s][^,]*$/", $tags)){
            flash("Tags should be seperated by commas and have no spaces after comma", "warning");
            $update = False;
        }
        
        if(preg_match("/^\\$0+\.00$/", $price)){
            $price = "Free To Play";
        }

        if($tags){
            $tags_new = explode(",", $tags_new);
        }
        
        if(strlen($description)>1000){
            flash("Summary description must be maximum 1,000 characters long", "warning");
            $update = False;
        }

        if(strlen($about)>60000){
            flash("About game must be maximum 60,000 characters long", "warning");
            $update = False;
        }
        
        $gameTagsData = [];
        if($tags){
            foreach($tags_new as $tag){
                $gameTagsData[] = ["game_id"=>$game_id, "tag"=>$tag];
            }
        }

        $query_update_games_details = "
        update `Games_details` set `game_name` = '$game_name',
        `price` = $price,
        `release_date` = '$release_date',
        `developer_name` = '$developer_name',
        `publisher_name` = '$publisher_name',
        `franchise_name` = '$franchise_name'
        where `game_id` = $game_id";

        $query_drop_game_tags = "delete from `Game_tags` where `game_id` = $game_id";

        $query_update_game_descriptions = "update `Game_descriptions` set `description` = \"$description\",
        `about` = \"$about\" where `game_id` = $game_id";

        if($update){
            $db = getDB();
            $stmt1 = $db->prepare($query_update_games_details);
            $stmt2 = $db->prepare($query_drop_game_tags);
            $stmt3 = $db->prepare($query_update_game_descriptions);

            try {
                $stmt1->execute();
                    $stmt2->execute();
                    echo var_export($gameTagsData);
                    if(!empty($gameTagsData)){
                        insert('Game_tags', $gameTagsData);
                    }
                    
                $stmt3->execute();

                flash("Update successful", "success");
            
            } catch (PDOException $e) {
                error_log(var_export($e, true),true);
                flash("An unexpected database error occured", "danger");
                echo var_export($e, true);
            } catch(Exception $e){
                error_log(var_export($e, true),true);
                flash("An unexpected error occured", "danger");
                echo var_export($e, true);

            }
        }

        unset($_POST);
    }
?>

<div class='container-fluid'>
    <h1>Edit Game Details</h1>  

    <form method="POST">
        <?php render_input(["type" => "text", "name" => "name", "value"=>$game_name, "label" => "Name", "rules" => ["required" => "required", "maxlength"=>100]]); ?>
        <?php render_input(["type" => "text", "name" => "price", "value"=>$price, "label" => "Price", "rules" => ["required" => "required", "pattern"=>"\d{1,}\.\d\d"]]); ?>
        <?php render_input(["type" => "date", "name" => "release_date", "value"=>$release_date, "label" => "Release Date", "rules" => ["required" => "required"]]); ?>
        <?php render_input(["type" => "text", "name" => "dev_name", "value"=>$developer_name, "label" => "Developer Name", "rules" => ["required" => "required", "maxlength"=>50]]); ?>
        <?php render_input(["type" => "text", "name" => "publisher_name", "value"=>$publisher_name, "label" => "Publisher Name", "rules"=>["maxlength"=>50]]); ?>
        <?php render_input(["type" => "text", "name" => "franchise_name", "value"=>$franchise_name, "label" => "Franchise Name", "rules"=>["maxlength"=>50]]); ?>
        
        <?php render_input(["type" => "text", "name" => "tags", "value"=>se($tags, "", "", false), "label" => "Tags (do not include spaces after commas)", "rules" => ["pattern"=>"^([^,\s][^,]*,)*[^,\s][^,]*$"]]); ?>

        <?php render_input(["type" => "textarea", "name" => "description", "value"=>$description, "label" => "Summary Description", "rules"=>["maxlength"=>1000]]); ?>
        <?php render_input(["type" => "textarea", "name" => "about", "value"=>$about, "label" => "About Game", "rules"=>["maxlength"=>60000]]); ?>

        <?php render_input(["type" => "hidden", "name" => "action"]); ?>
        <?php render_button(["type" => "submit", "text" => "Update"]); ?>

    </form>
    
</div>


<?php
    require_once(__DIR__ . "/../../../partials/flash.php");
?>