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

    // Include the file that runs all the denizen algorithms
    // include 'update_activities.php';
    include 'scheduleAlgo.php';

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
