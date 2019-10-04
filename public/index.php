<?php
require_once '../config.php';
require_once '../common.php';

$title = translate('home.title');

// Logout user automatically in case of 'logout' action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // On logout, destroy the session and redirect to login page
    session_destroy();
    header("Location: " . URL . "login.php");
}

// Use an empty array as default for $_SESSION
$_SESSION['cart'] = $_SESSION['cart'] ?? [];

// Insert new id in $_SESSION['cart'] if that id does'nt exist and the action is 'add'
if (isset($_GET['action']) && !empty($_GET['id']) && $_GET['action'] === "add" && !in_array($_GET['id'], $_SESSION['cart'])) {
    $_SESSION['cart'][] = $_GET['id'];
}

// Get products from db that are not in cart
$param_type = '';
$param_values = [];
$products_cart = $_SESSION['cart'];

$sql = "SELECT * FROM `products` ";

if ($products_cart) {
    $param_type = str_repeat('i', count($products_cart));
    $param_values[] = &$param_type;

    foreach ($products_cart as $key => $value) {
        $param_values[] = &$products_cart[$key];
    }

    $sql .= "WHERE `id` NOT IN  (" . str_repeat('? , ', count($products_cart) - 1) . " ? " . ")";
    $products = $db->prepare($sql);
    call_user_func_array(array($products, 'bind_param'), $param_values);
} else {
    $products = $db->prepare($sql);
}

$products->execute();
$products = $products->get_result();

?>
<?php require_once DIR . "/views/header.php" ?>

<?php if ($products->num_rows) :
    require_once DIR . '/views/show_products.php';
else: ?>
    <h6 style="margin:10px 0"><?= validation(translate('products.not.found')) ?></h6>
    <a href="cart.php" class="btn btn-dark" style="margin: 10px;"> <?= validation(translate('go.to.cart')) ?> </a>
    <a href="login.php" class="btn btn-dark" style="margin: 10px;"> <?= validation(translate('login')) ?> </a>
    <?php if (isset($_SESSION['admin'])) : ?>
        <a href="products.php" class="btn btn-dark" style="margin: 10px;"> <?= validation(translate('products')) ?> </a>
    <?php endif ?>
<?php endif; ?>

<?php require_once DIR . "/views/footer.php" ?>
