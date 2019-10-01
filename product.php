<?php
require_once 'languages/en.php'; 
$title = $product_page['title'];
require_once 'layout.php';
require_once 'common.php';
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: http://localhost");
    exit; // prevent further execution
}

$product_title = '';
$product_description = '';
$product_price = '';

switch ($_GET['action']){
    case 'create':
        if(isset($_POST['submit'])){
            // Check if image file is a actual image or fake image
            $image_checked = imageValidator();

            if($image_checked['upload_ok'])
            {
                $sql = "INSERT INTO `products` (`title`, `description`, `price`, `image`) VALUES (?,?,?,?)";
                $stmt = $connect_db->prepare($sql);
                $stmt->bind_param("ssds", $_POST['title'], $_POST['description'], $_POST['price'], $image_checked["image_name"]);
                echo $stmt->execute() ?  "The product was inserted in db" : "There was an error on trying to insert the new product in db";
            }
        }
        break;
    case 'edit':
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $id = mysqli_real_escape_string($connect_db,$id);
            $sql = "SELECT * FROM `products` WHERE `id`='" . $id . "'";
            $result = $connect_db->query($sql);
            $result = $result->fetch_assoc();
            $product_title = $result['title'];
            $product_description = $result['description'];
            $product_price = $result['price'];
            $product_image = $result['image'];

            if(isset($_POST["submit"]))
            {
                $image_checked = imageValidator();
                $id = mysqli_real_escape_string($connect_db,$id);
                $sql = "UPDATE `products`  SET `title`=?, `description`=?, `price`=?, `image`=? WHERE `id`='" . $id . "'";
                $stmt = $connect_db->prepare($sql);
         
                $product_image = $image_checked['upload_ok'] ? 
                $image_checked["image_name"] : $product_image;

                $stmt->bind_param("ssds", 
                    $_POST['title'], 
                    $_POST['description'], 
                    $_POST['price'], 
                    $product_image
                );
                echo $stmt->execute() ?  $product_page['good_message'] : $product_page['wrong_message'];
            }
               
        }
       
        break;
    case 'delete':
        if(isset($_GET['id'])){
            $id = $_GET['id'];
            $id = mysqli_real_escape_string($connect_db,$id);
            $sql = "DELETE FROM `products` WHERE `id`='" . $id . "'";
            $connect_db->query($sql);
           
            header("Location: http://localhost/products.php");
        }

        break;
    default:
        break;
    }
?>

<div class="container">

<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" class="form-group" enctype="multipart/form-data">
    <input type="text" placeholder="<?php echo $product_page['form_title'] ?>" value="<?php echo $product_title ?>" name="title" class="form-control" required>
    <input type="text" placeholder="<?php echo $product_page['form_description'] ?>" value="<?php echo $product_description ?>" name="description" class="form-control" required>

    <input type="number" step="any" placeholder="<?php echo $product_page['form_price'] ?>" value="<?php echo $product_price ?>"name="price" class="form-control" required>
    
    <input type="file" name="fileToUpload" class="form-control-file" id="formFile">

    <a href="products.php"  name="submit"><?php echo $product_page['products'] ?></a>
    <button type="submit" value="click"  name="submit"><?php echo $product_page['save'] ?></button>
</form>
</div>