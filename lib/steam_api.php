<?php
    require_once(__DIR__ . "/api_helper.php");

    function fetch_searchResults($search){
        $data = ["sugg"=>$search];
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

    

    
?>

