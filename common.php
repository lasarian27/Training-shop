<?php 
require_once 'config.php';
$connect_db = new mysqli(SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

function imageValidator($image_validator_errors) 
{
    $target_dir = "images/";
    $target_file = $target_dir . uniqid() . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $check = $_FILES["fileToUpload"]["tmp_name"] ? getimagesize($_FILES["fileToUpload"]["tmp_name"]) : false;
    if($check !== false) {
        $uploadOk = 1;
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            echo $image_validator_errors['filte_too_large'];
            $uploadOk = 0;
        }
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo $image_validator_errors['wrong_format'];
            $uploadOk = 0;
        }
            // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo $image_validator_errors['upload_failed'];
        // if everything is ok, try to upload file
        } else {
            if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {            
                echo $image_validator_errors['upload_failed'];
            }
        }
    } else {
        echo $image_validator_errors['image_error'];
        $uploadOk = 0;
    }
   
    return [
        'image_name'=> $target_file,
        'upload_ok' => $uploadOk
    ];
}

function showProduct($products, $cart, $pageName, $text_page)
{
    echo "<table class='table'> 
            <thead>
                <tr>
                    <th scope='col'>Image</th>
                    <th scope='col'>Name</th>
                    <th scope='col'>Description</th>
                    <th scope='col'>Price</th>
                    <th scope='col'>Actions</th>
                </tr>
            </thead>
            <tbody>";
    foreach($products as $product) {
        $condition = $pageName === 'index' ? !in_array($product['id'],$cart) : true;
        if($condition){
            echo "
            <tr>
                <td><img src='". $product['image'] ."' style='width:50px; height:50px;'/></td>
                <td>".$product['title'] ."</td>
                <td>".$product['description']."</td>
                <td>".$product['price']."$</td>";

                switch ($pageName){
                    case 'index':
                        echo "<td><a href='index.php?action=add&id=" . $product['id'] . "' class='btn btn-primary'>" . $text_page['add_button'] . "</a></td>";
                    break;
                    case 'products':
                        echo "<td><a href='product.php?action=edit&id=" . $product['id'] . "'class='btn btn-info'>" . $text_page['edit'] . "</a></td>" . 
                        "<td><a href='product.php?action=delete&id=" . $product['id'] . "&image=". $product['image'] . "'class='btn btn-danger'>" . $text_page['delete'] . "</a></td>";
                    break;
                    case 'cart':
                        echo "<td><a href='cart.php?action=remove&id=" . $product['id'] . "'class='btn btn-danger'>" . $text_page['remove'] . "</a></td>";
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
            echo  "<a href='cart.php' class='btn btn-dark' style='margin: 10px;'>" . $text_page['go_to_cart'] . "</a>" . 
            "<a href='index.php?action=logout' class='btn btn-dark' style='margin: 10px;'>" . $text_page['logout'] . "</a>";
            break;
        case 'products':
            echo  "<a href='product.php?action=create' class='btn btn-primary' style='margin: 10px;'>" . $text_page['add'] . "</a>" . 
            "<a href='index.php?action=logout' class='btn btn-dark' style='margin: 10px;''>" . $text_page['logout'] . "</a>";
            break;
        case 'cart':
            echo 
            "<form action='cart.php' method='post'>
                <input type='text' placeholder='" . $text_page['name'] . "' style='margin-bottom: 10px;' name='name' class='form-control' required>

                <input type='email' placeholder='" . $text_page['contact'] . "' style='margin-bottom: 10px;' name='email' class='form-control' required>
                
                <textarea class='form-control' style='margin-bottom: 10px;' name='comments' placeholder='" . $text_page['comments'] . "' rows='3' required></textarea>

                <button type='submit' value='click' class='btn btn-dark' style='margin: 10px;' name='submit'>" . $text_page['checkout'] . "</button>
            </form>" . 
            "<a href='index.php' class='btn btn-dark' style='margin: 10px;'>" . $text_page['go_home'] . "</a>"
            ;
            break;
        default:
            break;
    }
  
}
?>