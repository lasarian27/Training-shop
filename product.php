<?php

require_once 'common.php';

$title = translate('product_title');

if (!isset($_SESSION["admin"])) {
    header("Location: " . URL);
}

$product = [
    'title' => isset($_POST['title']) ? $_POST['title'] : '' ,
    'description' => isset($_POST['description']) ? $_POST['description'] : '' ,
    'price' => isset($_POST['price']) ? $_POST['price'] : '' ,
];
$fields = ['title', 'description', 'price'];

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
    if ($_GET['action'] === "edit") {
        $sql = "SELECT * FROM `products` WHERE `id`= ? ";
        $stmt = $connect_db->prepare($sql);
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();

        $result = $stmt->get_result()->fetch_array();

        $product['title'] = $result['title'];
        $product['description'] = $result['description'];
        $product['price'] = $result['price'];
    }

    if (isset($_POST['submit'])) {
        // Check if image file is a actual image or fake image
        $image_checked = imageValidator();

        array_map(function($el) use ($product, &$errors) {
            // Strip HTML and PHP tags from user input
            $product[$el] = $_POST[$el];
            
            // If user input is empty
            if(empty($product[$el]))
            {
                // Save in $_SESSION a specific message
                $errors[] = translate($el . '_required');
            }
        }, $fields);

        // If the image had passed the verification the new product is inserted
        if ($image_checked['upload_ok'] && !count($errors)) {

            // Depends on the action specific code is executed
            if($_GET['action'] === "edit") {
                // Update the product from db
                $sql = "UPDATE `products`  SET `title`= ?, `description`= ?, `price`= ?, `image`= ? WHERE `id`= ?";
                $stmt = $connect_db->prepare($sql);
                $stmt->bind_param("ssdsi", $_POST['title'], $_POST['description'], $_POST['price'], $image_checked["image_name"], $_GET['id']);

                if ($stmt->execute()) {
                    $messages[] = translate('product_updated');
                    header("Location: " . URL . "products.php");
                }
            } else {
                // Insert anew productin in db
                $sql = "INSERT INTO `products` (`title`, `description`, `price`, `image`) VALUES (?,?,?,?)";
                $stmt = $connect_db->prepare($sql);
                $stmt->bind_param("ssds", $_POST['title'], $_POST['description'], $_POST['price'], $image_checked["image_name"]);

                if ($stmt->execute()) {
                    $messages[] = translate('product_created');
                    header("Location: " . $_SERVER['REQUEST_URI']);
                }
            }

        } else {
            $product['title'] = $_POST['title'];
            $product['description'] = $_POST['description'];
            $product['price'] = $_POST['price'];
            $errors[] = translate('image_required');
        }
    }
}

?>

<?php require_once 'layout.php'; ?>
<div class="container">

<form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="form-group" enctype="multipart/form-data">
    <input type="text" placeholder="<?= translate('form_title') ?>" value="<?= $product['title'] ?>" name="title" class="form-control" style='margin:10px 0'>
 
    <input type="text" placeholder="<?= translate('form_description') ?>" value="<?= $product['description'] ?>" name="description" class="form-control" style='margin:10px 0'>

    <input type="number" step="any" placeholder="<?= translate('form_price') ?>" value="<?= $product['price'] ?>"name="price" class="form-control" style='margin:10px 0'>
   
    <input type="file" name="fileToUpload" class="form-control-file" id="formFile" ?>
    <?php showMessages($errors) ?>
    <a href="products.php"  name="submit" class='btn btn-dark' style='margin: 10px'><?= translate('products') ?></a>
    <button type="submit" value="click"  name="submit" class='btn btn-primary' style='margin: 10px'><?= translate('save') ?></button>
</form>
<?php showMessages($messages) ?>
</div>
