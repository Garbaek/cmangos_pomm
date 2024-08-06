# cmangos_pomm
Player Online Map for CMangos

This repository contains a PHP-based solution for visualizing online players on a map, specifically for an online game. It includes a PHP script to generate a map with player locations based on data fetched from a MySQL database.

Overview
map.php
map.php is the main script that generates an HTML page with an image map showing the locations of online players. It uses data from a MySQL database to place player icons on the map based on their coordinates.

Features:
Fetches online player data from a MySQL database.
Converts database coordinates to pixel coordinates on a map image.
Displays player icons based on their faction (Horde or Alliance).
Supports multiple map IDs (e.g., Eastern Kingdoms and Kalimdor) with specific coordinate mappings.

db-connection.php
db-connection.php establishes a connection to the MySQL database using PHP's PDO extension. It handles the database credentials and provides a connection object used by map.php to fetch player data.

Features:
Connects to the MySQL database using PDO.
Provides a secure and reliable database connection.
Setup Instructions
Prerequisites
PHP 7.4 or higher
MySQL 5.7 or higher
Web server (e.g., Apache, Nginx)
Access to the PHP GD library for image processing

Configuration
Database Configuration

Edit db-connection.php to include your MySQL database credentials.

Map Configuration

Ensure the map image and player icons are placed in the img/ directory.

Update the $mapWidth and $mapHeight in map.php to match the dimensions of your map image.

Sample Points
Update the $samplePointsEK and $samplePointsKalimdor arrays in map.php with accurate sample points for your map.
** The Map Points are still causing some troubles and are not displayed correct.

Usage
Place map.php and db-connection.php in your web server's document root or appropriate directory.
Ensure your database contains the characters table with the necessary columns (name, race, position_x, position_y, map).

Access the map page via: http://yourdomain.com/map.php
The map will display online players with their respective faction icons based on their coordinates.

License
This project is licensed under the MIT License - see the LICENSE file for details.

Contributing
Contributions are welcome! Please open an issue or submit a pull request if you have improvements or fixes.

Contact
For any questions or issues, please contact peter@garbaek.dk
