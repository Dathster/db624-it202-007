<?php
    require(__DIR__ . "/../../../lib/functions.php");

    session_start();
    //db624 it202-007 11/28/24
    if (!has_role("Admin")) {
        flash("You don't have permission to view this page", "warning");
        die(header("Location: $BASE_PATH" . "/home.php"));
    }

    if (!isset($_GET["game_id"])) {
        flash("No Game ID provided", "warning");
        die(header("Location: $BASE_PATH" . "/home.php"));
    }


    $id = se($_GET, "game_id", -1, false);
    // $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'No referrer';
    // echo "You were redirected from: " . htmlspecialchars($referrer);
    if ($id > 0) {
        $db = getDB();
        try {
            // if there are relationships, delete from child tables first
            // alternatively, during FOREIGN KEY creation would could have used cascade delete
            $stmt = $db->prepare("DELETE FROM `Games_details` where game_id = :id");
            $stmt->execute([":id" => $id]);
            // flash($stmt->rowCount());
            if($stmt->rowCount()>0){
                flash("Delete successful", "success");
            }else{
                flash("No records were found with the given game id", "warning");
            }
            // flash("You were redirected from: " . htmlspecialchars($referrer), "success");
        } catch (PDOException $e) {
            //error_log("Error deleting: " . var_export($e, true));
            //echo "Error deleting: " . var_export($e, true);
            flash("There was an error deleting the record", "danger");
        }
    }else{
        //Flash message if given game ID is negative (invalid value)
        flash("Error: Invalid game ID", "danger");
    }
    unset($_GET["game_id"]);

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