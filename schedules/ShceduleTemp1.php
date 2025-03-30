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

    $activities = []; // Store the final result

    foreach ($denizens as $denizen) {
        $denizenId = $denizen['denizen_id'];
        $denizenName = $denizen['name'];

        // Convert denizen name into a safe filename
        $scheduleFile = "schedules/" . strtolower(str_replace(' ', '_', $denizenName)) . ".php";

        if (file_exists($scheduleFile)) {
            include $scheduleFile;
            if (function_exists('getDenizenActivity')) {
                // Attempt to retrieve the activity
                $activity = getDenizenActivity($pdo, $denizen);
                if ($activity && isset($activity['action_id'], $activity['district_id'], $activity['location_id'])) {
                    $actionId = $activity['action_id'];
                    $districtId = $activity['district_id'];
                    $locationId = $activity['location_id'];
                } else {
                    continue;
                }
            } else {
                continue;
            }
        } else {
            // Default random selection
            $actionQuery = $pdo->query("SELECT action_id FROM actions ORDER BY RAND() LIMIT 1");
            $actionId = $actionQuery->fetchColumn();

            $districtQuery = $pdo->query("SELECT district_id FROM district ORDER BY RAND() LIMIT 1");
            $districtId = $districtQuery->fetchColumn();

            $locationQuery = $pdo->prepare("SELECT location_id FROM location WHERE district_id = :district_id ORDER BY RAND() LIMIT 1");
            $locationQuery->execute(['district_id' => $districtId]);
            $locationId = $locationQuery->fetchColumn();
        }

        if ($locationId) {
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

    // Output the updated activity data as JSON
    header('Content-Type: application/json');
    if (!empty($activities)) {
        echo json_encode($activities, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    } else {
        echo json_encode(['message' => 'No activities found']);
    }
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>