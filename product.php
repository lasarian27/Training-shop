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
$params = ['title', 'description', 'price', 'image'];

switch ($_GET['action']){
    case 'create':
        if(isset($_POST['submit'])){
            // Check if image file is a actual image or fake image
            $image_checked = imageValidator($image_validator_errors);

            if($image_checked['upload_ok'])
            {
                $sql = "INSERT INTO `products` (`title`, `description`, `price`, `image`) VALUES (?,?,?,?)";
                $stmt = $connect_db->prepare($sql);
                $stmt->bind_param("ssds", $_POST['title'], $_POST['description'], $_POST['price'], $image_checked["image_name"]);
                echo $stmt->execute() ?  $product_page['product_created'] : $product_page['product_error'];
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
                $image_checked = imageValidator($image_validator_errors);
                $id = mysqli_real_escape_string($connect_db,$id);
                $sql = "UPDATE `products`  SET `title`=?, `description`=?, `price`=?, `image`=? WHERE `id`='" . $id . "'";
                $stmt = $connect_db->prepare($sql);
         
                if($image_checked['upload_ok'])
                {
                    unlink($product_image);
                    $product_image = $image_checked["image_name"];
                }

                $stmt->bind_param("ssds",
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['price'],
                    $product_image
                );
                echo $stmt->execute() ?  $product_page['update_product'] : $product_page['update_product_error'];
            }
               
        }
       
        break;
    case 'delete':
        if(isset($_GET['id']) && isset($_GET['image'])){
            $id = $_GET['id'];
            $id = mysqli_real_escape_string($connect_db,$id);
            $sql = "DELETE FROM `products` WHERE `id`='" . $id . "'";
            $connect_db->query($sql);
            unlink($_GET['image']);
            header("Location: http://localhost/products.php");
        }

        break;
    default:
        break;
    }
?>

<div class="container">

<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" class="form-group" enctype="multipart/form-data">
    <input type="text" placeholder="<?php echo $product_page['form_title'] ?>" value="<?php echo $product_title ?>" name="title" class="form-control" style='margin:10px 0' required>
    <input type="text" placeholder="<?php echo $product_page['form_description'] ?>" value="<?php echo $product_description ?>" name="description" class="form-control" style='margin:10px 0' required>

    <input type="number" step="any" placeholder="<?php echo $product_page['form_price'] ?>" value="<?php echo $product_price ?>"name="price" class="form-control" style='margin:10px 0' required>
    
    <input type="file" name="fileToUpload" class="form-control-file" id="formFile" <?php if($_GET['action'] === "create") echo "required" ?>  >

    <a href="products.php"  name="submit" class='btn btn-dark' style='margin: 10px'><?php echo $product_page['products'] ?></a>
    <button type="submit" value="click"  name="submit" class='btn btn-primary' style='margin: 10px'><?php echo $product_page['save'] ?></button>
</form>
</div>