<?php require(__DIR__ . "/../../partials/nav.php"); ?>

<?php


	if (!isset($_GET["game_id"]) || empty($_GET["game_id"])) {
		flash("No Game ID provided", "warning");
		die(header("Location: $BASE_PATH" . "/games_view.php"));
	}

	$game_id = $_GET["game_id"];
	$query_games_details = "select 
	`gd`.`game_id`,
	`gd`.`game_name`,
	`gd`.`release_date`,
	`gd`.`developer_name`,
	`gd`.`publisher_name`,
	`gd`.`franchise_name`,
	`gd`.`created`,
	`gd`.`modified`,
	case when `gd`.`price` = 0.00 then 'Free To Play'
	else concat('$', `gd`.`price`) end as `price`,
	if(`gd`.`from_api`, 'true', 'false') as `from_api`
	from `Games_details` `gd` where `gd`.`game_id` = $game_id";
	$query_game_tags = "select * from `Game_tags` where `game_id` = $game_id";
	$query_game_media = "select * from `Game_media` where `game_id` = $game_id";
	$query_game_about = "select `about` from `Game_descriptions` where `game_id` = $game_id";

	$results_games_details = select($query_games_details);
	$results_game_tags = select($query_game_tags);
	$results_game_media = select($query_game_media);
	$result_game_about = select($query_game_about);

	$game_about = (empty($result_game_about))?"":se($result_game_about[0],'about', "", false);


	if(empty($results_games_details)){
	flash("Invalid game id detected please try a different game", "warning");
	die(header("Location: games_view.php"));
	}

	$query_screenshots = "select distinct `url` from `Game_media` where `game_id` = $game_id and `type` = 'screenshot'";
	$results_screenshots = select($query_screenshots);

	$query_videos = "select distinct `url` from `Game_media` where `game_id` = $game_id and `type` = 'video'";
	$results_videos = select($query_videos);

	$query_min_requirements = "select * from `Game_requirements` where `game_id` = $game_id and `requirement_type` = 'min'";
	$results_min_requirements = select($query_min_requirements);

	$query_recom_requirements = "select * from `Game_requirements` where `game_id` = $game_id and `requirement_type` = 'recom'";
	$results_recom_requirements = select($query_recom_requirements);

	$table_min_requirements = ["data"=>$results_min_requirements, "title"=>"min requirements"];
	$table_recom_requirements = ["data"=>$results_recom_requirements, "title"=>"recom requirements"];


	$game_name = se($results_games_details[0], "game_name", "", false);
	$price =  se($results_games_details[0], "price", "", false);
	$developer_name = se($results_games_details[0], "developer_name", "", false);
	$publiser_name = se($results_games_details[0], "publisher_name", "", false);
	$franchise_name = se($results_games_details[0], "franchise_name", "", false);
	$release_date = se($results_games_details[0], "release_date", "", false);
	$created = se($results_games_details[0], "created", "", false);
	$modified = se($results_games_details[0], "modified", "", false);
	$from_api = se($results_games_details[0], "from_api", "", false);
	$game_about = (empty($result_game_about))?"":se($result_game_about[0],'about', "", false);

	$edit_url = get_url("admin/game_edit.php");;
	$delete_url = get_url("admin/game_delete.php");

	$_edit_label = "Edit";
	$_delete_label = "Delete";

	$_edit_classes = "btn btn-warning";
	$_delete_classes = "btn btn-danger";
?>

<div class='container-fluid'>
<h1 class='ms-3 me-3'>
	<?php se($game_name); ?>
