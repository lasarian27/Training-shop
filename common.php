<?php 
require_once('config.php');

return [
    'connectDB' => function () {
        $connection_db = new mysqli(SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME);
        if ($connection_db->connect_error) {
            die("Connection failed: " . $connection_db->connect_error);
        }
        return $connection_db;
    },

]
?>