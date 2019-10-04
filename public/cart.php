<?php
require_once '../config.php';
require_once DIR . '/common.php';

$contact = [
    'name' => isset($_POST['name']) ? $_POST['name'] : '',
    'email' => isset($_POST['email']) ? $_POST['email'] : '',
    'comments' => isset($_POST['comments']) ? $_POST['comments'] : ''
];

$errors = [
    'name' => [],
    'email' => [],
    'comments' => []
];

// Remove a specific id from $_SESSION['cart'] 
if (isset($_GET['action']) && $_GET['action'] === "remove" && isset($_GET['id'])) {
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function ($el) {
        return $el !== $_GET['id'];
    });
}

if (isset($_GET['status'])) {
    if ($_GET['status']) {
        $_SESSION['cart'] = [];
        echo validation(translate('mail.sent'));
    } else {
        echo validation(translate('mail.failed'));
    }
}

$products_cart = $_SESSION['cart'];

// If the cart is not empty, get products, with this ids '$_SESSION['cart']' from db
// Otherwise inform the user that the cart is empty
if ($products_cart) {
    $param_values = [];
    $param_type = str_repeat('i', count($products_cart));
    $param_values[] = &$param_type;

    foreach ($products_cart as $key => $value) {
        $param_values[] = &$products_cart[$key];
    }

    $sql = "SELECT * FROM `products` WHERE `id` IN  (" . str_repeat('? , ', count($products_cart) - 1) . " ? " . ")";
    $result = $db->prepare($sql);

    call_user_func_array(array($result, 'bind_param'), $param_values);
    $result->execute();
    $products = $result->get_result();

    // If the request is submit, send mail to manager
    if (isset($_POST['submit'])) {

        foreach ($contact as $key => $value) {
            if (!strlen($contact[$key])) {
                // Save in $_SESSION a specific message
                $errors[$key][] = translate($key . '.required');
            }

            if ($key === "email" && strlen($contact[$key]) && !filter_var($contact[$key], FILTER_VALIDATE_EMAIL)) {
                $errors[$key] = translate($key . '.required');
            }
        }

        // Prepare the data for sending the mail
        if (!$errors['name'] && !$errors['email'] && !$errors['comments']) {
            $name = $_POST['name'];
            $comments = $_POST['comments'];

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
            $headers .= "From: " . $_POST['email'] . "\r\n";

            $html = "
            <br />
            <br />
            <table class=\"table\"> 
                <thead>
                    <tr>
                        <th scope=\"col\">" . validation(translate('image')) . "</th>
                        <th scope=\"col\">" . validation(translate('name')) . "</th>
                        <th scope=\"col\">" . validation(translate('description')) . "</th>
                        <th scope=\"col\">" . validation(translate('price')) . "</th>
                    </tr>
                </thead>
                <tbody>";

            foreach ($products as $product) {
                $html .= "
                    <tr>
                        <td><img src=\"" . URL . validation($product['image']) . "\" style=\"width:50px; height:50px;\"/></td>
                        <td>" . validation($product['title']) . "</td>
                        <td>" . validation($product['description']) . "</td>
                        <td>" . validation($product['price']) . "$</td>
                    </tr>";
            }

            $html .= "</tbody></table>";

            $mail_sent = mail(MANAGER_EMAIL, $name, $comments . $html, $headers);
            header("Location: " . $_SERVER['REQUEST_URI'] . "?status=" . $mail_sent);
        }
    }
}

?>

<?php
require_once DIR . "/views/header.php";

if ($products_cart):
    require_once DIR . '/views/show_products.php'; ?>

    <form action="cart.php" method="post">
        <input type="text" placeholder="<?= validation(translate('name')) ?>" style="margin-bottom: 10px;" name="name"
               value="<?= validation($contact['name']) ?>" class="form-control">
        <?php showMessages($errors['name']) ?>

        <input type="email" placeholder="<?= validation(translate('contact')) ?>" style="margin-bottom: 10px;"
               name="email"
               value="<?= validation($contact['email']) ?>" class="form-control">
        <?php showMessages($errors['email']) ?>

        <textarea class="form-control" style="margin-bottom: 10px;" name="comments"
                  placeholder="<?= validation(translate('comments')) ?>" rows="3"
                  value="<?= validation($contact['comments']) ?>"></textarea>
        <?php showMessages($errors['comments']) ?>

        <button type="submit" value="click" class="btn btn-dark" style="margin: 10px;"
                name="submit"><?= validation(translate("checkout")) ?></button>
    </form>
    <a href="index.php" class="btn btn-dark" style="margin: 10px;"><?= validation(translate("go.home")) ?></a>
<?php endif; ?>

<?php if (!isset($_POST['submit']) && !$products_cart): ?>
    <h5 style="text-align:center"><?= validation(translate('empty.cart')) ?></h5>
    <a href="<?= URL ?>" style="display:block" class="btn btn-dark"><?= validation(translate('go.back')) ?></a>
<?php endif; ?>

<?php require_once DIR . "/views/footer.php" ?>
