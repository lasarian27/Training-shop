<?php

require_once 'common.php';

$title = translate('title');

// Logout user automattically in case of 'logout' action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // On logout, destroy the session and redirect to login page
    session_destroy();
    header("Location: " . URL . "login.php");
}

// If $_SESSION['cart'] doesnt exist create an empty one
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Insert new id in $_SESSION['cart'] if that id doesnt exist and the action is 'add'
if (isset($_GET['action']) && $_GET['action'] === "add" && isset($_GET['id']) && !in_array($_GET['id'], $_SESSION['cart'])) {
    $_SESSION['cart'][] = $_GET['id'];
}

// Get products from db that are not in cart
if (count($_SESSION['cart'])) {
    $sql = "SELECT * FROM `products` WHERE `id` NOT IN  (".implode(',',$_SESSION['cart']).")";
}else{
    $sql = "SELECT * FROM `products`";
}

$result = $connect_db->prepare($sql);
$result->execute();
$products = $result->get_result();

?>

<?php require_once 'layout.php'; ?>
<?php if($products->num_rows): ?>
<?php 
    // output data
    $cart = $_SESSION['cart'];
    $pageName = getPageName();
    require_once 'cart_template.php';
?>
<?php else: ?>
    <h6 style="margin:10px 0"><?= translate('products_not_found') ?></h6>
    <a href="cart.php" class="btn btn-dark" style="margin: 10px;"> <?= translate('go_to_cart') ?> </a>
<?php endif ?>


</body>
</html>