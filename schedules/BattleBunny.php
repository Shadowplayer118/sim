<?php
// denizen_schedule.php

function getDenizenActivity($pdo, $denizen) {
    $denizenId = $denizen['denizen_id'];

    $hour = date('H');
    $dayOfWeek = date('N'); // 1 (Monday) to 7 (Sunday)

    // Check if it's a weekday (1-5)
    $isWeekday = $dayOfWeek >= 1 && $dayOfWeek <= 5;

    if ($isWeekday) {
        if ($hour >= 6 && $hour < 12 || ($hour >= 13 && $hour < 18)) {
            // Work/Occupation
            $occupationQuery = $pdo->prepare("SELECT location_id, type FROM occupation WHERE denizen_id = :denizen_id");
            $occupationQuery->execute(['denizen_id' => $denizenId]);
            $occupation = $occupationQuery->fetch(PDO::FETCH_ASSOC);

            if ($occupation) {
                $locationId = $occupation['location_id'];
                $type = $occupation['type'];

                $districtQuery = $pdo->prepare("SELECT district_id, name FROM location WHERE location_id = :location_id");
                $districtQuery->execute(['location_id' => $locationId]);
                $location = $districtQuery->fetch(PDO::FETCH_ASSOC);

                $actionQuery = $pdo->prepare("SELECT action_id FROM actions WHERE (location_id = :location_id AND type = :type) ORDER BY RAND() LIMIT 1");
                $actionQuery->execute(['location_id' => $locationId, 'type' => $type]);
                $actionId = $actionQuery->fetchColumn();

                return ['action_id' => $actionId ?: '1', 'district_id' => $location['district_id'], 'location_id' => $locationId];
            }
        } elseif ($hour >= 12 && $hour < 13) {
            // Lunch
            $foodQuery = $pdo->query("SELECT location_id, district_id FROM location WHERE type = 'Food' ORDER BY RAND() LIMIT 1");
            $food = $foodQuery->fetch(PDO::FETCH_ASSOC);

            $actionQuery = $pdo->prepare("SELECT action_id FROM actions WHERE location_id = :location_id ORDER BY RAND() LIMIT 1");
            $actionQuery->execute(['location_id' => $food['location_id']]);
            $actionId = $actionQuery->fetchColumn();

            return ['action_id' => $actionId ?: '1', 'district_id' => $food['district_id'], 'location_id' => $food['location_id']];
        } elseif ($hour >= 18 && $hour < 22) {
            // Social Time
            $socialQuery = $pdo->query("SELECT location_id, district_id FROM location WHERE type = 'Social' ORDER BY RAND() LIMIT 1");
            $social = $socialQuery->fetch(PDO::FETCH_ASSOC);

            $actionQuery = $pdo->prepare("SELECT action_id FROM actions WHERE location_id = :location_id ORDER BY RAND() LIMIT 1");
            $actionQuery->execute(['location_id' => $social['location_id']]);
            $actionId = $actionQuery->fetchColumn();

            return ['action_id' => $actionId ?: '1', 'district_id' => $social['district_id'], 'location_id' => $social['location_id']];
        } elseif ($hour >= 23 && $hour < 5) {
            // Home
            // $homeQuery = $pdo->prepare("SELECT location_id FROM homes WHERE denizen_id = :denizen_id");
            // $homeQuery->execute(['denizen_id' => $denizenId]);
            // $homeLocation = $homeQuery->fetchColumn();

            // $districtQuery = $pdo->prepare("SELECT district_id FROM location WHERE location_id = :location_id");
            // $districtQuery->execute(['location_id' => $homeLocation]);
            // $districtId = $districtQuery->fetchColumn();

            // $actionQuery = $pdo->prepare("SELECT action_id FROM actions WHERE type = 'Home' ORDER BY RAND() LIMIT 1");
            // $actionQuery->execute();
            // $actionId = $actionQuery->fetchColumn();

            return ['action_id' => $actionId ?: '28', 'district_id' => '5', 'location_id' => '51'];
        }
    } else {
        // Weekend Schedule
        $socialQuery = $pdo->query("SELECT location_id, district_id FROM location WHERE type = 'Social' ORDER BY RAND() LIMIT 1");
        $social = $socialQuery->fetch(PDO::FETCH_ASSOC);

        $actionQuery = $pdo->prepare("SELECT action_id FROM actions WHERE location_id = :location_id ORDER BY RAND() LIMIT 1");
        $actionQuery->execute(['location_id' => $social['location_id']]);
        $actionId = $actionQuery->fetchColumn();

        return ['action_id' => $actionId ?: '1', 'district_id' => $social['district_id'], 'location_id' => $social['location_id']];
    }
    return null;
}
?>
