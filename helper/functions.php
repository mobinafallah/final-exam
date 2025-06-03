<?php 
function view($filename, $data = [])
{
    if (file_exists(ROOT_PATH . '/Views/' . $filename)) {
        extract($data); 
        return require_once(ROOT_PATH . '/Views/' . $filename);
    }

    return require_once(ROOT_PATH . '/Views/404.php');
}

function redirect($url)
{
    header("Location: $url");
    exit();
}
function isloggedin()
{
    return isset($_SESSION['user']);
}
?>