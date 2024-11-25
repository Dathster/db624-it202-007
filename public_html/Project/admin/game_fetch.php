<?php
    require(__DIR__ . "/../../../partials/nav.php");

    if (!has_role("Admin")) {
        flash("You don't have permission to view this page", "warning");
        die(header("Location: $BASE_PATH" . "/home.php"));
    }

    if(isset($_POST["action"]) && isset($_POST["game_name"])){
        $game_name = $_POST['game_name'];
        $result = fetch_searchResults($game_name);
        $displayResult = [];
        foreach($result as $record){
            $ele = [];
            $ele["id"] = se($record, "id", "", false);
            $ele["name"] = se($record,"name","",false);
            $displayResult[] = $ele;
        }
        //echo var_export($result);
        // echo var_export($displayResult);
    }

    if(isset($_POST["action2"]) && isset($_POST["game_id"])){
        $game_id = $_POST["game_id"];
        $result = fetch_gameDetails($game_id);

        $details= prep_gamesDetails($game_id, $result);
        $media= prep_gameMedia($game_id, $result);
        $tags= prep_gameTags($game_id, $result);
        $requirements= prep_gameRequirements($game_id, $result);

        // echo var_export($details, true);
        // echo "<br>";
        // echo var_export($media, true);
        // echo "<br>";
        // echo var_export($tags, true);
        // echo "<br>";
        // echo var_export($requirements, true);
        // echo "<br>";

        try{
            $a = insert('Games_details', $details);
            // echo "<br><br><br>";
            // echo var_export($a);

            $a = insert('Game_media', $media);
            // echo "<br><br><br>";
            // echo var_export($a);

            $a = insert('Game_tags', $tags);
            // echo "<br><br><br>";
            // echo var_export($a);

            $a = insert('Game_requirements', $requirements);
            // echo "<br><br><br>";
            // echo var_export($a);

            flash("Inserted records successfuly", "success");
        }catch (PDOException $e){
            // Check if the error is a duplicate entry error
            if ($e->getCode() == 23000) {
                flash("Duplicate entry detected: Please try a different game", "danger");
                error_log($e->getMessage(),true);
            } else {
                // Handle other PDO exceptions
                flash("A database error occured, please try again later","danger");
                echo $e->getMessage();
            }
        }catch (Exception $e){
            flash("An unknown error has occured, please try again later","danger");
            error_log($e->getMessage(),true);
        }
        
    }

    if(isset($_POST["action3"])){
        if(empty($_POST["id"])){
            flash("Game ID field must not be empty", "warning");
        }
        if(empty($_POST["name"])){
            flash("Name field must not be empty", "warning");
        }
        if(empty($_POST["price"])){
            flash("Price field must not be empty", "warning");
        }
        if(empty($_POST["release_date"])){
            flash("Release Date field must not be empty", "warning");
        }
        if(empty($_POST["dev_name"])){
            flash("Developer Name field must not be empty", "warning");
        }
        
        $id = se($_POST, "id", "", false);
        $name = se($_POST, "name", "", false);
        $price = se($_POST, "price", "", false);
        echo $_POST["release_date"];
        $release_date = $_POST["release_date"];
        // $release_date = "2022-03-04";
        $dev_name = se($_POST, "dev_name", "", false);
        $publisher_name = isset($_POST["publisher_name"])?se($_POST, "publisher_name", "", false):NULL;
        $franchise_name = isset($_POST["franchise_name"])?se($_POST, "franchise_name", "", false):NULL;
        $tags = isset($_POST["tags"])?se($_POST, "tags", "", false):NULL;
        $insert = True;
        $gamesDetailsdata= [];
        $gameTagsdata = [];

        if(strlen($name)>100){
            flash("Game name must be maximum 100 characters long", "warning");
            $insert = False;
        }
        if(strlen($dev_name)>100){
            flash("Developer name must be maximum 50 characters long", "warning");
            $insert = False;
        }
        if($publisher_name && strlen($publisher_name)>50){
            flash("Publisher name must be maximum 50 characters long", "warning");
            $insert = False;
        }
        if($franchise_name && strlen($franchise_name)>50){
            flash("Franchise name must be maximum 50 characters long", "warning");
            $insert = False;
        }
        if(!preg_match("/^\\$[0-9]+\.[0-9][0-9]$/", $price)){
            flash("Price must be in format \$dd.dd $price", "warning");
            $insert = False;
        }
        if(!preg_match("/^([^,\s]+,)*[^,\s]+$/", $tags)){
            flash("Tags should be seperated by commas and have no spaces after comma", "warning");
            $insert = False;
        }
        
        if(preg_match("/^\\$0+\.00$/", $price)){
            $price = "Free To Play";
        }

        if($tags){
            $tags = explode(",", $tags);
        }
        
        $data = [
            "game_id"=>strval($id),
            "game_name"=>$name,
            "price"=>$price,
            "release_date"=>$release_date,
            "developer_name"=>$dev_name,
            "publisher_name"=>$publisher_name,
            "franchise_name"=>$franchise_name,
            "from_api"=>0
        ];

        if($tags){
            foreach($tags as $tag){
                $gameTagsdata[] = ["game_id"=>$id, "tag"=>$tag];
            }
        }

        try{
            $a = insert('Games_details', $data);
            $a = insert('Game_tags', $gameTagsdata);

            flash("Inserted records successfuly", "success");
        }catch (PDOException $e){
            // Check if the error is a duplicate entry error
            if ($e->getCode() == 23000) {
                flash("Duplicate entry detected: Please try a different game id", "danger");
                echo error_log($e->getMessage(),true);
            } else {
                // Handle other PDO exceptions
                flash("A database error occured, please try again later","danger");
                echo $e->getMessage();
            }
        }catch (Exception $e){
            flash("An unknown error has occured, please try again later","danger");
            error_log($e->getMessage(),true);
            echo $e->getMessage();
        }
        
        


    }
