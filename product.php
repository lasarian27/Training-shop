<?php
$title = 'Product page';
require_once('layout.php');
['connectDB' => $connect_db] = require_once('common.php');
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $target_dir = "images/";
    $target_file = $target_dir . uniqid() . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
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
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    /*$sql = "INSERT INTO products (title, description, price, image) VALUES (?,?,?)";
    $stmt = mysqli_prepare($sql);
    $stmt->bind_param("sss", $_POST['title'], $_POST['description'], $_POST['price'], $target_file);
    $stmt->execute();*/
}
?>

<div class="container">

<form action="product.php" method="post" class="form-group" enctype="multipart/form-data">
    <input type="text" placeholder="Title" name="title" class="form-control" required>

    <input type="text" placeholder="Description" name="description" class="form-control" required>

    <input type="number" placeholder="Price" name="price" class="form-control" required>
    
    <input type="file" name="fileToUpload" class="form-control-file" id="formFile">

    <a href="index.php"  name="submit">Products</a>
    <button type="submit" value="click"  name="submit">Save</button>
</form>
</div>