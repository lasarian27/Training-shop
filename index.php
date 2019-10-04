<?php

require_once 'common.php';

// Logout user automatically in case of 'logout' action
if (@$_GET['action'] === 'logout') {
    // On logout, destroy the session and redirect to login page
    session_destroy();
    header("Location: " . URL . "login.php");
}

// Use an empty array as default for $_SESSION
$_SESSION['cart'] = $_SESSION['cart'] ?? [];

// Insert new id in $_SESSION['cart'] if that id does'nt exist and the action is 'add'
if (@$_GET['action'] === "add" && !empty($_GET['id']) && !in_array($_GET['id'], $_SESSION['cart'])) {
    $_SESSION['cart'][] = $_GET['id'];
}

// Get products from db that are not in cart
$sql = "SELECT * FROM `products` ";
if (count($_SESSION['cart'])) {
    $sql .= "WHERE `id` NOT IN  (" . implode(',', $_SESSION['cart']) . ") ";
}

$products = $connect_db->prepare($sql);
$products->execute();
$products = $products->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= translate('home_title') ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body class="container">

<?php if ($products->num_rows):
    require_once 'cart_template.php';
else: ?>
    <h6 style="margin:10px 0"><?= translate('products_not_found') ?></h6>
    <a href="cart.php" class="btn btn-dark" style="margin: 10px;"> <?= translate('go_to_cart') ?> </a>
    <a href="login.php" class="btn btn-dark" style="margin: 10px;"> <?= translate('login') ?> </a>
    <?php if (isset($_SESSION['admin'])): ?>
        <a href="products.php" class="btn btn-dark" style="margin: 10px;"> <?= translate('products') ?> </a>
    <?php endif ?>
<?php endif; ?>

</body>
</html>
