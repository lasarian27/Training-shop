<?php 
require_once 'config.php';
$connect_db = new mysqli(SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

function imageValidator() 
{
    $target_dir = "images/";
    $target_file = $target_dir . uniqid() . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $check = $_FILES["fileToUpload"]["tmp_name"] ? getimagesize($_FILES["fileToUpload"]["tmp_name"]) : false;
    if($check !== false) {
        $uploadOk = 1;
         // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
            // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {            
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
   
    return [
        'image_name'=> $target_file,
        'upload_ok' => $uploadOk
    ];
}

function showProduct($products, $cart, $pageName, $text_page)
{
    echo "<table class='table'> <tbody>";
    while($product = $products->fetch_assoc()) {
        $condition = $pageName === 'index' ? !in_array($product['id'],$cart) : true;
        if($condition){
            echo "
            <tr>
                <td><img src='". $product['image'] ."' style='width:50px; height:50px;'/></td>
                <td>".$product['title'] ."</td>
                <td>".$product['description']."</td>
                <td>".$product['price']."</td>";

                switch ($pageName){
                    case 'index':
                        echo "<td><a href='index.php?action=add&id=" . $product['id'] . "' style='width:50px;'>" . $text_page['add_button'] . "</a></td>";
                    break;
                    case 'products':
                        echo "<td><a href='product.php?action=edit&id=" . $product['id'] . "'style='width:50px;'>" . $text_page['edit'] . "</a></td>" . 
                        "<td><a href='product.php?action=delete&id=" . $product['id'] . "'style='width:50px;'>" . $text_page['delete'] . "</a></td>";
                    break;
                    case 'cart':
                        echo "<td><a href='cart.php?action=remove&id=" . $product['id'] . "'style='width:50px;'>" . $text_page['remove'] . "</a></td>";
                    break;
                    default:
                    break;
                }
            
            echo " </tr>";
        }
    }
    echo "</tbody></table>";
    
    switch ($pageName){
        case 'index':
            echo  "<a href='cart.php' style='display:block'>" . $text_page['go_to_cart'] . "</a>" . 
            "<a href='index.php?action=logout' style='display:block'>" . $text_page['logout'] . "</a>";
            break;
        case 'products':
            echo  "<a href='product.php?action=create' style='display:block'>" . $text_page['add'] . "</a>" . 
            "<a href='index.php?action=logout' style='display:block'>" . $text_page['logout'] . "</a>";
            break;
        case 'cart':
            echo 
            "<form action='cart.php' method='post'>
                <input type='text' placeholder='" . $text_page['name'] . "' name='name' class='form-control' required>

                <input type='email' placeholder='" . $text_page['contact'] . "' name='email' class='form-control' required>
                
                <textarea class='form-control' name='comments' placeholder='" . $text_page['comments'] . "' rows='3'></textarea>

                <button type='submit' value='click' name='submit'>" . $text_page['checkout'] . "</button>
            </form>" . 
            "<a href='index.php' style='display:block'>" . $text_page['go_home'] . "</a>"
            ;
            break;
        default:
            break;
    }
  
}
?>