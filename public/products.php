<?php
require_once '../common.php';

$title = translate('title');

// Only admin have access to this page
// If the user is not admin it will be redirected to home
if (!isset($_SESSION["admin"])) {
    header("Location: " . URL);
}

// Getting all products from db
$result = $db->prepare("SELECT * FROM `products`");
if ($result) {
    $result->execute();
    $products = $result->get_result();
}

?>

<?php
require_once DIR . "/views/header.php";
require_once DIR . '/views/show_products.php';
require_once DIR . "/views/footer.php";
?>
