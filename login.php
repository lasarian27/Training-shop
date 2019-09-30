<?php
$data = [];
$title = 'Login page';
require_once('layout.php');
require_once('config.php');
session_start();

function checkCredentials() {
    if($_POST['username'] === USERNAME && $_POST['password'] === PASSWORD){
        $_SESSION['loggedin'] = true;
        $_SESSION['admin'] = $_POST['username'] === 'sadmin';
        header("Location: http://localhost");
    }else{
        echo "Credentials are wrong";
        $data[] = "Something went wrong";
    }
}

if(isset($_POST['submit']))
{
    checkCredentials();
}
?>
<form action="login.php" method="post">
    <input type="text" placeholder="Enter Username" name="username" required>

    <input type="password" placeholder="Enter Password" name="password" required>

    <button type="submit" value="click" name="submit">Login</button>
</form>
