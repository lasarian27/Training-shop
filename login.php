<?php
require_once 'languages/en.php'; 
$title = $login_page['title'];
require_once 'layout.php';
require_once 'config.php';
session_start();

// Check if the post request was submit
if(isset($_POST['submit']))
{
    // Check credentials
    if($_POST['username'] === USERNAME && $_POST['password'] === PASSWORD){
        $_SESSION['loggedin'] = true;
        $_POST['username'] === 'admin' ? $_SESSION['admin'] = true : '';
        $_SESSION['cart'] = [];
        // Redirect home
        header("Location: http://localhost");
    }else{
        // Show credentials error
        echo $login_page['credentials_error'];
    }
}
?>
<form action="login.php" method="post" style="text-align: center;">
    <input type="text" placeholder="<?php echo $login_page['username_placeholder']?>" class="form-control" style='margin:10px 0' name="username" required>

    <input type="password" placeholder="<?php echo $login_page['password_placeholder']?>" class="form-control" style='margin:10px 0' name="password" required>

    <button type="submit" class='btn btn-success' value="click" style='margin: 10px;' name="submit"><?php echo $login_page['login_button']?></button>
</form>
