<?php
// denizen_schedule.php


    $denizenId = 18;

    date_default_timezone_set('Asia/Manila'); // Set to Philippine time
    $hour = intval(date('H')); // Get the current hour as an integer

    // Variables to store the selected action, location, and district
    $actionId = null;
    $location = null;
    $district = 1; // Default district

    if ($hour >= 6 && $hour < 12) {
        $actionId = [22, 21, 20, 19, 18, 17, 16, 15, 14][array_rand([22, 21, 20, 19, 18, 17, 16, 15, 14])];
        $location = 53;
    } 
    
    
    elseif ($hour >= 12 && $hour < 13) {
        $location = [13, 14, 52][array_rand([13, 14, 52])];
        
        if ($location == 13) {
            $actionId = [36, 37, 38][array_rand([36, 37, 38])];
        } elseif ($location == 14) {
            $actionId = [33, 34, 35][array_rand([33, 34, 35])];
        } elseif ($location == 52) {
            $actionId = [29, 30, 31, 32][array_rand([29, 30, 31, 32])];
        }
    } 
    
    
    
    elseif ($hour >= 13 && $hour < 17) {
        $actionId = [22, 21, 20, 19, 18, 17, 16, 15, 14][array_rand([22, 21, 20, 19, 18, 17, 16, 15, 14])];
        $location = 53;
    } 
    
    
    
    elseif ($hour >= 17 && $hour < 22) {
        $location = [6, 8, 9, 16, 17, 18, 19, 27, 29, 30, 31, 32, 48, 49, 50][array_rand([6, 8, 9, 16, 17, 18, 19, 27, 29, 30, 31, 32, 48, 49, 50])];
        $actionId = [23, 24, 25, 26, 27][array_rand([23, 24, 25, 26, 27])];
    } 
    
    
    else {
        $location = 51;
        $actionId = 28;
    }

    // Update the activities table directly
    try {
        // Check for existing activity
        $checkQuery = $pdo->prepare("SELECT * FROM activities WHERE denizen_id = :denizen_id");
        $checkQuery->execute(['denizen_id' => $denizenId]);
        $existingActivity = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if ($existingActivity) {
            // Update existing activity
            $updateQuery = $pdo->prepare("
                UPDATE activities 
                SET action_id = :action_id, district_id = :district_id, location_id = :location_id 
                WHERE denizen_id = :denizen_id
            ");
            $updateQuery->execute([
                'action_id' => $actionId,
                'district_id' => $district,
                'location_id' => $location,
                'denizen_id' => $denizenId
            ]);
        } else {
            // Insert new activity
            $insertQuery = $pdo->prepare("
                INSERT INTO activities (denizen_id, action_id, district_id, location_id) 
                VALUES (:denizen_id, :action_id, :district_id, :location_id)
            ");
            $insertQuery->execute([
                'denizen_id' => $denizenId,
                'action_id' => $actionId,
                'district_id' => $district,
                'location_id' => $location
            ]);
        }
    } catch (PDOException $e) {
        error_log("Error updating activity for denizen ID $denizenId: " . $e->getMessage());
    }

?>
