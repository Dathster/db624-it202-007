<?php if (isset($data)) : ?>
    <?php
        $_view_url = se($data, "view_url", "", false);
        $_edit_url = se($data, "edit_url", "", false);
        $_delete_url = se($data, "delete_url", "", false);
        
        $_view_label = "View";
        $_edit_label = "Edit";
        $_delete_label = "Delete";

        $_view_classes = se($data, "view_classes", "btn btn-primary", false);
        $_edit_classes = se($data, "edit_classes", "btn btn-warning", false);
        $_delete_classes = se($data, "delete_classes", "btn btn-danger", false);

        $_screenshot = (count($data["screenshots"]))?$data["screenshots"][0]["url"]:"";
        $_game_name = se($data, "game_name", "", false);
        $_price = se($data, "price", "", false);
        $_developer_name = se($data, "developer_name", "", false);
        $_tags = (empty($data["combined_tags"]))?"None":$data["combined_tags"];
        $_game_id = se($data,"game_id", "", false);
        $_release_date = se($data, "release_date", "", false);
        $_query_string = se($data, "query_string", "", false);
    ?>    
    
    
    
    <div class="card">
        <div class="card-header">
            <?php 
                echo "From API: ".$data["from_api"];
            ?>
        </div>
        <?php if (count($data["screenshots"])): ?>
        <img class="card-img-top" src="<?php echo $_screenshot; ?>" alt="Card image cap">
        <?php endif ?>
        <div class="card-body">
            <h5 class="card-title">
                <?php echo $_game_name; ?>
            </h5>
            <h6 class="card-subtitle mb-2 text-body-secondary">
                <?php echo $_price;?>
            </h6>
            <h6 class="card-subtitle mb-2 text-body-secondary">
                <?php echo $_developer_name; ?>
            </h6>
            <h6 class="card-subtitle mb-2 text-body-secondary">
                <?php echo "Release date: " . $_release_date; ?>
            </h6>
            
            <!-- Collapse Trigger -->
            <button class="btn btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#gameTags_<?php echo $_game_id; ?>" aria-expanded="false" aria-controls="gameTags_<?php echo $_game_id; ?>">
                Tags
            </button>

            <!-- Collapsible Content -->
            <div class="collapse mt-3" id="gameTags_<?php echo $_game_id; ?>">
                <div class="card card-body">
                <?php if (!empty($data["combined_tags"])) echo $_tags; else echo "None"; ?>
                </div>
            </div>

            <!-- <div class="card-text">
                
            </div> -->

            <div class="card-footer">
                <ul class="list-group list-group-flush">
                    <div class="row">
                        <div class="col-3">
                            <?php if ($_view_url) : ?>
                                <a href="<?php echo $_view_url; ?>?<?php echo "game_id"; ?>=<?php echo $_game_id; ?>" class="<?php se($_view_classes); ?>"><?php se($_view_label); ?></a>
                            <?php endif; ?>
                        </div>
                        <div class="col-3">
                            <?php if ($_edit_url) : ?>
                                <a href="<?php echo $_edit_url; ?>?<?php echo "game_id"; ?>=<?php echo $_game_id; ?>" class="<?php se($_edit_classes); ?>"><?php se($_edit_label); ?></a>
                            <?php endif; ?>
                        </div>
                        <div class="col-3">
                            <?php if ($_delete_url) : ?>
                                <a href="<?php echo $_delete_url; ?>?<?php echo "game_id"; ?>=<?php echo $_game_id . "&$_query_string"; ?>" class="<?php se($_delete_classes); ?>"><?php se($_delete_label); ?></a>
                            <?php endif; ?>        
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </div>
<?php endif ?>