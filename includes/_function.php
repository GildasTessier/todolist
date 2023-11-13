<?php
function getUrlWithParam ($urlStart):void {
    $url = $urlStart . '?';
    foreach ($_GET as $key => $value) {
       $url .= $key . '=' . $value . '&';
    }
    $_SESSION['url'] = $url;
}









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
    if (!isset($_SERVER['HTTP_REFERER']) || !str_contains($_SERVER['HTTP_REFERER'], 'localhost/todolist')) {
        $_SESSION['notif'] = 'error_referer';
        header('Location: index.php');
        exit;
    } else if (!isset($_SESSION['token']) || !isset($_REQUEST['token']) || $_REQUEST['token'] !== $_SESSION['token'] || $_SESSION['tokenExpire'] < time()) {
        $_SESSION['notif'] = 'error_token';
        header('Location: index.php');
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
$query = $dbCo->prepare(" UPDATE task SET priority_task = (priority_task - 1) WHERE priority_task > :priority_task;");
$query->execute([
    'priority_task' => $nb['priority_task']
]);
}
function updateNbPriorityCategorie($dbCo):void {
    $query = $dbCo->prepare(" SELECT id_category FROM association WHERE id_task = :id_task;");
    $query->execute([
        'id_task' => intval($_GET['id']),
        ]);
        $idCategory = $query->fetchAll();

        foreach ($idCategory as  $key) {
$query = $dbCo->prepare(" SELECT priority_task FROM association WHERE id_task = :id_task AND id_category = :id_category;");
$query->execute([
    'id_task' => intval($_GET['id']),
    'id_category' => $key['id_category']
    ]);
$nb = $query->fetch();
$query = $dbCo->prepare(" UPDATE association SET priority_task = (priority_task - 1) WHERE priority_task > :priority_task AND id_category = :id_category;");
$query->execute([
    'priority_task' => $nb['priority_task'],
    'id_category' => $key['id_category']
]);
}
}



