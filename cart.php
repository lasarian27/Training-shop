<?php
require_once 'languages/en.php';
$title = $cart_page['title'];
require_once 'layout.php';
require_once 'common.php';
require_once 'config.php';

// Print out all messages from $_SESSION["messages"]
showMessages();

$contact = [
    'name' => isset($_POST['name']) ? $_POST['name'] : '' ,
    'email' => isset($_POST['email']) ? $_POST['email'] : '' ,
    'comments' => isset($_POST['comments']) ? $_POST['comments'] : '' ,
];
$fields = ['name', 'email', 'comments'];

// Remove a specific id from $_SESSION['cart'] 
if(isset($_GET['action']) && $_GET['action'] === "remove" && isset($_GET['id']))
{
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($el){
        return $el !== $_GET['id'];
    });
}

$products_cart = $_SESSION['cart'];
// If the cart is not empty, get some products, with this ids '$_SESSION['cart']' from db
// Otherwise inform the user that the cart is empty
if(count($products_cart)){
    $sql = "SELECT * FROM products WHERE `id` IN  (".implode(',',$products_cart).")";
    $result = $connect_db->query($sql);
    
    if ($result->num_rows > 0) {
        // output data of each row
        showProduct($result, $products_cart, str_replace(['/','.php'],'',$_SERVER['PHP_SELF']), $cart_page);
    } 

    // If the request is submit, send mail to manager
    if(isset($_POST['submit'])){
        
        array_map(function($el) use ($contact, $cart_page){
            $contact[$el] = strip_tags($_POST[$el]);
            // If the value of contact[$el] is empty, save the error message in session 
            if(empty($contact[$el]))
            {
                $_SESSION[$el] = $cart_page[$el . '_required'];
            }
        }, $fields);

        // Prepare the data for sending the mail

        $to      = MANAGER_EMAIL;
        $name = $_POST['name'];
        $comments =  $_POST['comments'];
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .=  "Content-type: text/html; charset=UTF-8" . "\r\n"; 
        $headers .= "From: " . $_POST['email'] . "\r\n";
        $html = "<table class='table'> 
            <thead>
                <tr>
                    <th scope='col'>" . $cart_page['image'] . "</th>
                    <th scope='col'>" . $cart_page['name'] . "</th>
                    <th scope='col'>" . $cart_page['description'] . "</th>
                    <th scope='col'>" . $cart_page['price'] . "</th>
                </tr>
            </thead>
            <tbody>";
            foreach($result as $product) {
                $html .= "
                <tr>
                    <td><img src='http://" . SERVER_NAME . "/". $product['image'] ."' style='width:50px; height:50px;'/></td>
                    <td>".$product['title'] ."</td>
                    <td>".$product['description']."</td>
                    <td>".$product['price']."$</td>
                </tr>";
            }
           
        // If all fields are'nt empty send the mail and show a specific message
        if($contact['name'] && $contact['email'] && $contact['comments'])
        {
            $_SESSION["messages"][] = mail($to, $name, $comments . $html, $headers) ? $cart_page['mail_sended'] : $cart_page['mail_failed'];
            header("Location: " . $_SERVER['REQUEST_URI']);
        }
    }
}else{
    echo "<h5 style='text-align:center'>".$cart_page['empty_cart']."</h5>";
    echo "<a href='http://localhost' style='display:block' class='btn btn-dark'>" . $cart_page['go_back'] . "</a>";
}

   
?>
<?php if(count($products_cart)): ?>
    <form action="cart.php" method="post">
        <input type="text" placeholder="<?php echo $cart_page['name'] ?>" style="margin-bottom: 10px;" name="name" value="<?php echo $contact['name'] ?>" class="form-control">
        <p style="color:red"><?php echo isset($_SESSION['name']) ?  $_SESSION['name'] : ""?></p>

        <input type="email" placeholder="<?php echo $cart_page['contact'] ?>" style="margin-bottom: 10px;" name="email" value="<?php echo $contact['email'] ?>" class="form-control">
        <p style="color:red"><?php echo isset($_SESSION['email']) ?  $_SESSION['email'] : ""?></p>

        <textarea class="form-control" style="margin-bottom: 10px;" name="comments" placeholder="<?php echo $cart_page['comments'] ?>" rows="3" value="<?php echo $contact['comments'] ?>"></textarea>
        <p style="color:red"><?php echo isset($_SESSION['comments']) ?  $_SESSION['comments'] : ""?></p>

        <button type="submit" value="click" class="btn btn-dark" style="margin: 10px;" name="submit"><?php echo $cart_page["checkout"] ?></button>
    </form>
    <a href="index.php" class="btn btn-dark" style="margin: 10px;"><?php echo $cart_page["go_home"] ?></a>
<?php 
    unset($_SESSION['name']);
    unset($_SESSION['email']);
    unset($_SESSION['comments']);
    endif;
?>