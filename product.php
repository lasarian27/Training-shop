<?php
require_once 'languages/en.php'; 
$title = $product_page['title'];
require_once 'layout.php';
require_once 'common.php';

if (!isset($_SESSION["admin"])) 
{
    header("Location: http://localhost");
    exit; // Prevent further execution
}
// Print out all messages from $_SESSION["messages"]
showMessages();

$product = [
    'title' => isset($_POST['title']) ? $_POST['title'] : '' ,
    'description' => isset($_POST['description']) ? $_POST['description'] : '' ,
    'price' => isset($_POST['price']) ? $_POST['price'] : '' ,
];
$fields = ['title', 'description', 'price'];

// Depends on the action do something
switch ($_GET['action']){
    // Create a product
    case 'create':
        if(isset($_POST['submit'])){
            
            // Check if image file is a actual image or fake image
            $image_checked = imageValidator($image_validator_errors);

            array_map(function($el) use ($product, $product_page){
                $product[$el] = strip_tags($_POST[$el]);
                
                if(empty($product[$el]))
                {
                    $_SESSION[$el] = $product_page[$el . '_required'];
                }
            }, $fields);

            // If the image had passed the verification the new product is inserted
            if($image_checked['upload_ok'] && $product['title'] && $product['description'] && $product['price'])
            {
                $sql = "INSERT INTO `products` (`title`, `description`, `price`, `image`) VALUES (?,?,?,?)";
                $stmt = $connect_db->prepare($sql);
                $stmt->bind_param("ssds", $_POST['title'], $_POST['description'], $_POST['price'], $image_checked["image_name"]);
                if($stmt->execute()) {
                    $_SESSION["messages"][] = $product_page['product_created'];
                    header("Location: " . $_SERVER['REQUEST_URI']);
                }else{
                    $_SESSION["messages"][] = $product_page['product_error'];
                }
            }else{
                $_SESSION['image'] = $product_page['image_required'];
            }
        }
                
        break;
    // Update a product
    case 'edit':
        if(isset($_GET['id'])){
            // Get product by id from db
            $id = $_GET['id'];
            $id = mysqli_real_escape_string($connect_db,$id);
            $sql = "SELECT * FROM `products` WHERE `id`='" . $id . "'";
            $result = $connect_db->query($sql);
            $result = $result->fetch_assoc();
            
            $product['title'] = $result['title'];
            $product['description'] = $result['description'];
            $product['price'] = $result['price'];
            $product['image'] = $result['image'];

            if(isset($_POST["submit"]))
            {
                // Check if image file is a actual image or fake image
                $image_checked = imageValidator($image_validator_errors);
                $id = mysqli_real_escape_string($connect_db,$id);
                $sql = "UPDATE `products`  SET `title`=?, `description`=?, `price`=?, `image`=? WHERE `id`='" . $id . "'";
                $stmt = $connect_db->prepare($sql);
        
                // If its a good image
                if($image_checked['upload_ok'])
                {
                    // Delete the first image and assign $product['image'] with new image
                    unlink($product['image']);
                    $product['image'] = $image_checked["image_name"];
                }

                $stmt->bind_param("ssds",
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['price'],
                    $product['image']
                );

                // Check every field to not be empty, if it is save in session an error message
                array_map(function($el) use ($product, $product_page){
                  if(!strip_tags($_POST[$el]))
                    {
                        $_SESSION[$el] = $product_page[$el . '_required'];
                    }
                }, $fields);
         
                // If every field has values 
                if(strip_tags($_POST['title']) && strip_tags($_POST['description']) && strip_tags($_POST['price']))
                {
                    // Update the product with $stmt->execute() and add in session a specific message
                    $_SESSION["messages"][] = $stmt->execute() ?  $product_page['update_product'] : $product_page['update_product_error'];
                    header("Location: " . $_SERVER['REQUEST_URI']);
                }
               
            }
        }
       
        break;
    // Delete a product
    case 'delete':
        if(isset($_GET['id']) && isset($_GET['image'])){
            // Eliminate an image by id
            $id = $_GET['id'];
            $id = mysqli_real_escape_string($connect_db,$id);
            $sql = "DELETE FROM `products` WHERE `id`='" . $id . "'";
            $connect_db->query($sql);
            // Delete the actual image
            unlink($_GET['image']);
            // Redirect to products page
            header("Location: http://localhost/products.php");
        }
        break;
    default:
        break;
    }
  
?>

<div class="container">

<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" class="form-group" enctype="multipart/form-data">
    <input type="text" placeholder="<?php echo $product_page['form_title'] ?>" value="<?php echo $product['title'] ?>" name="title" class="form-control" style='margin:10px 0'>
    <p style="color:red"><?php echo isset($_SESSION['title']) ?  $_SESSION['title'] : ""?></p>

    <input type="text" placeholder="<?php echo $product_page['form_description'] ?>" value="<?php echo $product['description'] ?>" name="description" class="form-control" style='margin:10px 0'>
    <p style="color:red"><?php echo isset($_SESSION['description']) ?  $_SESSION['description'] : ""?></p>

    <input type="number" step="any" placeholder="<?php echo $product_page['form_price'] ?>" value="<?php echo $product['price'] ?>"name="price" class="form-control" style='margin:10px 0'>
    <p style="color:red"><?php echo isset($_SESSION['price']) ?  $_SESSION['price'] : ""?></p>

    <input type="file" name="fileToUpload" class="form-control-file" id="formFile"  ?>
    <p style="color:red"><?php echo isset($_SESSION['image']) ?  $_SESSION['image'] : ""?></p>

    <a href="products.php"  name="submit" class='btn btn-dark' style='margin: 10px'><?php echo $product_page['products'] ?></a>
    <button type="submit" value="click"  name="submit" class='btn btn-primary' style='margin: 10px'><?php echo $product_page['save'] ?></button>
</form>
</div>
<?php 
    unset($_SESSION['title']);
    unset($_SESSION['description']);
    unset($_SESSION['price']);
    unset($_SESSION['image']);
?>