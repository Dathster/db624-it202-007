<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}



$query_games_details = "SELECT * FROM `Games_details` ORDER BY created DESC LIMIT 25";
$db = getDB();
$stmt = $db->prepare($query_games_details);
$results_games_details = [];
try {
    $stmt->execute();
    $r = $stmt->fetchAll();
    if ($r) {
        $results_games_details = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching game details " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

$query_game_tags = "SELECT * FROM `Game_tags` ORDER BY created DESC LIMIT 25";
$db = getDB();
$stmt = $db->prepare($query_game_tags);
$results_game_tags = [];
try {
    $stmt->execute();
    $r = $stmt->fetchAll();
    if ($r) {
        $results_game_tags = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching game tags " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

$query_game_media = "SELECT * FROM `Game_media` ORDER BY created DESC LIMIT 25";
$db = getDB();
$stmt = $db->prepare($query_game_media);
$results_game_media = [];
try {
    $stmt->execute();
    $r = $stmt->fetchAll();
    if ($r) {
        $results_game_media = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching game media " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

$query_game_requirements = "SELECT * FROM `Game_requirements` ORDER BY created DESC LIMIT 25";
$db = getDB();
$stmt = $db->prepare($query_game_requirements);
$results_game_requirements = [];
try {
    $stmt->execute();
    $r = $stmt->fetchAll();
    if ($r) {
        $results_game_requirements = $r;
    }
} catch (PDOException $e) {
    error_log("Error fetching game requirements " . var_export($e, true));
    flash("Unhandled error occurred", "danger");
}

$games_details_table = ["data" => $results_games_details, "title" => "Game Details", "ignored_columns" => ["id"], "edit_url" => get_url("admin/games_edit.php")];
$game_tags_table = ["data" => $results_game_tags, "title" => "Game Tags", "ignored_columns" => ["id"], "edit_url" => get_url("admin/games_edit.php")];
$game_media_table = ["data" => $results_game_media, "title" => "Game Media", "ignored_columns" => ["id"], "edit_url" => get_url("admin/games_edit.php")];
$game_requirements_table = ["data" => $results_game_requirements, "title" => "Game Requirements", "ignored_columns" => ["id"], "edit_url" => get_url("admin/games_edit.php")];



?>
<div class="container-fluid">
    <h3 class='mt-3 mb-3'>Steam Game Data</h3>
    <?php render_table($games_details_table); ?>
    <hr class='border-2'>
    <h3 class='mt-3 mb-3'>Steam Game Data</h3>
    <?php render_table($game_tags_table); ?>
    <hr class='border-2'>
    <?php render_table($game_media_table); ?>
    <hr class='border-2'>
    <h3 class='mt-3 mb-3'>Steam Game Data</h3>
    <?php render_table($game_requirements_table); ?>    
</div>