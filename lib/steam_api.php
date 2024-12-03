<?php
    require_once(__DIR__ . "/api_helper.php");

    function fetch_searchResults($search){
        $data = ["sugg"=>$search];//db624 it202-007 11/28/24
        $endpoint = "https://games-details.p.rapidapi.com/search";
        $isRapidAPI = true;
        $rapidAPIHost = "games-details.p.rapidapi.com";
        $result = get($endpoint, "STEAM_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

        error_log("Response: " . var_export($result, true));
        if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
            $result = json_decode($result["response"], true);
        } else {
            $result = [];
        }
        // echo $result;
        return $result;
    }

    function fetch_gameDetails($gameID){
        $data = [$gameID];
        $endpoint = "https://games-details.p.rapidapi.com/single_game";
        $isRapidAPI = true;
        $rapidAPIHost = "games-details.p.rapidapi.com";
        $result = get2($endpoint, "STEAM_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

        error_log("Response: " . var_export($result, true));
        if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
            $result = json_decode($result["response"], true);
        } else {
            $result = [];
        }

        return $result;
    }

    function prep_gamesDetails($game_id, $result){
        $id = $game_id;
        $name = $result["name"];
        $price = ($result['pricing'])?$result['pricing'][0]['price']:NULL;
        // echo $price;
        // echo $price === "Free To Play"; 
        if($price === "Free To Play"){
            $price = 0.00;
        }else if($price){
            $price = preg_replace('/[^\d.]/', '', $price);
        }
        // echo "<br> $price <br>";
        $dateString = $result['release_date'];
        if($dateString != 'To be announced'){
            // Create a DateTime object
            $date = new DateTime($dateString);
            // Format it to yyyy-mm-dd
            $formattedDate = $date->format('Y-m-d');
        }else{
            $formattedDate = NULL;
        }
        
        $dev_name = $result["dev_details"]['developer_name'][0];
        $publisher_name = $result["dev_details"]['publisher'][0];
        $franchise_name = (empty($result["dev_details"]['franchise']))?null:$result["dev_details"]['franchise'][0];

        return [
            "game_id"=>$game_id,
            "game_name"=>$name,
            "price"=>$price,
            "release_date"=>$formattedDate,
            "developer_name"=>$dev_name,
            "publisher_name"=>$publisher_name,
            "franchise_name"=>$franchise_name
        ];
    }//db624 it202-007 11/28/24

    function prep_gameMedia($game_id, $result){
        $screenshot_array = $result['images']['screenshot'];//db624 it202-007 11/28/24
        $videos_array = $result['images']['videos'];
        $output_arr = [];

        foreach ($screenshot_array as $screenshot){
            $output_arr[] = ["game_id"=>$game_id, "url"=>$screenshot, "type"=>"screenshot"];
        }
        unset($screenshot);

        foreach ($videos_array as $video){
            $output_arr[] = ["game_id"=>$game_id, "url"=>$video, "type"=>"video"];
        }
        unset($video);

        return $output_arr;
    }

    function prep_gameTags($game_id, $result){
        $tags_array = $result['tags'];
        $output_arr = [];

        foreach ($tags_array as $tag){
            $output_arr[] = ["game_id"=>$game_id, "tag"=>$tag];
        }
        unset($tag);

        return $output_arr;
    }

    function prep_gameRequirements($game_id, $result){
        $sys_req = $result["sys_req"];
        $output_arr = [];
        $relevant_columns = ['OS:', 'Processor:', 'Memory:', 'Graphics:', 'Storage:'];
        foreach($sys_req as $os=>$reqs){
            
            $min_arr = [];
            $min_set = false;
            foreach($reqs['min'] as $attr){
                if(str_starts_with($attr,'OS:')){
                        $min_arr['os_version']=substr($attr, strlen('OS:')+1);
                        $min_set = true;
                }else if(str_starts_with($attr,'OS *:')){
                    $min_arr['os_version']=substr($attr, strlen('OS *:')+1);
                    $min_set = true;
                }else if(str_starts_with($attr,'Processor:')){
                    $min_arr['processor']=substr($attr, strlen('Processor:')+1);
                    $min_set = true;
                }else if(str_starts_with($attr,'Memory:')){
                    $min_arr['memory']=substr($attr, strlen('Memory:')+1);
                    $min_set = true;
                }else if(str_starts_with($attr,'Graphics:')){
                    $min_arr['graphics']=substr($attr, strlen('Graphics:')+1);
                    $min_set = true;
                }else if(str_starts_with($attr,'Storage:')){
                    $min_set = true;
                    $min_arr['storage']=substr($attr, strlen('Storage:')+1);
                }
            }
            //db624 it202-007 11/28/24
            if($min_set){
                $min_arr["os_version"]=(isset($min_arr["os_version"]))?$min_arr["os_version"]:NULL;
                $min_arr["processor"]=(isset($min_arr["processor"]))?$min_arr["processor"]:NULL;
                $min_arr["graphics"]=(isset($min_arr["graphics"]))?$min_arr["graphics"]:NULL;
                $min_arr["memory"]=(isset($min_arr["memory"]))?$min_arr["memory"]:NULL;
                $min_arr["storage"]=(isset($min_arr["storage"]))?$min_arr["storage"]:NULL;

            }
            
            if(count($min_arr)>1){
                $min_arr['game_id'] = $game_id;
                $min_arr['requirement_type'] = 'min';
                $output_arr[] = $min_arr;
            }


            if($reqs["recomm"]){
                $recom_arr = [];
                $recom_set = false;
                foreach($reqs['recomm'] as $attr){
                    if(str_starts_with($attr,'OS:')){
                        $recom_arr['os_version']=substr($attr, strlen('OS:')+1);
                        $recom_set = true;
                    }else if(str_starts_with($attr,'OS *:')){
                        $recom_arr['os_version']=substr($attr, strlen('OS *:')+1);
                        $recom_set = true;
                    }else if(str_starts_with($attr,'Processor:')){
                        $recom_arr['processor']=substr($attr, strlen('Processor:')+1);
                        $recom_set = true;
                    }else if(str_starts_with($attr,'Memory:')){
                        $recom_arr['memory']=substr($attr, strlen('Memory:')+1);
                        $recom_set = true;
                    }else if(str_starts_with($attr,'Graphics:')){
                        $recom_arr['graphics']=substr($attr, strlen('Graphics:')+1);
                        $recom_set = true;
                    }else if(str_starts_with($attr,'Storage:')){
                        $recom_arr['storage']=substr($attr, strlen('Storage:')+1);
                        $recom_set = true;//db624 it202-007 11/28/24
                    }
                }
                if($recom_set){
                    $recom_arr["os_version"]=(isset($recom_arr["os_version"]))?$recom_arr["os_version"]:NULL;
                    $recom_arr["processor"]=(isset($recom_arr["processor"]))?$recom_arr["processor"]:NULL;
                    $recom_arr["graphics"]=(isset($recom_arr["graphics"]))?$recom_arr["graphics"]:NULL;
                    $recom_arr["memory"]=(isset($recom_arr["memory"]))?$recom_arr["memory"]:NULL;
                    $recom_arr["storage"]=(isset($recom_arr["storage"]))?$recom_arr["storage"]:NULL;
    
                }
                if(count($recom_arr)>1){
                    $recom_arr['game_id'] = $game_id;
                    $recom_arr['requirement_type'] = 'recom';
                    $output_arr[] = $recom_arr;
                }
            }

            
            
            
        }

        return $output_arr;
    }

    function prep_gameDescriptions($game_id, $result){
        $desc = se($result, 'desc', "", false);
        $about = se($result, 'about_game', "", false);
        $output_arr = ["game_id"=>$game_id,
                    "description"=>$desc,
                    "about"=>$about];

        return $output_arr;
    }//db624 it202-007 11/28/24
?>