?>

<div class="container-fluid">
    <h3>Create or Fetch Stock</h3>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="switchTab('fetch')">Create</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "search", "name" => "game_name", "placeholder" => "Game name"]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch_search"]); ?>
            <?php render_button(["text" => "Find game", "type" => "submit",]); ?>
        </form>
        <p></p>
        <form method="POST">
            <?php render_input(["type" => "number", "name" => "game_id", "placeholder" => "Game ID"]); ?>
            <?php render_input(["type" => "hidden", "name" => "action2", "value" => "fetch_details"]); ?>
            <?php render_button(["text" => "Get details", "type" => "submit",]); ?>
        </form>

        <?php if(isset($displayResult) && !empty($displayResult)) : ?>
            <hr>
            <h3>Search results</h3>
            <form>
                <?php 
                    render_table(["data"=>$displayResult]);
                    render_button(["text"=>"clear", "type"=>"submit"]);
                ?>
            </form>
            
        <?php endif; ?>
    </div>
    <div id="create" style="display: none;" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "number", "name" => "id", "label" => "Game ID", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "name", "label" => "Name", "rules" => ["required" => "required", "maxlength"=>100]]); ?>
            <?php render_input(["type" => "text", "name" => "price", "label" => "Price", "rules" => ["required" => "required", "pattern"=>"\\\$\d{1,}\.\d\d"]]); ?>
            <?php render_input(["type" => "date", "name" => "release_date", "label" => "Release Date", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "dev_name", "label" => "Developer Name", "rules" => ["required" => "required", "maxlength"=>50]]); ?>
            <?php render_input(["type" => "text", "name" => "publisher_name", "label" => "Publisher Name", "rules"=>["maxlength"=>50]]); ?>
            <?php render_input(["type" => "text", "name" => "franchise_name", "label" => "Franchise Name", "rules"=>["maxlength"=>50]]); ?>
            
            <?php render_input(["type" => "text", "name" => "tags", "label" => "Tags (do not include spaces after commas)", "rules" => ["pattern"=>"^([^,\s]+,)*[^,\s]+$"]]); ?>

            <?php render_input(["type" => "hidden", "name" => "action3", "value" => "create"]); ?>
            <?php render_button(["text" => "Search", "type" => "submit", "text" => "Create"]); ?>
        </form>
    </div>
</div>
<script>
    function switchTab(tab) {
        let target = document.getElementById(tab);
        if (target) {
            let eles = document.getElementsByClassName("tab-target");
            for (let ele of eles) {
                ele.style.display = (ele.id === tab) ? "none" : "block";
            }
        }
    }
</script>

<?php
    require_once(__DIR__ . "/../../../partials/flash.php");
?>
