<?php
// denizen_schedule.php


    $denizenId = 13;

    date_default_timezone_set('Asia/Manila'); // Set to Philippine time
    $hour = intval(date('H')); // Get the current hour as an integer

    // Variables to store the selected action, location, and district
    $actionId = null;
    
    


    if ($hour >= 6 && $hour < 12) {
       $location=2;
       $district=1;
       $actionQuery = $pdo->query("SELECT action_id FROM actions WHERE type = 'Work' ORDER BY RAND() LIMIT 1");
       $actionId = $actionQuery->fetchColumn();
        
    } 
    
    
    elseif ($hour >= 12 && $hour < 13) {
       // Select a random location with type 'Food'
$locationQuery = $pdo->query("SELECT location_id FROM location WHERE type = 'Food' ORDER BY RAND() LIMIT 1");
$district=1;
$location = $locationQuery->fetchColumn();

if ($location) {
    // Select a random action associated with the selected location
    $actionQuery = $pdo->prepare("SELECT action_id FROM actions WHERE location_id = :location_id ORDER BY RAND() LIMIT 1");
    $actionQuery->execute(['location_id' => $location]);
    $actionId = $actionQuery->fetchColumn();
}

    } 
    
    
    
    elseif ($hour >= 13 && $hour < 17) {
        $location=2;
        $district=1;
        $actionQuery = $pdo->query("SELECT action_id FROM actions WHERE type = 'Work' ORDER BY RAND() LIMIT 1");
        $actionId = $actionQuery->fetchColumn();
    } 
    
    
    elseif ($hour >= 17 && $hour < 22) {
        // Generate a random number between 1 and 100
        $chance = rand(1, 100);
    
        if ($chance <= 50) {
            // 75% chance: Select a random action with type 'Crime'
            $district = 1;
            $location = 51;
            $actionQuery = $pdo->query("SELECT action_id FROM actions WHERE type = 'Home' ORDER BY RAND() LIMIT 1");
        } else {
            // 25% chance: Select a random action with type 'Art'
            $actionQuery = $pdo->query("SELECT action_id FROM actions WHERE type = 'Social' ORDER BY RAND() LIMIT 1");
        }
    
        $actionId = $actionQuery->fetchColumn();
    }
    
    
    else {
        
            $location = 51;
            $actionId = 28;
            $district = 1;
        
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
