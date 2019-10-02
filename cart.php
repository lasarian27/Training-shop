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
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html;charset=iso-8859-1" . "\r\n";
        $headers .= "From: " . $_POST['email'] . "\r\n";

        mail($to, $name, $comments, $headers);
    }

    $cart_products = $_SESSION['cart'];
    if(count($cart_products)){
        $sql = "SELECT * FROM products WHERE `id` IN  (".implode(',',$cart_products).")";
        $result = $connect_db->query($sql);
        
        if ($result->num_rows > 0) {
            // output data of each row
            showProduct($result, $cart_products, str_replace(['/','.php'],'',$_SERVER['PHP_SELF']), $cart_page);
        } 
    }else{
        echo $cart_page['empty_cart'];
        echo "<a href='http://localhost' style='display:block'>" . $cart_page['go_back'] . "</a>";
    }
    
?>