<?php

require_once '../config.php';
require_once DIR . '/common.php';

$title = translate('login.title');
$credentials = [
    'username' => isset($_POST['username']) ? $_POST['username'] : '',
    'password' => isset($_POST['password']) ? $_POST['password'] : '',
];
$errors = [
    'username' => [],
    'password' => []
];

// Check if the post request was submit
if (isset($_POST['submit'])) {

    foreach ($credentials as $key => $value) {
        // If user input is empty
        if (empty($credentials[$key])) {
            // Save in $_SESSION a specific message
            $errors[$key][] = translate($key . '.required');

        }
    }

    if (!$errors['username'] && !$errors['password']) {
        // Check credentials
        if ($credentials['username'] === USERNAME && $credentials['password'] === PASSWORD) {
            $_SESSION['login'] = true;

            if ($credentials['username'] === 'admin') {
                $_SESSION['admin'] = true;
            }

            // Redirect home
            header("Location: " . URL);
        } else {
            // Show credentials error
            echo translate('credentials.error');
        }
    }
}

?>
<?php require_once DIR . "/views/header.php" ?>

<form action="login.php" method="post" class="text-center">
    <input type="text" placeholder="<?= validation(translate('username.placeholder')) ?>" class="form-control"
           style='margin:10px 0'
           name="username" value="<?= $credentials['username'] ?>">
    <?php showMessages($errors['username']) ?>

    <input type="password" placeholder="<?= validation(translate('password.placeholder')) ?>" class="form-control"
           style='margin:10px 0' name="password" value="<?= $credentials['password'] ?>">
    <?php showMessages($errors['password']) ?>

    <button type="submit" class='btn btn-success' value="click" style='margin: 10px;'
            name="submit"><?= validation(translate('login.button')) ?></button>
    <a href="index.php" class="btn btn-dark" class="m-1"> <?= validation(translate('go.home')) ?> </a>
</form>

<?php require_once DIR . "/views/footer.php" ?>
