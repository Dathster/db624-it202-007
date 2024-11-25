<?php
    require(__DIR__ . "/../../../partials/nav.php");

    if (!has_role("Admin")) {
        flash("You don't have permission to view this page", "warning");
        die(header("Location: $BASE_PATH" . "/home.php"));
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
            <p></p>
            <?php render_input(["type" => "number", "name" => "game_id", "placeholder" => "Game ID"]); ?>
            <?php render_input(["type" => "hidden", "name" => "action", "value" => "fetch_details"]); ?>
            <?php render_button(["text" => "Get details", "type" => "submit",]); ?>
        </form>
    </div>
    <div id="create" style="display: none;" class="tab-target">
        <form method="POST">
            <?php render_input(["type" => "number", "name" => "id", "label" => "Game ID", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "name", "label" => "Name", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "number", "name" => "price", "label" => "Price", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "date", "name" => "release_date", "label" => "Release Date", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "dev_name", "label" => "Developer Name", "rules" => ["required" => "required"]]); ?>
            <?php render_input(["type" => "text", "name" => "publisher_name", "label" => "Publisher Name"]); ?>
            <?php render_input(["type" => "text", "name" => "franchise_name", "label" => "Franchise Name"]); ?>
            
            <?php render_input(["type" => "text", "name" => "tags", "label" => "Tags (do not include spaces after commas)", "rules" => ["required" => "required"]]); ?>

            <?php render_input(["type" => "hidden", "name" => "action", "value" => "create"]); ?>
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
