<?php
require_once 'languages/en.php'; 
$title = $products_page['title'];
require_once 'layout.php';
require_once 'common.php';
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: http://localhost");
    exit; // prevent further execution
}

$sql = "SELECT id, title, description, price, image FROM products";
$result = $connect_db->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    showProduct($result, $_SESSION['cart'], str_replace(['/','.php'],'',$_SERVER['PHP_SELF']), $products_page);

}

?>