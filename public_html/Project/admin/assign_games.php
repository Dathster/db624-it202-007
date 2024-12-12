<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: $BASE_PATH" . "/home.php"));
}
//attempt to apply
if (isset($_POST["users"]) && isset($_POST["games"])) {
    $user_ids = $_POST["users"]; //se() doesn't like arrays so we'll just do this
    $game_ids = $_POST["games"]; //se() doesn't like arrays so we'll just do this
    if (empty($user_ids) || empty($game_ids)) {
        flash("Both users and roles need to be selected", "warning");
    } else {
        //db624 it202 12/11/24
        try{
            foreach($user_ids as $user){
                foreach($game_ids as $game){
                    //Check if the game user association already exists
                    $check_dupe = exec_query("select * from `Game_associations` where `user_id` = $user and `game_id` = $game");
                    if($check_dupe){
                        $res = exec_query("delete from `Game_associations` where `user_id` = $user and `game_id` = $game");
                    }else{
                        $res = exec_query("insert into `Game_associations` (`user_id`, `game_id`) values ($user, $game)");
                    }
                }
            }
            unset($user);
            unset($game);
            flash("Users have been updated successfully", "success");
        }catch(PDOException $e){
            flash("A database error has occured", "danger");
            error_log(var_export($e, true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");
        }catch(Exception $e){
            flash("An unexpected error has occured, please try again later", "danger");
            error_log(var_export($e, true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");
        }
    }
}

//get matched games db624 it202 12/11/24
$games = [];
$game_name = "";
$db = getDB();
$query = "SELECT `game_name`, `game_id` from `Games_details`";

if(isset($_POST["game_name"])){
    $game_name = se($_POST, "game_name", "", false);
    $query  .= " where `game_name` like '%$game_name%'";
}
$query .= " limit 25";
$stmt = $db->prepare($query);
try {
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $games = $results;
    }
} catch (PDOException $e) {
    flash("A database error has occured", "danger");
    error_log(var_export($e->errorInfo, true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");;
}

//search for user by username db624 it202 12/11/24
$users = [];
$username = "";

$username = se($_POST, "username", "", false);

$db = getDB();
$stmt = $db->prepare("SELECT Users.id, username
from Users WHERE username like :username limit 25");
try {
    $stmt->execute([":username" => "%$username%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($results) {
        $users = $results;
    }
} catch (PDOException $e) {
    flash("A database error has occured", "danger");
    error_log(var_export($e->errorInfo, true), 3, "/Users/datha/Documents/IT202_Github/db624-it202-007/public_html/Project/admin/error_log.log");
}



?>

<div class="container-fluid">
    <h1>Assign Games</h1>
    <form method="POST">
        <?php render_input(["type" => "search", "name" => "username", "placeholder" => "Username Search", "value" => $username]);/*db624 it202 12/11/24*/ ?>
        <?php render_input(["type" => "search", "name" => "game_name", "placeholder" => "Game Search", "value" => $game_name]); ?>
        <?php render_button(["text" => "Search", "type" => "submit"]); ?>
    </form>
    <form method="POST"> <!-- db624 it202 12/11/24 -->
        <?php if (isset($username) && !empty($username)) : ?>
            <input type="hidden" name="username" value="<?php se($username, false); ?>" />
        <?php endif; ?>
        <table class="table">
            <thead>
                <th>Users</th>
                <th>Games to Assign</th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <table class="table">
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td>
                                        <label for="user_<?php se($user, 'id'); ?>"><?php se($user, "username"); ?></label>
                                        <input id="user_<?php se($user, 'id'); ?>" type="checkbox" name="users[]" value="<?php se($user, 'id'); ?>" />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </td>
                    <td>
                        <?php foreach ($games as $game) : ?>
                            <div>
                                <label for="game_<?php se($game, 'game_id'); ?>"><?php se($game, "game_name"); ?></label>
                                <input id="game_<?php se($game, 'game_id'); ?>" type="checkbox" name="games[]" value="<?php se($game, 'game_id'); ?>" />
                            </div>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php render_button(["text" => "Toggle Games", "type" => "submit", "color" => "secondary"]); ?>
    </form>
</div>

<?php

//note we need to go up 1 more directory
require_once(__DIR__ . "/../../../partials/flash.php");
?>