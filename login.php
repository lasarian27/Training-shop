<?php

require_once 'config.php';
require_once 'common.php';

$credentials = [
    'username' => isset($_POST['username']) ? $_POST['username'] : '',
    'password' => isset($_POST['password']) ? $_POST['password'] : '',
];
$errors = [];

// Check if the post request was submit
if (isset($_POST['submit'])) {

    foreach ($credentials as $key => $value) {
        // If user input is empty
        if (empty($credentials[$key])) {
            // Save in $_SESSION a specific message
            $errors[] = translate($key . '_required');
        }
    }

    if (!count($errors)) {
        // Check credentials
        if ($credentials['username'] === USERNAME && $credentials['password'] === PASSWORD) {
            $_SESSION['login'] = true;
            $credentials['username'] === 'admin' ? $_SESSION['admin'] = true : '';
            // Redirect home
            header("Location: " . URL);
        } else {
            // Show credentials error
            echo translate('credentials_error');
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= translate('login_title') ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body class="container">
<form action="login.php" method="post" style="text-align: center;">
    <input type="text" placeholder="<?= translate('username_placeholder') ?>" class="form-control" style='margin:10px 0'
           name="username" value="<?= $credentials['username'] ?>">
    <input type="password" placeholder="<?= translate('password_placeholder') ?>" class="form-control"
           style='margin:10px 0' name="password" value="<?= $credentials['password'] ?>">
    <?php showMessages($errors) ?>
    <button type="submit" class='btn btn-success' value="click" style='margin: 10px;'
            name="submit"><?= translate('login_button') ?></button>
    <a href="index.php" class="btn btn-dark" style="margin: 10px;"> <?= translate('go_home') ?> </a>
</form>
</body>
</html>
