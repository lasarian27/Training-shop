<?php
require_once '../common.php';

if (!isset($_SESSION["admin"])) {
    header("Location: " . URL);
}

$title = translate('product.title');
$product = [
    'title' => isset($_POST['title']) ? $_POST['title'] : '',
    'description' => isset($_POST['description']) ? $_POST['description'] : '',
    'price' => isset($_POST['price']) ? $_POST['price'] : ''
];

$errors = [
    'title' => [],
    'description' => [],
    'price' => [],
    'image' => []
];

if (isset($_GET['status'])) {
    echo translate($_GET['status'] ? "success" : "failed");
}

if ($_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['image'])) {
    // Eliminate an image by id
    $sql = "DELETE FROM `products` WHERE `id`= ?";
    $stmt = $db->prepare($sql);
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
        $stmt = $db->prepare("SELECT * FROM `products` WHERE `id`= ? ");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();

        $data = $stmt->get_result()->fetch_array();
        $product['title'] = $data['title'];
        $product['description'] = $data['description'];
        $product['price'] = $data['price'];
        $product['image_url'] = $data['image'];

    }

    if (isset($_POST['submit'])) {

        foreach ($product as $key => $value) {
            // If user input is empty
            if (empty($product[$key])) {
                // Save in $_SESSION a specific message
                $errors[$key][] = translate($key . '.required');
            }
        }

        // Check if image file is a actual image or fake image
        $image = imageValidator();

        if (!$image['upload_ok']) {
            $errors['image'][] = translate('image.required');
            if ($image['errors']) {
                $errors['image'][] = $image['errors'];
            }
        }

        // Delete status variable from url and
        // If the image had passed the verification the next code is executed
        if (!$errors['title'] && !$errors['description'] && !$errors['price'] && !$errors['image']) {
            $url = $_SERVER['REQUEST_URI'];
            if (strpos($url, 'status') !== false) {
                $url = str_replace(["&status=", "0", "1"], '', $url);
            }

            // If the action is edit, edit a product from db, otherwise create one
            if ($_GET['action'] === "edit") {
                $stmt = $db->prepare("UPDATE `products`  SET `title`= ?, `description`= ?, `price`= ?, `image`= ? WHERE `id`= ?");
                $stmt->bind_param("ssdsi", $product['title'], $product['description'], $product['price'], $image["name"], $_GET['id']);
                header("Location: " . $url . "&status=" . $stmt->execute());
            } else {
                $stmt = $db->prepare("INSERT INTO `products` (`title`, `description`, `price`, `image`) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssds", $product['title'], $product['description'], $product['price'], $image["name"]);
                header("Location: " . $url . "&status=" . $stmt->execute());
            }
        }
    }
}

?>

<?php require_once DIR . "/views/header.php" ?>

<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="form-group" enctype="multipart/form-data">
    <br>
    <input type="text" placeholder="<?= translate('form.title') ?>" value="<?= $product['title'] ?>" name="title"
           class="form-control">
    <?php showMessages($errors['title']) ?>
    <br>
    <input type="text" placeholder="<?= translate('form.description') ?>" value="<?= $product['description'] ?>"
           name="description" class="form-control">
    <?php showMessages($errors['description']) ?>
    <br>
    <input type="number" step="any" placeholder="<?= translate('form.price') ?>" value="<?= $product['price'] ?>"
           name="price" class="form-control">
    <?php showMessages($errors['price']) ?>
    <br>
    <input type="file" name="fileToUpload" class="form-control-file" id="formFile">
    <?php showMessages($errors['image']) ?>
    <br>
    <a href="products.php" name="submit" class='btn btn-dark' style='margin: 10px'><?= translate('products') ?></a>
    <button type="submit" value="click" name="submit" class='btn btn-primary'
            style='margin: 10px'><?= translate('save') ?></button>
</form>

<?php require_once DIR . "/views/footer.php" ?>
