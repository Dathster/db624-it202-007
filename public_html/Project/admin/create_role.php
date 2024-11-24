<?php
    require(__DIR__ . "/../../../partials/nav.php");

    // Redirect user to home if they don't have admin permission
    if(!has_role("Admin")){ //db624 it202-007 11/11/24
        flash("You don't have permission to view this page", "warning");
        die(header("Location: " . get_url("home.php")));
    }

    //Load role name and description from form
    if(isset($_POST["name"]) && isset($_POST["description"])){
        $name = $_POST["name"];
        $desc = $_POST["description"];

        //Warn user if they didn't provide a role name
        if(empty($name)){
            flash("Name is required", "warning");
        }else{
            //Try to insert the role into the database
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO Roles (name, description, is_active) VALUES (:name, :desc, 1)");
            try{
                $stmt->execute([":name"=>$name, ":desc"=>$desc]);
                flash("Successfully create role $name!", "success");
            }catch(PDOException $e){
                if($e->errorInfo[1] === 1062){
                    flash("A role with this name already exists, please try another", "warning");
                }else{
                    flash(var_export($e->errorInfo,true), "danger");
                }
            }
        }
    }
?>

<div class='container-fluid'>
    <h1>
        Create Role
    </h1>
    <form method="POST">
        <?php render_input(["type"=>"text", "id"=>"r", "name"=>"name", "label"=>"Role name", "rules"=>["required"=>true]]); ?>
        <?php render_input(["type"=>"textarea", "id"=>"d", "name"=>"description", "label"=>"Description"]); ?>
        <?php render_button(["text"=>"Create Role", "type"=>"submit"]); ?>
    </form>
</div>

<?php
require_once(__DIR__ . "/../../../partials/flash.php");
?>