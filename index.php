<?php
require_once 'languages/en.php'; 
$title = $home_page['title'];
require_once 'layout.php';
require_once 'common.php';
session_start();

// If loggedIn doesnt exists in $_SESSION 
// or $_SESSION['loggedin] equal false 
// or the action was logout 
// the user will be loged out automatically 
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin'] || 
    (isset($_GET['action']) && $_GET['action'] === 'logout')
)
{
    session_destroy();
    header("Location: http://localhost/login");
    die();
}


if(isset($_GET['action']) && $_GET['action'] === "add" && isset($_GET['id']) && !in_array($_GET['id'],$_SESSION['cart']))
{
    $_SESSION['cart'][] = $_GET['id'];
}

$sql = "SELECT id, title, description, price, image FROM products";
$result = $connect_db->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    showProduct($result, $_SESSION['cart'], str_replace(['/','.php'],'',$_SERVER['PHP_SELF']), $home_page);

} 


?>