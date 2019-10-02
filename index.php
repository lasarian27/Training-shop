<?php
require_once 'languages/en.php';
$title = $home_page['title'];
require_once 'layout.php';
require_once 'common.php';

// Logged out user automattically in case of 'logout' action
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] || 
    (isset($_GET['action']) && $_GET['action'] === 'logout')
)
{
    // On logout, destroy the session and redirect to login page
    session_destroy();
    header("Location: http://localhost/login.php");
    die();
}

// Insert new id in $_SESSION['cart'] if that id doesnt exist and the action is 'add'
if(isset($_GET['action']) && $_GET['action'] === "add" && isset($_GET['id']) && !in_array($_GET['id'],$_SESSION['cart']))
{
    $_SESSION['cart'][] = $_GET['id'];
}

// Get all products from db
$sql = "SELECT id, title, description, price, image FROM products";
$result = $connect_db->query($sql);

if ($result->num_rows > 0) {
    // output data
    showProduct($result, $_SESSION['cart'], str_replace(['/','.php'],'',$_SERVER['PHP_SELF']), $home_page);
} 

?>