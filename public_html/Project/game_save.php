<?php
    require(__DIR__ . "/../../../lib/functions.php");

    session_start();

    //Check if the user is logged in and redirect otherwise
    is_logged_in($redirect = true);

    //Check if game id is set in url, else redirect to games view page 
    if((!isset($_GET["game_id"])) || empty($_GET["game_id"])){
        flash("Game ID must be provided", "warning");
        die(header("Location: $BASE_PATH" . "/games_view.php"));
    }

    //Obtain user ID from session and game ID from URL query parameter
    $user_id = get_user_id();
    $game_id = se($_GET, "game_id", -1, false);

    if($game_id > 0){
        //Try to insert record into Game_associations table linking user ID and game ID
        $db = getDB();
        try{
            $query = "insert into `Game_associations` (`user_id`, `game_id`) values ($user_id, $game_id)"; 
            $stmt = $db->prepare($query);
            $stmt->execute();
            
            //Check if insert statement actually inserted records into the table
            if($stmt->rowCount()>0){
                flash("Game saved to profile", "success");
            }else{
                flash("Game not saved to profile due to an unknown error", "warning");
            }
        }catch (PDOException $e){
            if($e->errorInfo[1] === 1062){
                flash("The game has already been saved to the user's profile", "warning");
            }else{
                flash("An unknown database error has occured", "danger");
            }
            error_log(var_export($e, true));
        }catch (Exception $e){
            flash("An unknown error has occured", "danger");
            error_log(var_export($e, true));
        }
    }else{
        //Flash message if given game ID is negative (invalid value)
        flash("Error: Invalid game ID", "danger");
    }

    unset($_GET["game_id"]);
    $loc = get_url("games_view.php")."?" . http_build_query($_GET);
    error_log("Location: $loc");
    die(header("Location: $loc"));

?>