<?php
// denizen_schedule.php

function getDenizenActivity($pdo, $denizen) {
    $denizenId = $denizen['denizen_id'];

    date_default_timezone_set('Asia/Manila'); // Set to Philippine time

    $hour = intval(date('H')); // Get the current hour as an integer
    
   // Display the current hour for debugging
    
    if ($hour >= 6 && $hour < 12) {

        $actionId = [22,21,20,19,18,17,16,15,14][array_rand([22,21,20,19,18,17,16,15,14])];
        $location = 53;
        $district = 1;
    
    
       
        return ['action_id' => $actionId, 'district_id' => $district, 'location_id' => $location];
    } 
    
    
    
    
    elseif ($hour >= 12 && $hour < 13) {

    
        $location = [13,14,52][array_rand([13,14,52])];
        $actionId = 0;
    
        if($location == 13){
             $actionId = [36,37,38][array_rand([36,37,38])];
        }
    
        elseif ($location == 14){
            $actionId = [33,34,35][array_rand([33,34,35])];
        }
    
        elseif ($location == 52){
            $actionId = [29,30,31,32][array_rand([29,30,31,32])];
        }
    
        $district = 1;
    
       
        return ['action_id' => $actionId, 'district_id' => $district, 'location_id' => $location];
    } 
    
    
    
    elseif ($hour >= 13 && $hour < 17) {
        
        $actionId = [22,21,20,19,18,17,16,15,14][array_rand([22,21,20,19,18,17,16,15,14])];
        $location = 53;
        $district = 1;
    
    
       
        return ['action_id' => $actionId, 'district_id' => $district, 'location_id' => $location];
      
    } 
    
    
    elseif ($hour >= 17 && $hour < 22) {
    
      
       
        
        $location = [6,8,9,16,17,18,19,27,29,30,31,32,48,49,50][array_rand([6,8,9,16,17,18,19,27,29,30,31,32,48,49,50])];
        $actionId = [ 23,24,25,26,27][array_rand([ 23,24,25,26,27])];
        $district = 1;
      
        return ['action_id' => $actionId, 'district_id' => $district, 'location_id' => $location];
    } 
    
    else {
      
        $location =51;
        $actionId =28;
        $district =1;
      
        return ['action_id' => $actionId, 'district_id' => $district, 'location_id' => $location];
    }
}
?>
