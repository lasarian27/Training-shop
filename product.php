<?php

require_once 'common.php';

if (!isset($_SESSION["admin"])) {
    header("Location: " . URL);
}

$product = [
    'title' => isset($_POST['title']) ? $_POST['title'] : '',
    'description' => isset($_POST['description']) ? $_POST['description'] : '',
    'price' => isset($_POST['price']) ? $_POST['price'] : ''
];

$errors = [];
$messages = [];

if ($_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['image'])) {
    // Eliminate an image by id
    $sql = "DELETE FROM `products` WHERE `id`= ? ";
    $stmt = $connect_db->prepare($sql);
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();

    // Delete the actual image
    unlink($_GET['image']);

    // Redirect to products page
    header("Location: " . URL . "products.php");
}

if ($_GET['action'] === "create" || $_GET['action'] === "edit") {

    // If the action is edit autocomplete the inputs with the data from db
    if ($_GET['action'] === "edit" && !isset($_POST['submit'])) {
        $stmt = $connect_db->prepare("SELECT * FROM `products` WHERE `id`= ? ");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();

        $data = $stmt->get_result()->fetch_array();
        $product['title'] = $data['title'];
        $product['description'] = $data['description'];
        $product['price'] = $data['price'];
    }

    if (isset($_POST['submit'])) {

        foreach ($product as $key => $value) {
            // If user input is empty
            if (empty($product[$key])) {
                // Save in $_SESSION a specific message
                $errors[] = translate($key . '_required');
            }
        }

        // Check if image file is a actual image or fake image
        $image = imageValidator();

        if (!$image['upload_ok']) {
            $errors[] = translate('image_required');
        }

        // If the image had passed the verification the new product is inserted
        if (empty($errors)) {

            // Depends on the action specific code is executed
            if ($_GET['action'] === "edit") {
                // Update the product from db

                $stmt = $connect_db->prepare("UPDATE `products`  SET `title`= ?, `description`= ?, `price`= ?, `image`= ? WHERE `id`= ?");
                $stmt->bind_param("ssdsi", $product['title'], $product['description'], $product['price'], $image["name"], $_GET['id']);

                if ($stmt->execute()) {
                    $messages[] = translate('product_updated');
                    header("Location: " . URL . "products.php");
                }
            } else {
                // Insert a new product in db
                $stmt = $connect_db->prepare("INSERT INTO `products` (`title`, `description`, `price`, `image`) VALUES (?,?,?,?)");
                $stmt->bind_param("ssds", $product['title'], $product['description'], $product['price'], $image["name"]);

                if ($stmt->execute()) {
                    $messages[] = translate('product_created');
                    header("Location: " . $_SERVER['REQUEST_URI']);
                }
            }

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= translate('product_title') ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body class="container">
<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="form-group" enctype="multipart/form-data">
    <br>
    <input type="text" placeholder="<?= translate('form_title') ?>" value="<?= $product['title'] ?>" name="title"
           class="form-control">
    <br>
    <input type="text" placeholder="<?= translate('form_description') ?>" value="<?= $product['description'] ?>"
           name="description" class="form-control">
    <br>
    <input type="number" step="any" placeholder="<?= translate('form_price') ?>" value="<?= $product['price'] ?>"
           name="price" class="form-control">
    <br>
    <input type="file" name="fileToUpload" class="form-control-file" id="formFile" ?>
    <?php showMessages($errors) ?>
    <br>
    <a href="products.php" name="submit" class='btn btn-dark' style='margin: 10px'><?= translate('products') ?></a>
    <button type="submit" value="click" name="submit" class='btn btn-primary'
            style='margin: 10px'><?= translate('save') ?></button>
</form>
</body>
</html>
