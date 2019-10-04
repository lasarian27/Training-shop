<?php
require_once 'config.php';
require_once DIR . '/translation.php';

// Start php $_SESSION
session_start();

// Make a mysqli connection with credentials from 'config.php'
$db = new mysqli(DB_SERVER_NAME, DB_USERNAME, DB_PASSWORD, DB_NAME);

/**
 * Checking an image by size and formats
 * @return array
 */
function imageValidator()
{
    $target_dir = "images/";
    $target_file = $target_dir . uniqid() . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $errors = [];
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = !$_FILES["fileToUpload"]["error"] ? getimagesize($_FILES["fileToUpload"]["tmp_name"]) : false;

    if ($check !== false) {
        $uploadOk = 1;
        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            $errors[] = translate('file.too.large');
            $uploadOk = 0;
        }
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $errors[] = translate('wrong.format');
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $errors[] = translate('upload.failed');
            // if everything is ok, try to upload file
        } else {
            if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $errors[] = translate('upload.failed');
            }
        }
    } else {
        $uploadOk = 0;
    }

    return [
        'name' => $target_file,
        'upload_ok' => $uploadOk,
        'errors' => $errors
    ];
}

/**
 * Show different messages
 * @param array $data
 */
function showMessages($data)
{
    if ($data) {
        foreach ($data as $item) :
            include_once DIR . '/views/message_template.php';
        endforeach;
    }
}

/**
 * @param string $word
 * @param null $language
 * @return mixed
 */
function translate($word, $language = NULL)
{
    global $translation;

    if (!$language) {
        $language = "en";
    }

    return $translation[$language][$word];
}

/**
 * @return mixed
 */
function getPageName()
{
    return str_replace(['/', '.php'], '', $_SERVER['PHP_SELF']);
}

/**
 * @param string $text
 * @return string
 */
function validation($text)
{
    return htmlentities($text);
}
