<?php
require_once 'common.php';
$title = translate('title');
require_once 'layout.php';


// Only admin have access to this page
// If the user is not admin it will be redirected to home
if (!isset($_SESSION["admin"])) 
{
    header("Location: http://localhost");
}

// Getting all products from db
$sql = "SELECT id, title, description, price, image FROM products";

$result = $connect_db->prepare($sql);
$result->execute();
$products = $result->get_result();

// output data
$cart = $_SESSION['cart'];
$pageName = getPageName();
require_once 'cart_template.php';

?>