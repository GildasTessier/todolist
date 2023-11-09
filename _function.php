<?php
// require_once './dbCo.php';

/**
 * Function for generate token 
 *
 * @return void
 */
function generateToken ():void {
if(!isset($_SESSION['token'])|| !isset($_SESSION['tokenExpire'])|| $_SESSION['tokenExpire'] < time() ) {
$_SESSION['token'] = md5(uniqid(mt_rand(), true));
$_SESSION['tokenExpire'] =  time() +  15 * 60;
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
        header('Location: ' . $url . '?notif=error_referer');
        exit;
    } else if (!isset($_SESSION['token']) || !isset($_REQUEST['token']) || $_REQUEST['token'] !== $_SESSION['token'] || $_SESSION['tokenExpire'] < time()) {
        header('Location: ' . $url . '?notif=error_token');
        exit;
    }
};
/**
 * Function for update all nb_task priority 
 *
 * @param [type] $dbCo
 * @return void
 */
function updateNbPriority($dbCo):void {
$query = $dbCo->prepare(" SELECT priority_task FROM task WHERE id_task = :id_task;");
$query->execute([
    'id_task' => intval($_GET['id'])
    ]);
$nb = $query->fetch();
var_dump($nb['priority_task']);
$query = $dbCo->prepare(" UPDATE task SET priority_task = (priority_task - 1) WHERE priority_task > :priority_task;");
$query->execute([
    'priority_task' => $nb['priority_task']
]);
}



