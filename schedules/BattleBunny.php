<?php
// denizen_schedule.php

function getDenizenActivity($pdo, $denizen) {
    $denizenId = $denizen['denizen_id'];
    $denizenId = 18;
    $hour = date('H');
    $dayOfWeek = date('N'); // 1 (Monday) to 7 (Sunday)

    // Check if it's a weekday (1-5)
    $isWeekday = $dayOfWeek >= 1 && $dayOfWeek <= 5;

    if ($isWeekday) {
        if ($hour >= 6 && $hour < 12 || ($hour >= 13 && $hour < 18)) {
            // Work/Occupation
            $occupationQuery = $pdo->prepare("SELECT location_id, type FROM occupation WHERE denizen_id = :denizen_id");
            if (!$occupationQuery->execute(['denizen_id' => $denizenId])) {
                echo "Error: ".$occupationQuery->errorInfo()[2];
                return null;
            }
            $occupation = $occupationQuery->fetch(PDO::FETCH_ASSOC);
            if ($occupation) {
                $locationId = $occupation['location_id'];
                $type = $occupation['type'];

                $districtQuery = $pdo->prepare("SELECT district_id, name FROM location WHERE location_id = :location_id");
                if (!$districtQuery->execute(['location_id' => $locationId])) {
                    echo "Error: ".$districtQuery->errorInfo()[2];
                    return null;
                }
                $location = $districtQuery->fetch(PDO::FETCH_ASSOC);

                $actionQuery = $pdo->prepare("SELECT action_id FROM actions WHERE location_id = :location_id OR type = :type ORDER BY RAND() LIMIT 1");
                if (!$actionQuery->execute(['location_id' => $locationId, 'type' => $type])) {
                    echo "Error: ".$actionQuery->errorInfo()[2];
                    return null;
                }
                $actionId = $actionQuery->fetchColumn();

                echo "Occupation: Action ID: $actionId, District ID: {$location['district_id']}, Location ID: $locationId\n";
                return ['action_id' => $actionId, 'district_id' => $location['district_id'], 'location_id' => $locationId];
            }
        } elseif ($hour >= 12 && $hour < 13) {
            // Lunch
            $foodQuery = $pdo->query("SELECT location_id, district_id FROM location WHERE type = 'Food' ORDER BY RAND() LIMIT 1");
            $food = $foodQuery->fetch(PDO::FETCH_ASSOC);
            if (!$food) {
                echo "Error: No food location found.";
                return null;
            }

            $actionQuery = $pdo->prepare("SELECT action_id FROM actions WHERE location_id = :location_id ORDER BY RAND() LIMIT 1");
            if (!$actionQuery->execute(['location_id' => $food['location_id']])) {
                echo "Error: ".$actionQuery->errorInfo()[2];
                return null;
            }
            $actionId = $actionQuery->fetchColumn();

            echo "Lunch: Action ID: $actionId, District ID: {$food['district_id']}, Location ID: {$food['location_id']}\n";
            return ['action_id' => $actionId, 'district_id' => $food['district_id'], 'location_id' => $food['location_id']];
        }
    }
    echo "No matching schedule found.\n";
    return null;
}
?>