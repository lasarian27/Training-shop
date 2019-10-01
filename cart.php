<?php
    require_once 'languages/en.php'; 
    $title = $cart_page['title'];
    require_once 'layout.php';
    require_once 'common.php';
    session_start();
    
    if(isset($_GET['action']) && $_GET['action'] === "remove" && isset($_GET['id']))
    {
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($el){
            return $el !== $_GET['id'];
        });
    }
    if(isset($_POST['submit'])){
        $to      = MANAGER_EMAIL;
        $name = $_POST['name'];
        $comments =  $_POST['comments'];
        $headers = 'From: '. $_POST['email'] . "\r\n" .
            'Reply-To: '. $_POST['email'] . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $name, $comments, $headers);
    }

    $array = $_SESSION['cart'];
    if(count($array)){
        $sql = "SELECT * FROM products WHERE `id` IN  (".implode(',',$array).")";
        $result = $connect_db->query($sql);
        
        if ($result->num_rows > 0) {
            // output data of each row
            showProduct($result, $_SESSION['cart'], str_replace(['/','.php'],'',$_SERVER['PHP_SELF']), $cart_page);
        } 
    }else{
        echo $cart_page['empty_cart'];
        echo "<a href='http://localhost' style='display:block'>" . $cart_page['go_back'] . "</a>";
    }
    
?>