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
    <p>Remember, we typically won't be frequently calling live data from our API, this is merely a quick sample. We'll want to cache data in our DB to save on API quota.</p>
    <form>
        <div>
            <label>Symbol</label>
            <input name="symbol" />
            <input type="submit" value="Fetch Stock" />
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

        <?php
            echo "$name <br>";
            echo "$desc <br>";
            echo "$release_date <br>";
        ?>
    </div>
</div>
<?php
require(__DIR__ . "/../../partials/flash.php");