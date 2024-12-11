<?php
    require(__DIR__ . "/../../lib/functions.php");

    session_start();

    //Check if the user is logged in and redirect otherwise
    is_logged_in($redirect = true);

    //Check if game id is set in url, else redirect to games view page 
    if((!isset($_GET["game_id"])) || empty($_GET["game_id"])){
        flash("Game ID must be provided", "warning");
        die(header("Location: $BASE_PATH" . "/games_view.php"));
    }

    //Obtain user ID from session and game ID from URL query parameter
    $user_id = se($_GET, "user_id", get_user_id(), false);
    $game_id = se($_GET, "game_id", -1, false);

    //Check if the game is saved or not (Assume not saved by default and try to save)
    $is_saved = se($_GET, "saved", 0, false);

    if($game_id > 0){
        //Try to insert record into Game_associations table linking user ID and game ID
        $db = getDB();
        try{
            if(!$is_saved){
                $query = "insert into `Game_associations` (`user_id`, `game_id`) values ($user_id, $game_id)"; 
                $stmt = $db->prepare($query);
                $stmt->execute();
                
                //Check if insert statement actually inserted records into the table
                if($stmt->rowCount()>0){
                    flash("Game saved to profile", "success");
                }else{
                    flash("Game not saved to profile due to an unknown error", "warning");
                }
            }else{
                $query = "delete from `Game_associations` where `user_id` = $user_id and `game_id` = $game_id"; 
                $stmt = $db->prepare($query);
                $stmt->execute();
                
                //Check if insert statement actually inserted records into the table
                if($stmt->rowCount()>0){
                    flash("Game removed from profile", "success");
                }else{
                    $t = var_export($_GET, true);
                    // flash($t)
                    flash("Game not removed from profile due to an unknown error", "warning");
                }
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
    unset($_GET["saved"]);
    unset($_GET["user_id"]);
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referrer = $_SERVER['HTTP_REFERER'];
        // Parse the URL to extract components
        $parsed_url = parse_url($referrer);
    
        // Reconstruct the base URL without query parameters
        $base_url = $parsed_url['scheme'] . "://" . $parsed_url['host'] . (isset($parsed_url['path']) ? $parsed_url['path'] : '');
    
        $loc = $referrer;
    } else {
        $loc = get_url("games_view.php")."?" . http_build_query($_GET);
    }
    
    error_log("Location: $loc");
    die(header("Location: $loc"));

?>