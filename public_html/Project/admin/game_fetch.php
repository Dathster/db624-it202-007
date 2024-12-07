<?php
    require(__DIR__ . "/../../../partials/nav.php");

    if (!has_role("Admin")) {
        flash("You don't have permission to view this page", "warning");
        die(header("Location: $BASE_PATH" . "/home.php"));
    }

    if(isset($_POST["action"]) && isset($_POST["game_name"]) && !empty($_POST["game_name"])){
        $game_name = $_POST['game_name'];
        $result = fetch_searchResults($game_name);
        $displayResult = [];
        if(isset($result[0]["name"])){
            foreach($result as $record){
                $ele = [];
                $ele["id"] = se($record, "id", "", false);
                $ele["name"] = se($record,"name","",false);
                $displayResult[] = $ele;
            }

        }
    }

    if (isset($_POST["id"]) && !empty($_POST["id"])){
        $game_id = $_POST["id"];//db624 it202-007 11/28/24
        
        $result = fetch_gameDetails($game_id);

        $details= prep_gamesDetails($game_id, $result);
        $media= prep_gameMedia($game_id, $result);
        $tags= prep_gameTags($game_id, $result);
        $requirements= prep_gameRequirements($game_id, $result);
        $descriptions= prep_gameDescriptions($game_id, $result);

        // echo var_export($descriptions);

        $insert_rest = true;
        try{
            $a = insert('Games_details', $details);
        }catch (PDOException $e){
            // Check if the error is a duplicate entry error
            if ($e->getCode() == 23000) {
                flash("Duplicate entry detected: Please try a different game", "danger");
                error_log(var_export($e->getMessage(),true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");
            } else {
                // Handle other PDO exceptions
                flash("A database error occured, please try again later","danger");
                error_log(var_export($e->getMessage(),true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");
                // echo var_export($e->getMessage(),true);
                
            }
            $insert_rest = false;
        }catch (Exception $e){
            flash("An unknown error has occured, please try again later","danger");
            error_log(var_export($e->getMessage(),true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");
            $insert_rest = false;
        }

        if($insert_rest){ //db624 it202-007 11/28/24
            try{
                $a = insert('Game_media', $media);
    
                $a = insert('Game_tags', $tags);
    
                $a = insert('Game_requirements', $requirements);
    
                $a = insert('Game_descriptions', $descriptions);
    
                flash("Inserted records successfuly", "success");
            }catch (PDOException $e){
                // Check if the error is a duplicate entry error
                if ($e->getCode() == 23000) {
                    flash("Duplicate entry detected: Please try a different game", "danger");
                    error_log(var_export($e->getMessage(),true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");
                } else {
                    // Handle other PDO exceptions
                    flash("A database error occured, please try again later","danger");
                    error_log(var_export($e->getMessage(),true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");
                    // echo var_export($e->getMessage(),true);
                    
                }
            }catch (Exception $e){
                flash("An unknown error has occured, please try again later","danger");
                error_log(var_export($e->getMessage(),true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");
            }
        }
    }

    if(isset($_POST["action3"])){
        $insert = True;
        if(empty($_POST["game_id"])){ //db624 it202-007 11/28/24
            flash("Game ID field must not be empty", "warning");
            $insert = False;
        }
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
        
        if($insert){
            $id = se($_POST, "game_id", "", false);
            $name = se($_POST, "name", "", false);
            $price = se($_POST, "price", "", false);
            $release_date = $_POST["release_date"];
            $dev_name = se($_POST, "dev_name", "", false);
            $publisher_name = isset($_POST["publisher_name"])?se($_POST, "publisher_name", "", false):NULL;
            $franchise_name = isset($_POST["franchise_name"])?se($_POST, "franchise_name", "", false):NULL;
            $tags = isset($_POST["tags"])?se($_POST, "tags", "", false):NULL;
            $description = se($_POST, "description", "", false);
            $about = se($_POST, "about", "", false);

            $insert = True;
            $gamesDetailsdata= [];
            $gameTagsdata = [];
        
            if(!validate_numbers($id)   ){
                flash("Game ID must be a positive integer", "warning");
                $insert = False;
            }
            if(strlen($name)>100){  //db624 it202-007 11/28/24
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
            if(!validateDateFormat($release_date)){
                flash("Date must follow yyyy-mm-dd format", "warning");
                $insert = False;
            }
            if(!preg_match("/^[0-9]{1,7}\.[0-9][0-9]$/", $price)){
                flash("Price must be in format \$dd.dd and have maximum of nine digits", "warning");
                $insert = False;
            }
            if($tags && !preg_match("/^([^,\s][^,]*,)*[^,\s][^,]*$/", $tags)){
                flash("Tags should be seperated by commas and have no spaces after comma", "warning");
                $insert = False;
            }
            
            if(strlen($description)>1000){
                flash("Summary description must be maximum 1,000 characters long", "warning");
                $insert = False;
            }

            if(strlen($about)>60000){
                flash("About game must be maximum 60,000 characters long", "warning");
                $insert = False;
            }
            
            if(preg_match("/^\\$0+\.00$/", $price)){
                $price = "Free To Play";
            }
    
            if($tags){
                $tags = explode(",", $tags);
            }

            $data = [//db624 it202-007 11/28/24
                "game_id"=>$id,
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

            $gameDescriptionData[] = ["game_id"=>$id, "description"=>$description, "about"=>$about];
            $insert_rest = false;
            try{
                if($insert){//db624 it202-007 11/28/24
                    $a = insert('Games_details', $data);
                    if($tags){
                        $a = insert('Game_tags', $gameTagsdata);
                    }
                    $insert_rest=true;
                }
            }catch (PDOException $e){
                // Check if the error is a duplicate entry error
                if ($e->getCode() == 23000) {
                    flash("Duplicate entry detected: Please try a different game id or game name", "danger");
                    error_log(var_export($e->getMessage(),true));
                } else {
                    // Handle other PDO exceptions
                    flash("A database error occured, please try again later","danger");
                    error_log(var_export($e->getMessage(),true));
                }
            }catch (Exception $e){
                flash("An unknown error has occured, please try again later","danger");
                error_log($e->getMessage(),true);
            }

            if($insert_rest){
                try{//db624 it202-007 11/28/24
                    if($insert){
                        if($tags){
                            $a = insert('Game_tags', $gameTagsdata);
                        }
                        
                        $a = insert('Game_descriptions', $gameDescriptionData);
    
                        flash("Inserted records successfuly", "success");
                    }
                }catch (PDOException $e){
                    // Check if the error is a duplicate entry error
                    if ($e->getCode() == 23000) {
                        flash("Duplicate entry detected: Please try a different game id or game name", "danger");
                        error_log(var_export($e->getMessage(),true));
                    } else {
                        // Handle other PDO exceptions
                        flash("A database error occured, please try again later","danger");
                        error_log(var_export($e->getMessage(),true));
                    }
                }catch (Exception $e){
                    flash("An unknown error has occured, please try again later","danger");
                    error_log($e->getMessage(),true);
                }
            }
        }

        
        
    }
?>

<div class="container-fluid">
    <h3>Create or Fetch Game</h3>
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="switcher nav-link active" href="#" onclick="switchTab('create')">Fetch</a>
        </li>
        <li class="nav-item">
            <a class="switcher nav-link" href="#" onclick="switchTab('fetch')">Create</a>
        </li>
    </ul>
    <div id="fetch" class="tab-target">
        <form method="POST" > <!-- db624 it202-007 11/28/24 -->
            <?php render_input(["type" => "search", "name" => "game_name", "placeholder" => "Game name"]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch_search"]); ?>
            <?php render_button(["text" => "Find game", "type" => "submit",]); ?>
        </form>

        <?php if(isset($displayResult)) : ?>
            <hr>
            <h3>Search results</h3>
            <form method="POST">
                <?php 
                    render_table(["data"=>$displayResult, "post_self_form"=>["name"=>"id", "label"=>"Insert", "classes"=>"btn btn-secondary"]]);
                ?>
            </form>

            <form>
                <?php render_button(["text"=>"clear", "type"=>"submit"]); ?>
            </form>
            
        <?php endif; ?>
    </div>
    <div id="create" style="display: none;" class="tab-target"> <!-- db624 it202-007 11/28/24 -->
        <form method="POST" onsubmit="return validate(this);">
            <?php render_input(["type" => "number", "name" => "game_id", "label" => "Game ID", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "name", "label" => "Name", "rules" => ["required" => "required", "maxlength"=>100]]); ?>
            <?php render_input(["type" => "text", "name" => "price", "label" => "Price", "rules" => ["required" => "required", "pattern"=>"\d{1,}\.\d\d"]]); ?>
            <?php render_input(["type" => "date", "name" => "release_date", "label" => "Release Date", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "dev_name", "label" => "Developer Name", "rules" => ["required" => "required", "maxlength"=>50]]); ?>
            <?php render_input(["type" => "text", "name" => "publisher_name", "label" => "Publisher Name", "rules"=>["maxlength"=>50]]); ?>
            <?php render_input(["type" => "text", "name" => "franchise_name", "label" => "Franchise Name", "rules"=>["maxlength"=>50]]); ?>
            
            <?php render_input(["type" => "text", "name" => "tags", "label" => "Tags (do not include spaces after commas)", "rules" => ["pattern"=>"^([^,\s][^,]*,)*[^,\s][^,]*$"]]); ?>

            <?php render_input(["type" => "textarea", "name" => "description", "label" => "Summary Description", "rules"=>["maxlength"=>1000]]); ?>
            <?php render_input(["type" => "textarea", "name" => "about", "label" => "About Game", "rules"=>["maxlength"=>60000]]); ?>

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
            let navs = document.getElementsByClassName("switcher");
		    for(let nav of navs) {
			nav.classList.remove("active");
		}
		event.target.classList.add("active");
        }
        document.querySelector("form").reset();
    }
</script>

<script>
    function validate(form) {
        let game_id = form.game_id.value; //db624 it202-007 11/28/24
        let name = form.name.value;
        let price = form.price.value;
        let release_date = form.release_date.value;
        let developer_name = form.dev_name.value;
        let publisher_name = form.publisher_name.value;
        let franchise_name = form.franchise_name.value;
        let tags = form.tags.value;
        let description = form.description.value;
        let about = form.about.value;
        let isValid = true;

        let gameIdPattern = /^\d+$/;
        let pricePattern = /^\d{1,7}\.\d\d$/;
        let datePattern = /^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/;
        let tagsPattern = /^([^,\s][^,]*,)*[^,\s][^,]*$/;

        if(!game_id){
            flash("[Client]: Game ID cannot be empty", "warning");
            isValid = false;
        }
        if(!name){
            flash("[Client]: Game Name cannot be empty", "warning");
            isValid = false;
        }
        if(!price){
            flash("[Client]: Price cannot be empty", "warning");
            isValid = false;
        }
        if(!release_date){
            flash("[Client]: Release date cannot be empty", "warning");
            isValid = false;
        }
        if(!developer_name){
            flash("[Client]: Developer name cannot be empty", "warning");
            isValid = false;
        }

        if(name.length > 100){
            flash("[Client]: Game name must be at most 100 characters long", "warning");
            isValid = false;
        }
        if(developer_name.length > 50){ //db624 it202-007 11/28/24
            flash("[Client]: Developer name must be at most 100 characters long", "warning");
            isValid = false;
        }
        if(publisher_name && publisher_name.length > 50){
            flash("[Client]: Publisher name must be at most 100 characters long", "warning");
            isValid = false;
        }
        if(franchise_name.length && franchise_name.length> 50){
            flash("[Client]: Franchise name must be at most 100 characters long", "warning");
            isValid = false;
        }
        if(description.length && description.length> 1000){
            flash("[Client]: Description must be at most 1,000 characters long", "warning");
            isValid = false;
        }
        if(about.length && about.length> 60000){
            flash("[Client]: About field must be at most 60,0000 characters long", "warning");
            isValid = false;
        }

        if(game_id && !gameIdPattern.test(game_id)){
            flash("[Client]: Game ID must be a positive integer", "warning");
            isValid = false;
        }
        if(price && !pricePattern.test(price)){
            flash("[Client]: Price must be in d.dd format, at most 9 digits long, and be positive", "warning");
            isValid = false;
        }
        if(release_date && !datePattern.test(release_date)){
            flash("[Client]: Date must be in form yyyy-mm-dd", "warning");
            isValid = false;
        }
        if(tags && !tagsPattern.test(tags)){
            flash("[Client]: Tags must be comma seperated with no space after comma", "warning");
            isValid = false;
        }

        return isValid;
    }
</script>


<?php
    require_once(__DIR__ . "/../../../partials/flash.php");
?>
