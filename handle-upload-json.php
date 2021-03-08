<?php
session_start();
// read request json file
$json_file = $_FILES['json_file'];
$json_file_name = $json_file['name'];
$json_file_tmp_name = $json_file['tmp_name'];
$json_file_error = $json_file['error'];
$json_file_ext = pathinfo($json_file_name, PATHINFO_EXTENSION);

// request errors array
$errors = [];

// validation rules
if ($json_file_error !== 0) {
    $errors[] = 'an error occurred when uploading file';
} elseif (!in_array($json_file_ext, ['json'])) {
    $errors[] = 'invalid file extension, allowed file extensions: .json';
}

// handling data
if (empty($errors)) {
    // move file to project directory
    $randomString = uniqid();
    $fileNewName = "$randomString.$json_file_ext";
    move_uploaded_file($json_file_tmp_name, $fileNewName);
    // reading file contents
    $fileContentsResource = fopen($fileNewName, 'r');
    $fileSize = filesize($fileNewName);
    $fileContents = fread($fileContentsResource, $fileSize);
    $data = json_decode($fileContents);
    // set data in session
    $_SESSION['data'] = $data;
    header("location: display.php");
} else {
    // set errors in session
    $_SESSION['errors'] = $errors;
    header("location: upload-json.php");
}