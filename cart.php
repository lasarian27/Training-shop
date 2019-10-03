<?php
require_once 'config.php';
require_once 'common.php';

$title = translate('cart_title');

$contact = [
    'name' => isset($_POST['name']) ? $_POST['name'] : '' ,
    'email' => isset($_POST['email']) ? $_POST['email'] : '' ,
    'comments' => isset($_POST['comments']) ? $_POST['comments'] : '' ,
];

$fields = ['name', 'email', 'comments'];
$errors = [];

// Remove a specific id from $_SESSION['cart'] 
if (isset($_GET['action']) && $_GET['action'] === "remove" && isset($_GET['id'])) {
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($el){
        return $el !== $_GET['id'];
    });
}

$products_cart = $_SESSION['cart'];

// If the cart is not empty, get products, with this ids '$_SESSION['cart']' from db
// Otherwise inform the user that the cart is empty
if (count($products_cart)) {
    $sql = "SELECT * FROM products WHERE `id` IN  (".implode(',',$products_cart).")";
    $result = $connect_db->prepare($sql);
    $result->execute();
    $products = $result->get_result();

    if ($products->num_rows) {
        // Output data 
        $cart = $products_cart;
        $pageName = str_replace(['/','.php'], '', $_SERVER['PHP_SELF']);
        
        // Import layout and show products in page
        require_once 'layout.php';
        require_once 'cart_template.php';
    } 

    // If the request is submit, send mail to manager
    if (isset($_POST['submit'])) {
        array_map(function($el) use ($contact, &$errors){
            // Strip HTML and PHP tags from user input
            $contact[$el] = $_POST[$el];

            // If user input is empty
            if(empty($contact[$el]))
            {
                // Save in $errors a specific message
                $errors[] = translate($el . '_required');
            }
        }, $fields);
        
        // Prepare the data for sending the mail
        if (!count($errors)) {
            $to      = MANAGER_EMAIL;
            $name = $_POST['name'];
            $comments =  $_POST['comments'];
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .=  "Content-type: text/html; charset=UTF-8" . "\r\n"; 
            $headers .= "From: " . $_POST['email'] . "\r\n";
            $html = "
            <table class='table'> 
                <thead>
                    <tr>
                        <th scope='col'>" . translate('image') . "</th>
                        <th scope='col'>" . translate('name') . "</th>
                        <th scope='col'>" . translate('description') . "</th>
                        <th scope='col'>" . translate('price') . "</th>
                    </tr>
                </thead>
                <tbody>";
                foreach($products as $product) {
                    $html .= "
                    <tr>
                        <td><img src='" . URL . $product['image'] ."' style='width:50px; height:50px;'/></td>
                        <td>".$product['title'] ."</td>
                        <td>".$product['description']."</td>
                        <td>".$product['price']."$</td>
                    </tr>";
                }
            $html .="<?tbody></table>";

            $_SESSION["messages"][] = mail($to, $name, $comments . $html, $headers) ? translate('mail_sended') : translate('mail_failed');
            $_SESSION['cart'] = [];
            header("Location: " . $_SERVER['REQUEST_URI']);
        }
    }
}
   
?>

<?php if(count($products_cart)): ?>
    <form action="cart.php" method="post">
        <input type="text" placeholder="<?= translate('name') ?>" style="margin-bottom: 10px;" name="name" value="<?= $contact['name'] ?>" class="form-control">
       
        <input type="email" placeholder="<?= translate('contact') ?>" style="margin-bottom: 10px;" name="email" value="<?= $contact['email'] ?>" class="form-control">
       
        <textarea class="form-control" style="margin-bottom: 10px;" name="comments" placeholder="<?= translate('comments') ?>" rows="3" value="<?= $contact['comments'] ?>"></textarea>
        <?php showMessages($errors) ?>

        <button type="submit" value="click" class="btn btn-dark" style="margin: 10px;" name="submit"><?= translate("checkout") ?></button>
    </form>
    <a href="index.php" class="btn btn-dark" style="margin: 10px;"><?= translate("go_home") ?></a>
<?php endif; ?>

<?php if(!isset($_POST['submit']) && !count($products_cart)): ?>
    <?php  require_once 'layout.php'; ?>
    <h5 style='text-align:center'><?= translate('empty_cart') ?></h5>
    <a href='<?= URL ?>' style='display:block' class='btn btn-dark'><?= translate('go_back') ?></a>
<?php endif; ?>

</body>
</html>