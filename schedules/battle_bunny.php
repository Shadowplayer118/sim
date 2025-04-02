<?php
// denizen_schedule.php

$denizenId = 18;

// Database connection (assuming PDO)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=purgatory_city', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // echo "Database connection failed: " . $e->getMessage();
    exit;
}

function conflictChecker($pdo, $denizen_id, $conflict_types) {
    try {
        // Step 1: Get the denizen's current location from activities
        $stmt = $pdo->prepare("SELECT location_id, district_id FROM activities WHERE denizen_id = ?");
        $stmt->execute([$denizen_id]);
        $denizenLocation = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$denizenLocation) {
            // echo "No location found for denizen $denizen_id.";
            return false;
        }

        $denizenLocationId = $denizenLocation['location_id'];
        $denizenDistrictId = $denizenLocation['district_id'];

        // Step 2: Check for active conflicts
        $stmt = $pdo->query("SELECT * FROM conflict WHERE isActive = 1");
        $conflicts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($conflicts as $conflict) {
            $stmt = $pdo->prepare("SELECT district_id FROM location WHERE location_id = ?");
            $stmt->execute([$conflict['location_id']]);
            $conflictDistrict = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$conflictDistrict) continue;

            $conflictLocationId = $conflict['location_id'];
            $conflictDistrictId = $conflictDistrict['district_id'];

            $isInConflict = false;
            if ($conflict['visibility_range'] == 'location' && $denizenLocationId == $conflictLocationId) {
                $isInConflict = true;
            } elseif ($conflict['visibility_range'] == 'district' && $denizenDistrictId == $conflictDistrictId) {
                $isInConflict = true;
            }

            if ($isInConflict && in_array($conflict['type'], $conflict_types)) {
                $stmt = $pdo->prepare("UPDATE denizen SET Occupied_by = 'conflict', Event_id = ? WHERE denizen_id = ?");
                $stmt->execute([$conflict['conflict_id'], $denizen_id]);

                $stmt = $pdo->prepare("UPDATE activities SET location_id = ?, district_id = ?, action_id = ? WHERE denizen_id = ?");
                $stmt->execute([$conflictLocationId, $conflictDistrictId, $conflict['conflict_id'], $denizen_id]);

                // echo "Denizen $denizen_id is now part of conflict {$conflict['conflict_id']}";
                return true;
            }
        }

        // echo "No applicable conflict found for denizen $denizen_id.";
        return false;
    } catch (PDOException $e) {
        // echo "Error: " . $e->getMessage();
        return false;
    }
}

// Check for conflict first
if (!conflictChecker($pdo, $denizenId, ['Heroic'])) {
    date_default_timezone_set('Asia/Manila');
    $hour = intval(date('H'));

    $actionId = null;
    $location = null;
    $district = 1;

    if ($hour >= 6 && $hour < 12) {
        $actionId = [22, 21, 20, 19, 18, 17, 16, 15, 14][array_rand([22, 21, 20, 19, 18, 17, 16, 15, 14])];
        $location = 53;
    } elseif ($hour >= 12 && $hour < 13) {
        $location = [13, 14, 52][array_rand([13, 14, 52])];
        if ($location == 13) {
            $actionId = [36, 37, 38][array_rand([36, 37, 38])];
        } elseif ($location == 14) {
            $actionId = [33, 34, 35][array_rand([33, 34, 35])];
        } elseif ($location == 52) {
            $actionId = [29, 30, 31, 32][array_rand([29, 30, 31, 32])];
        }
    } elseif ($hour >= 13 && $hour < 17) {
        $actionId = [22, 21, 20, 19, 18, 17, 16, 15, 14][array_rand([22, 21, 20, 19, 18, 17, 16, 15, 14])];
        $location = 53;
    } elseif ($hour >= 17 && $hour < 22) {
        $location = [6, 8, 9, 16, 17, 18, 19, 27, 29, 30, 31, 32, 48, 49, 50][array_rand([6, 8, 9, 16, 17, 18, 19, 27, 29, 30, 31, 32, 48, 49, 50])];
        $actionId = [23, 24, 25, 26, 27][array_rand([23, 24, 25, 26, 27])];
    } else {
        $location = 51;
        $actionId = 28;
    }

    try {
        $checkQuery = $pdo->prepare("SELECT * FROM activities WHERE denizen_id = :denizen_id");
        $checkQuery->execute(['denizen_id' => $denizenId]);
        $existingActivity = $checkQuery->fetch(PDO::FETCH_ASSOC);

        if ($existingActivity) {
            $updateQuery = $pdo->prepare("UPDATE activities SET action_id = :action_id, district_id = :district_id, location_id = :location_id WHERE denizen_id = :denizen_id");
            $updateQuery->execute(['action_id' => $actionId, 'district_id' => $district, 'location_id' => $location, 'denizen_id' => $denizenId]);
        } else {
            $insertQuery = $pdo->prepare("INSERT INTO activities (denizen_id, action_id, district_id, location_id) VALUES (:denizen_id, :action_id, :district_id, :location_id)");
            $insertQuery->execute(['denizen_id' => $denizenId, 'action_id' => $actionId, 'district_id' => $district, 'location_id' => $location]);
        }
    } catch (PDOException $e) {
        error_log("Error updating activity for denizen ID $denizenId: " . $e->getMessage());
    }
}
?>
