<?php
require_once 'languages/en.php'; 
$title = $login_page['title'];
require_once 'layout.php';
require_once 'config.php';
require_once 'common.php';


$username = '';
$password = '';

// Check if the post request was submit
if(isset($_POST['submit']))
{
    $username = strip_tags($_POST['username']);
    $password = strip_tags($_POST['password']);

    unset($_SESSION['username']);
    unset($_SESSION['password']);

    // Check every field in particular
    if(empty($username)){
        $_SESSION['username']= $login_page['username_required'];
    }

    if(empty($password)){
        $_SESSION['password']= $login_page['password_required'];
    }

    // Check credentials
    if(!empty($username) && !empty($password))
    {
        if($username === USERNAME && $password === PASSWORD){
            $_SESSION['loggedin'] = true;
            $username === 'admin' ? $_SESSION['admin'] = true : '';
            $_SESSION['cart'] = [];
            // Redirect home
            header("Location: http://localhost");
        }else{
            // Show credentials error
            echo $login_page['credentials_error'];
        }
    }
   
}
?>

<form action="login.php" method="post" style="text-align: center;">
    <input type="text" placeholder="<?php echo $login_page['username_placeholder']?>" class="form-control" style='margin:10px 0' name="username" value="<?php echo $username ?>">
    <p style="color:red"><?php echo isset($_SESSION['username']) ?  $_SESSION['username'] : ""?></p>
    <input type="password" placeholder="<?php echo $login_page['password_placeholder']?>"  class="form-control" style='margin:10px 0' name="password" value="<?php echo $password ?>">
    <p style="color:red"><?php echo isset($_SESSION['password']) ?  $_SESSION['password'] : ""?></p>
    <button type="submit" class='btn btn-success' value="click" style='margin: 10px;' name="submit"><?php echo $login_page['login_button']?></button>
</form>