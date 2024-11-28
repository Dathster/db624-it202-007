<?php require(__DIR__ . "/../../../partials/nav.php"); ?>

<?php
  $game_id = $_GET["game_id"];
  
  $query_games_details = "select * from `Games_details` where `game_id` = $game_id";
  $query_game_tags = "select * from `Game_tags` where `game_id` = $game_id";
  $query_game_media = "select * from `Game_media` where `game_id` = $game_id";
  $query_game_requirements = "select * from `Game_requirements` where `game_id` = $game_id";

  $results_games_details = select($query_games_details);
  $results_game_tags = select($query_game_tags);
  $results_game_media = select($query_game_media);
  $results_game_requirements = select($query_game_requirements);

  if(empty($results_games_details)){
    flash("Invalid game id detected", "warning");
    die(header("Location: games_view.php"));
  }

  $table_games_details = ["data"=>$results_games_details, "title"=>"game details"];
  $table_game_tags = ["data"=>$results_game_tags, "title"=>"game tags"];
  $table_game_media = ["data"=>$results_game_media, "title"=>"game media"];
  $table_games_requirements = ["data"=>$results_game_requirements, "title"=>"game requirements"];

  $query_screenshots = "select distinct `url` from `Game_media` where `game_id` = $game_id and `type` = 'screenshot'";
  $results_screenshots = select($query_screenshots);

  $query_videos = "select distinct `url` from `Game_media` where `game_id` = $game_id and `type` = 'video'";
  $results_videos = select($query_videos);
  
  $game_name = se($results_games_details[0], "game_name", "", false);
  $price =  se($results_games_details[0], "price", "", false);
  $developer_name = se($results_games_details[0], "developer_name", "", false);
  $publiser_name = se($results_games_details[0], "publisher_name", "", false);
  $franchise_name = se($results_games_details[0], "franchise_name", "", false);
  $release_date = se($results_games_details[0], "release_date", "", false);
  $created = se($results_games_details[0], "created", "", false);
  $modified = se($results_games_details[0], "modified", "", false);
  $from_api = se($results_games_details[0], "from_api", "", false);

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

		
			<div class='row'>
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
						<li class="list-group-item">Modified: <?php se($modified) ?></li>
						<li class="list-group-item">From API: <?php se($from_api) ?></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class='row'>
			
	</div>
</div>




<!-- <div class="container mt-4 tab-target" id="video">
    <div class="ratio ratio-16x9">
        <video controls>
            <source src="<?php //echo $results_videos[0]["url"] ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
</div> -->

<?php render_table($table_games_details); ?>
<?php render_table($table_game_tags); ?>
<?php render_table($table_game_media); ?>
<?php render_table($table_games_requirements); ?>

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
require_once(__DIR__ . "/../../../partials/flash.php");
?>  