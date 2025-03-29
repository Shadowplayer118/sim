<?php
// get_activity.php
$host = 'localhost';
$dbname = 'purgatory_city';
$username = 'root';
$password = '';

try {
    // Establish a connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get all denizens with their IDs
    $denizenQuery = $pdo->query("SELECT denizen_id, name FROM denizen");
    $denizens = $denizenQuery->fetchAll(PDO::FETCH_ASSOC);

    // Get all actions with their IDs
    $actionQuery = $pdo->query("SELECT action_id, name FROM actions");
    $actions = $actionQuery->fetchAll(PDO::FETCH_ASSOC);

    // Get all districts with their IDs
    $districtQuery = $pdo->query("SELECT district_id, name FROM district");
    $districts = $districtQuery->fetchAll(PDO::FETCH_ASSOC);

    foreach ($denizens as $denizen) {
        $denizenId = $denizen['denizen_id'];

        // Select a random action
        $randomAction = $actions[array_rand($actions)];
        $actionId = $randomAction['action_id'];

        // Select a random district
        $randomDistrict = $districts[array_rand($districts)];
        $districtId = $randomDistrict['district_id'];

        // Get a random location within the selected district
        $locationQuery = $pdo->prepare("SELECT location_id, name FROM location WHERE district_id = :district_id ORDER BY RAND() LIMIT 1");
        $locationQuery->execute(['district_id' => $districtId]);
        $locationResult = $locationQuery->fetch(PDO::FETCH_ASSOC);
        $locationId = $locationResult ? $locationResult['location_id'] : null;

        if ($locationId) {
            // Check if the denizen already exists in the activities table
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
                    'district_id' => $districtId,
                    'location_id' => $locationId,
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
                    'district_id' => $districtId,
                    'location_id' => $locationId
                ]);
            }
        }
    }

    // Fetch the updated activity data
    $activityQuery = $pdo->query("
        SELECT d.name AS denizen_name, a.name AS action_name, ds.name AS district_name, l.name AS location_name 
        FROM activities 
        JOIN denizen d ON activities.denizen_id = d.denizen_id
        JOIN actions a ON activities.action_id = a.action_id
        JOIN district ds ON activities.district_id = ds.district_id
        JOIN location l ON activities.location_id = l.location_id
    ");
    $activities = $activityQuery->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($activities);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>