<?php
require(__DIR__ . "/../../partials/nav.php");

$result = [];
if (isset($_GET["symbol"]) and !empty($_GET["symbol"])) {
        $result = fetch_gameDetails($_GET["symbol"]);

        $name = $result['name'];
        $desc = $result['desc'];
        $release_date = $result['release_date'];
}
?>
<div class="container-fluid">
    <h1>Steam Game Info</h1>
    <form>
        <div>
            <label>Symbol</label>
            <input name="symbol" />
            <input type="submit" value="Fetch Game" />
        </div>
    </form>
    <div class="row ">
        <?php if (isset($result)) : ?>
            <?php foreach ($result as $key=>$stock) : ?>
                <pre>
                    <?php echo $key; var_export($stock);?>
                </pre>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");