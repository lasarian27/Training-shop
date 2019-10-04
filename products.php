<?php
require_once 'common.php';

// Only admin have access to this page
// If the user is not admin it will be redirected to home
if (!isset($_SESSION["admin"])) {
    header("Location: " . URL);
}

// Getting all products from db
$sql = "SELECT id, title, description, price, image FROM products";

$result = $connect_db->prepare($sql);
if ($result) {
    $result->execute();
    $products = $result->get_result();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= translate('title') ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body class="container">
<?php require_once 'cart_template.php'; ?>
</body>
</html>
