<?php


/**
 * Function for generate token 
 *
 * @return void
 */
function generateToken ():void {
if(!isset($_SESSION['token'])) {
$_SESSION['token'] = md5(uniqid(mt_rand(), true));
}};

/**
 * Function to check url
 *
 * @param string $url
 * @return void
 */
function checkCSRF(string $url): void
{
    if (!isset($_SERVER['HTTP_REFERER']) || !str_contains($_SERVER['HTTP_REFERER'], 'http://localhost/todolist')) {
        header('Location: ' . $url . '?error=error_referer');
        exit;
    } else if (!isset($_SESSION['token']) || !isset($_REQUEST['token']) || $_REQUEST['token'] !== $_SESSION['token'] || $_SESSION['tokenExpire'] < time()) {
        header('Location: ' . $url . '?error=error_token');
        exit;
    }
}



