<?php
$title = 'Home page';
require_once('layout.php');
['connectDB' => $connect_db] = require_once('common.php');
session_start();

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) 
{
    header("Location: http://localhost/login");  
    die();
}
if (isset($_GET['action']))
{
    switch ($_GET['action']) {
        case 'logout':
            session_destroy();
            header("Location: http://localhost/login");  
            break;
        case 'add-product':
            session_destroy();
        break;
        default:
           break;
    }
}

$isAdmin = $_SESSION['admin'];
$sql = "SELECT id, title, description, price FROM products";
$result = $connect_db()->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo 
        "<div style='display:inline-flex'>
            <img src='/images/image1.jpeg' style='width:50px'/>" .
                "<ul style='list-style-type: none;padding: 5px'" . 
                    "<li>" . $row['title'] . "</li>" . 
                    "<li>" . $row['description'] . "</li>" . 
                    "<li>" . $row['price'] . "$</li>" .
                "</ul>";
                echo $isAdmin ? 
            "<a href='#'>" . 'Remove' . "</a>" :
            "<a href='#'>" . 'Edit' . "</a>" . 
            "<a href='#'>" . 'Delete' . "</a>" .
        "</div>";
    }
    echo $isAdmin ? 
         "<a href='#'>Go to cart</a>" : 
         "<a href='product.php'>" . 'Add' . "</a>" . 
         "<a href='index.php?action=logout'>" . 'LogOut' . "</a>";

} else {
    echo "0 results";
}


?>