</h1>

	<?php if (!(empty($results_screenshots) || empty($results_videos))) : ?>
		<div class="row ms-3 me-3">
			<ul class="nav nav-pills">
				<li class="nav-item">
					<a class="switcher nav-link active" href="#" onclick="switchTab('video')">Screenshots</a>
				</li>
				<li class="nav-item">
					<a class="switcher nav-link" href="#" onclick="switchTab('screenshot')">Videos</a>
				</li>
			</ul>  
		</div>

	
		<div class='row '>
			<div class='col-8'>
				<div id="screenshot" class="carousel slide tab-target">
					<div class="carousel-inner">
						<div class="carousel-item active">
							<img src="<?php echo $results_screenshots[0]["url"]?>" class="d-block w-100" alt="...">
						</div>
						<?php if (count($results_screenshots)>1) : ?>
							<?php foreach (array_slice($results_screenshots, 1) as $screenshot): ?>
								<div class="carousel-item">
								<img src="<?php echo $screenshot["url"]?>" class="d-block w-100" alt="...">
								</div>
							<?php endforeach ?>
						<?php endif ?>
					</div>
					<?php if (count($results_screenshots)>1) : ?>
						<button class="carousel-control-prev" type="button" data-bs-target="#screenshot" data-bs-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Previous</span>
						</button>
						<button class="carousel-control-next" type="button" data-bs-target="#screenshot" data-bs-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Next</span>
						</button>
					<?php endif ?>
				</div>
			
				<div id="video" class="carousel slide tab-target" style="display: none;">
					<div class="carousel-inner">
						<div class="carousel-item active">
							<div class="ratio ratio-16x9">
								<video controls>
								<source src="<?php echo $results_videos[0]["url"] ?>" type="video/mp4">
								Your browser does not support the video tag.
							</video>
						</div>
					</div>
					
					<?php if (count($results_videos)>1) : ?>
						<?php foreach (array_slice($results_videos, 1) as $videos): ?>
							<div class="carousel-item">
								<div class="ratio ratio-16x9">
									<video controls>
										<source src="<?php echo $videos["url"] ?>" type="video/mp4">
										Your browser does not support the video tag.
									</video>
								</div>
							</div>
						<?php endforeach ?>


					<?php endif ?>

				</div>
				
				<?php if(count($results_videos)>1) : ?>
					<button class="carousel-control-prev" type="button" data-bs-target="#video" data-bs-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Previous</span>
					</button>
					<button class="carousel-control-next" type="button" data-bs-target="#video" data-bs-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Next</span>
					</button>
				<?php endif ?>	
			</div>

		</div>
	<?php endif ?>

	<div class='col-3 mt-3'>
		<div class="row">	
			<div class="card">
				<h5 class="card-header">Details</h5>
				<div class="card-body">
					<h5 class="card-title"><?php se($price) ?></h5>
					<hr>
					<h5 class="card-title mb-3">Developer: <?php se($developer_name) ?></h5>
					<h5 class="card-title mb-3">Publisher: <?php se($publiser_name) ?></h5>
					<h5 class="card-title mb-3">Franchise: <?php se($franchise_name) ?></h5>
					<hr>
					<h5 class="card-title">Release date: <?php se($release_date) ?></h5>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="card">
				<h5 class="card-header">Record data</h5>
				<ul class="list-group list-group-flush">
					<li class="list-group-item">Created: <?php se($created) ?></li>
					<li class="list-group-item">Last Updated: <?php se($modified) ?></li>
					<li class="list-group-item">From API: <?php se($from_api) ?></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="row card ms-3 me-3 mb-3">
	<h5 class="card-header">Tags</h5>
	<div class='row mt-3'>
		<?php foreach ($results_game_tags as $tag): ?>
			<div class="col-1">
				<p><?php se($tag["tag"]);?></p>
			</div>
		<?php endforeach; ?>
	</div>				
</div>

<div class="row mb-3">
	<div class='col mt-3'>
		<div class="card ms-2">
			<h5 class="card-header">Minimum Specs</h5>
			<div class="card-body">
				<?php foreach($results_min_requirements as $min): ?>
					<h5 class="card-title mb-3"><?php se($min, "os_version") ?></h5>
					<hr>
					<h6 class="card-title mb-3">Processor: <?php se($min, "processor") ?></h6>
					<h6 class="card-title mb-3">Graphics: <?php se($min, "graphics") ?></h6>
					<h6 class="card-title mb-3">Memory: <?php se($min, "graphics") ?></h6>
					<h6 class="card-title mb-3">Storage: <?php se($min, "storage") ?></h6>
					
					<hr class='mb-3'></hr>
				<?php endforeach ?>
				
			</div>
		</div>
	</div>

	<div class='col mt-3'>
		<div class="card me-2">
			<h5 class="card-header">Recommended Specs</h5>
			<div class="card-body">
				<?php foreach($results_recom_requirements as $recom): ?>
					<h5 class="card-title mb-3"><?php se($recom, "os_version") ?></h5>
					<hr>
					<h6 class="card-title mb-3">Processor: <?php se($recom, "processor") ?></h6>
					<h6 class="card-title mb-3">Graphics: <?php se($recom, "graphics") ?></h6>
					<h6 class="card-title mb-3">Memory: <?php se($recom, "graphics") ?></h6>
					<h6 class="card-title mb-3">Storage: <?php se($recom, "storage") ?></h6>
					
					<hr class='mb-3'></hr>
				<?php endforeach ?>
				
			</div>
		</div>
	</div>				
</div>

<div class="card ms-3 me-3 mb-3">
  <div class="card-body">
    <h5 class="card-title">About Game</h5>
    <p class="card-text"><?php se($game_about); ?></p>
  </div>
</div>

<?php if(has_role("Admin")): ?>
	<div class="row card ms-3 me-3 mb-3"> 
		<div class="card-body">
			<h5 class="card-title">
				Admin functions
			</h5>

			<div>
				<ul class="list-group">
					<div class="row">
						<div class="col-1">
							<?php if ($edit_url) : ?>
								<a href="<?php echo $edit_url; ?>?<?php echo "game_id"; ?>=<?php echo $game_id; ?>" class="<?php se($_edit_classes); ?>"><?php se($_edit_label); ?></a>
							<?php endif; ?>
						</div>
						<div class="col-1">
							<?php if ($delete_url) : ?>
								<a href="<?php echo $delete_url; ?>?<?php echo "game_id"; ?>=<?php echo $game_id; ?>" class="<?php se($_delete_classes); ?>"><?php se($_delete_label); ?></a>
							<?php endif; ?>        
						</div>
					</div>
				</ul>
			</div>  
	</div>
<?php endif ?>

</div>




		













<script>
function switchTab(tab) {
	let target = document.getElementById(tab);
	if (target) {
		let eles = document.getElementsByClassName("tab-target");
		for (let ele of eles) {
			ele.style.display = (ele.id === tab) ? "none" : "block";
		}
		let navs = document.getElementsByClassName("switcher");
		for(let nav of navs) {
			nav.classList.remove("active");
		}
		event.target.classList.add("active");
	}
}
</script>



<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>  