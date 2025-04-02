<?php
$host = 'localhost';
$dbname = 'purgatory_city';
$username = 'root';
$password = '';

try {
    // Establish a connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set all isActive and location_id to 0 before updating conflicts
    $resetQuery = $pdo->prepare("UPDATE conflict SET isActive = 0, location_id = 0");
    $resetQuery->execute();

    // Reset all denizens' Occupied_by and Event_id to NULL

    // Select a random number of locations (between 1 and 5 for example)
    $numLocations = rand(1, 5);
    $locationQuery = $pdo->query("SELECT location_id FROM location ORDER BY RAND() LIMIT $numLocations");
    $locations = $locationQuery->fetchAll(PDO::FETCH_COLUMN);

    $selectQuery = $pdo->prepare("SELECT * FROM denizen WHERE Occupied_by IS NOT NULL AND Event_id IS NOT NULL");
    $selectQuery->execute();
    $denizens = $selectQuery->fetchAll(PDO::FETCH_ASSOC);
    $districtId=0;
    $locationName="Any";
    $locationId=0;
    $locationget=[];

    if ($denizens) {
        $conflictPoints = ["Harmless" => 0, "Easy" => 5, "Medium" => 10, "Hard" => 15, "Deadly" => 20];

        foreach ($denizens as $denizen) {
            $denizenId = $denizen['denizen_id'];
            $denizenName = $denizen['name'];
            $skillLvl = $denizen['Skill_Level'];
            $eventId = $denizen['Event_id'];

            $conflictQuery = $pdo->prepare("SELECT level, min_damage, max_damage, name, location_id FROM conflict WHERE conflict_id = ?");
            $conflictQuery->execute([$eventId]);
            $conflict = $conflictQuery->fetch(PDO::FETCH_ASSOC);

            if ($conflict) {
                $conflictLevel = $conflict['level'];
                $minDmg = $conflict['min_damage'];
                $maxDmg = $conflict['max_damage'];
                $conflictName = $conflict['name'];
                $conflictLocationId = $conflict['location_id'];

                $locationQuery = $pdo->prepare("SELECT name, district_id FROM location WHERE location_id = ?");
                $locationQuery->execute([$conflictLocationId]);
                $locationget = $locationQuery->fetch(PDO::FETCH_ASSOC);

                if($locationget){
                    $locationName = $locationget['name'];
                    $districtId = $locationget['district_id'];
                }

                $conflictQuery = $pdo->prepare("SELECT name FROM district WHERE district_id = ?");
                $conflictQuery->execute([$districtId]);
                $districtName = $conflictQuery->fetch(PDO::FETCH_ASSOC)['name'] ?? 'Unknown';

                if (array_key_exists($conflictLevel, $conflictPoints)) {
                    $damageDifference = $conflictPoints[$conflictLevel] - $skillLvl;
                    $denizenHitPoints = 5;
                    $conflictHitPoints = $damageDifference;
                    $damage = 1;

                    while ($denizenHitPoints > 0 && $conflictHitPoints > 0) {
                        $coinFlip = rand(0, 1);
                        if ($coinFlip === 0) {
                            $denizenHitPoints -= max(0, $damage);
                        } else {
                            $conflictHitPoints -= max(0, $damage);
                        }

                        if ($denizenHitPoints <= 0) {
                            $outcome = "$denizenName failed to act on $conflictName in $locationName";
                            $insertQuery = $pdo->prepare("INSERT INTO conflict_history (status, outcome, denizen_id, location_id, district_id) VALUES ('Failed', ?, ?, ?, ?)");
                            $insertQuery->execute([$outcome, $denizenId, $conflictLocationId, $districtId]);
                            break;
                        }
                        if ($conflictHitPoints <= 0) {
                            $outcome = "$denizenName successfully acted on $conflictName in $locationName";
                            $insertQuery = $pdo->prepare("INSERT INTO conflict_history (status, outcome, denizen_id, location_id, district_id) VALUES ('Success', ?, ?, ?, ?)");
                            $insertQuery->execute([$outcome, $denizenId, $conflictLocationId, $districtId]);
                            break;
                        }
                    }
                }
                $damageTaken = rand($minDmg, $maxDmg);
                $updateHealthQuery = $pdo->prepare("UPDATE denizen SET health = GREATEST(health - ?, 0) WHERE denizen_id = ?");
                $updateHealthQuery->execute([$damageTaken, $denizenId]);
            }
        }
    }

    $resetDenizensQuery = $pdo->prepare("UPDATE denizen SET Occupied_by = NULL, Event_id = NULL");
    $resetDenizensQuery->execute();

    $conflictQuery = $pdo->query("SELECT conflict_id FROM conflict ORDER BY RAND() LIMIT $numLocations");
    $conflicts = $conflictQuery->fetchAll(PDO::FETCH_COLUMN);

    foreach ($locations as $index => $location) {
        $conflictId = $conflicts[$index];
        $updateQuery = $pdo->prepare("UPDATE conflict SET location_id = ?, isActive = 1 WHERE conflict_id = ?");
        $updateQuery->execute([$location, $conflictId]);
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
