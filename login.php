<?php
require_once 'languages/en.php'; 
require_once 'layout.php';
require_once 'config.php';
session_start();

function checkCredentials() {
    if($_POST['username'] === USERNAME && $_POST['password'] === PASSWORD){
        $_SESSION['loggedin'] = true;
        $_POST['username'] === 'admin' ? $_SESSION['admin'] = true : '';
        $_SESSION['cart'] = [];
        header("Location: http://localhost");
    }else{
        echo $credentials_error;
    }
}

if(isset($_POST['submit']))
{
    checkCredentials();
}
?>
<form action="login.php" method="post">
    <input type="text" placeholder="<?php echo $username_placeholder?>" class="form-control" name="username" required>

    <input type="password" placeholder="<?php echo $password_placeholder?>" class="form-control" name="password" required>

    <button type="submit" class='btn btn-success' value="click" name="submit"><?php echo $login_button?></button>
</form>
