<?php
require_once 'languages/en.php'; 
$title = $products_page['title'];
require_once 'layout.php';
require_once 'common.php';

// Only admin have access to this page
// If the user is not admin it will be redirected to home
if (!isset($_SESSION["admin"])) {
    header("Location: http://localhost");
    exit; // prevent further execution
}

// Getting all products from db
$sql = "SELECT id, title, description, price, image FROM products";
$result = $connect_db->query($sql);

// output data in the page
showProduct($result, $_SESSION['cart'], str_replace(['/','.php'],'',$_SERVER['PHP_SELF']), $products_page);

?>