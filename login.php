<?php

require_once 'config.php';
require_once 'common.php';

$title = translate('login_title');
$credentials = [
    'username' => isset($_POST['username']) ? $_POST['username'] : '' ,
    'password' => isset($_POST['password']) ? $_POST['password'] : '' ,
];
$fields = ['username', 'password'];
$errors = [];

// Check if the post request was submit
if (isset($_POST['submit'])) {
    array_map(function($el) use ($credentials, &$errors){
        // Strip HTML and PHP tags from user input
        $credentials[$el] = strip_tags($_POST[$el]);
        
        // If user input is empty
        if (empty($credentials[$el]))
        {
            // Save in $errors a specific message
            $errors[] = translate($el . '_required');
        }
    }, $fields);
    
    if (!count($errors)) {
        // Check credentials
        if ($credentials['username'] === USERNAME && $credentials['password'] === PASSWORD) {
            $_SESSION['loggedin'] = true;
            $credentials['username'] === 'admin' ? $_SESSION['admin'] = true : '';
            // Redirect home
            header("Location: " . URL);
        } else {
            // Show credentials error
            echo translate('credentials_error');
        }
    }
}

require_once 'layout.php';
?>

<form action="login.php" method="post" style="text-align: center;">
    <input type="text" placeholder="<?= translate('username_placeholder')?>" class="form-control" style='margin:10px 0' name="username" value="<?= $credentials['username'] ?>">
    <input type="password" placeholder="<?= translate('password_placeholder') ?>"  class="form-control" style='margin:10px 0' name="password" value="<?= $credentials['password'] ?>">
    <?php showMessages($errors) ?>
    <button type="submit" class='btn btn-success' value="click" style='margin: 10px;' name="submit"><?= translate('login_button') ?></button>
    <a href="index.php" class="btn btn-dark" style="margin: 10px;"> <?= translate('go_home') ?> </a>
</form>

</body>
</html>
