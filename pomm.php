<?php
include('db_connection.php'); // Include the database connection file

// Fetch online players' data
$sql = "SELECT name, race, position_x, position_y, map FROM characters WHERE online = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Function to determine faction based on race
function getFaction($race) {
    $horde = [2, 5, 6, 8, 10]; // Horde race IDs
    return in_array($race, $horde) ? 'Horde' : 'Alliance';
}

// Map dimensions
$mapWidth = 1337;
$mapHeight = 893;

// Sample points for Eastern Kingdoms (map ID 0)
$samplePointsEK = [
    ['db_x' => -8833.379883, 'db_y' => 628.0627991, 'px_x' => 966, 'px_y' => 569], // Stormwind
    ['db_x' => -4918.879883, 'db_y' => -940.406006, 'px_x' => 1002, 'px_y' => 445], // Ironforge
    ['db_x' => 1584.069946, 'db_y' => 241.9870000, 'px_x' => 969, 'px_y' => 240], // Undercity
    ['db_x' => -14297.200195, 'db_y' => 530.992981, 'px_x' => 665, 'px_y' => 720] // Bootybay
];

// Sample points for Kalimdor (map ID 1)
$samplePointsKalimdor = [
    ['db_x' => 9949.559570, 'db_y' => 2284.209961, 'px_x' => 197, 'px_y' => 138], // Darnassus
    ['db_x' => 1629.359985, 'db_y' => -4373.390137, 'px_x' => 395, 'px_y' => 395], // Orgrimmar
    ['db_x' => -1277.369995, 'db_y' => 124.804001, 'px_x' => 263, 'px_y' => 473], // Thunder Bluff
    ['db_x' => -4841.189941, 'db_y' => 1309.439941, 'px_x' => 226, 'px_y' => 579], // Feralas
    ['db_x' => -7177.149902, 'db_y' => -3785.340088, 'px_x' => 379, 'px_y' => 648] // Gadgetzan
];

// Function to calculate scale factors and offsets
function calculateScaleAndOffset($samplePoints, $mapWidth, $mapHeight) {
    $dbMinX = min(array_column($samplePoints, 'db_x'));
    $dbMaxX = max(array_column($samplePoints, 'db_x'));
    $dbMinY = min(array_column($samplePoints, 'db_y'));
    $dbMaxY = max(array_column($samplePoints, 'db_y'));

    $scaleX = $mapWidth / ($dbMaxX - $dbMinX);
    $scaleY = $mapHeight / ($dbMaxY - $dbMinY);

    return [$scaleX, $scaleY, $dbMinX, $dbMinY];
}

// Calculate scale and offset for both maps
list($scaleXEK, $scaleYEK, $dbMinXEK, $dbMinYEK) = calculateScaleAndOffset($samplePointsEK, $mapWidth, $mapHeight);
list($scaleXKalimdor, $scaleYKalimdor, $dbMinXKalimdor, $dbMinY) = calculateScaleAndOffset($samplePointsKalimdor, $mapWidth, $mapHeight);

// Function to map DB coordinates to pixel coordinates
function mapToPixels($dbX, $dbY, $scaleX, $scaleY, $dbMinX, $dbMinY) {
    $pxX = ($dbX - $dbMinX) * $scaleX;
    $pxY = ($dbY - $dbMinY) * $scaleY;
    return ['px_x' => $pxX, 'px_y' => $pxY];
}

// HTML for displaying the map with online players
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body { font-family: Arial, sans-serif; }
        #map { position: relative; width: {$mapWidth}px; height: {$mapHeight}px; background: url('img/azeroth-map-small.png') no-repeat; }
        .player-icon { position: absolute; width: 24px; height: 24px; }
    </style>
    <title>Online Players Map</title>
</head>
<body>
    <h1>Online Players Map</h1>
    <div id='map'>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $race = $row['race'];
        $positionX = $row['position_x'];
        $positionY = $row['position_y'];
        $mapId = $row['map'];
        $faction = getFaction($race);
        $icon = ($faction == 'Horde') ? 'img/horde.gif' : 'img/alliance.gif';

        if ($mapId == 0) {
            $pixelCoords = mapToPixels($positionX, $positionY, $scaleXEK, $scaleYEK, $dbMinXEK, $dbMinYEK);
        } elseif ($mapId == 1) {
            $pixelCoords = mapToPixels($positionX, $positionY, $scaleXKalimdor, $scaleYKalimdor, $dbMinXKalimdor, $dbMinYKalimdor);
        } else {
            // Skip unknown maps
            continue;
        }

        echo "<img src='$icon' alt='$faction' class='player-icon' style='left: {$pixelCoords['px_x']}px; top: {$pixelCoords['px_y']}px;' title='$name'>";
    }
} else {
    echo "<p>No players online.</p>";
}

echo "    </div>
</body>
</html>";

$stmt->close();
$conn->close();
?>
