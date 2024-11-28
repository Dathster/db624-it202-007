<?php
require(__DIR__ . "/../../../lib/functions.php");

session_start();

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
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
        if($stmt->rowCount()>1){
            flash("Delete successful", "success");
        }else{
            flash("No records were found with the given game id", "warning");
        }
        // flash("You were redirected from: " . htmlspecialchars($referrer), "success");
    } catch (PDOException $e) {
        error_log("Error deleting: " . var_export($e, true));
        echo "Error deleting: " . var_export($e, true);
        flash("There was an error deleting the record", "danger");
    }
}
unset($_GET["game_id"]);
$loc = get_url("games_view.php")."?" . http_build_query($_GET);
error_log("Location: $loc");
die(header("Location: $loc"));