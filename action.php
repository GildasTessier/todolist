<?php
session_start();
require_once './vendor/autoload.php';
require_once './includes/_function.php';
require_once './includes/_dbCo.php';
checkXSS($_POST);
checkXSS($_GET);

checkCSRF('index.php');
$msg = 'nothing';


if((isset($_POST['action']) && ($_POST['action'] === 'connect-acount'))) {

    $query = $dbCo->prepare("SELECT id_user FROM users WHERE mail_user = :mail_user AND password_user = :password_user;");
    $isQueryOk = $query -> execute([
        'mail_user' => $_POST['email'],
        'password_user' => $_POST['password']
        ]);
        $user = $query->fetch();
        $_SESSION['id_user'] = $user['id_user'];
        if(!$isQueryOk || $user['id_user'] == null) $msg = 'connectingError'; 
    }





else if((isset($_POST['action']) && ($_POST['action'] === 'create-acount'))) {
    // CHECK IF THIS EMAIL DOSENT HAVE ALREDY AN ACCOUNT
    $query = $dbCo->prepare("SELECT COUNT(id_user) AS nb_user FROM users WHERE mail_user = :mail_user;");
    $isQueryOk = $query -> execute(
        ['mail_user' => $_POST['new-email']]);
        $nb_user = $query->fetch();
        // IF ALREADY EXIST AN ACCOUNT WHITH THIS MAIL
if($nb_user['nb_user'] != '0' ) $msg = 'alredyAccountMail';

else {
    $query = $dbCo->prepare("INSERT INTO  users (mail_user, password_user) VALUES (:mail_user, :password_user);");
    $isQueryOk = $query -> execute([
        'mail_user' => $_POST['new-email'],
        'password_user' => $_POST['new-password']
    ]);
    if($isQueryOk ) {
        $msg = 'createAccount';
    }
    else {
        $msg = 'createAccountError';
    }
}
}
// FOR DISCONNECT
else if ((isset($_GET['action']) && ($_GET['action'] === 'disconnection'))) {
    $_SESSION['id_user'] = null;
}



// FOR DELETE CATEGORY
else if ((isset($_GET['action']) && ($_GET['action'] === 'deletecategory'))) {
    $query = $dbCo->prepare("DELETE FROM association WHERE id_category = :id_category;
                             DELETE FROM category WHERE id_category = :id_category;");
            $isQueryOk = $query -> execute(
                ['id_category' => $_GET['id_category']]);

                if($isQueryOk ) {
                    $msg = 'deleteCategory';
                }
        
            else {
                $msg = 'deleteCategoryError';
                
            }
}


// FOR ADD NEW CATEGORY
else if(isset($_POST['new-category'])) {
$query = $dbCo->prepare("INSERT INTO category(name_category) VALUES (:new_category)");
            $isQueryOk = $query -> execute(
                ['new_category' => $_POST['new-category']]);

                if($isQueryOk && $query->rowCount()=== 1) {
                    $msg = 'addCategory';
                }
            
            else {
                $msg = 'addCategoryError';
                
            }
        }



// FOR ADD NEW TASK
else if(isset($_POST['add'])) {  

        if(strlen($_POST['name_task']) > 0) {  

            $dbCo->beginTransaction();

            $query = $dbCo->prepare("SELECT (COUNT(id_task)+1) AS row_ FROM task WHERE priority_task IS NOT NULL;");
            $query -> execute();
            $nbRow = $query->fetch();
            
            $nameTask = $_POST['name_task'];
            $dateNow = date('Y-m-d h:i:s');
            $dateAlert = strlen($_POST['alert_date']) === 0 ? NULL : $_POST['alert_date'];

            $query = $dbCo->prepare(" INSERT INTO task (name_task, alert_date, date_create ,state_task, priority_task, color, id_user)
                            VALUES (:name_task, :alert_date, :day_now, 0, :nb_row, :color, :id_user)");
            $isQueryOk = $query->execute([
            'name_task' => $nameTask, 
            'alert_date' => $dateAlert, 
            'day_now' => $dateNow,
            'nb_row' => $nbRow['row_'],
            'color' => $_POST['color'],
            'id_user' => $_SESSION['id_user']
            ]);
            $dbCo->commit();
        
        if($isQueryOk && $query->rowCount()=== 1) {
            $msg = 'addTask';
        };
    }
    else {
        $msg = 'addTaskError';
        
    };
    if(isset($_POST['category'])) {
        $query = $dbCo->prepare("SELECT id_task FROM task WHERE name_task = :name_task AND date_create = :date_create");
        $query->execute([
            'name_task' => $nameTask,
            'date_create' => $dateNow
        ]);
        $idTask = $query->fetch();
        
        foreach ($_POST['category'] as $id ) {
            
            
            $query = $dbCo->prepare("SELECT (COUNT(id_association)+1) AS row_ FROM association WHERE priority_task IS NOT NULL AND id_category = :id_category;");
            $query -> execute([
                'id_category' => $id
            ]);
            $nbRow = $query->fetch();
            
            $query = $dbCo->prepare("INSERT INTO association(id_task, id_category, priority_task) VALUES (:id_task, :id_category, :priority_task)");
            $query->execute([
                'id_task' => $idTask['id_task'],
                'id_category' => $id,
                'priority_task' => $nbRow['row_']
            ]);
        }
        
    }
}

// FOR MODIFY TASK
else if(isset($_POST['modify'])) {   
        if(strlen($_POST['name_task']) > 0) {                        
            $query = $dbCo->prepare(" UPDATE task SET name_task = :name_task   WHERE id_task = :id_task ");
            $isQueryOk = $query->execute([
                'name_task' => $_POST['name_task'], 
                'id_task' => intval($_POST['id']) ]
            );
            
            if($isQueryOk && $query->rowCount()=== 1) {
                $msg='updateTask';
            }
        else {
            $msg = 'addTaskError';
        };
    }
    else {
        $msg = 'addTaskError';
    }
    if(isset($_POST['category'])) {
        $query = $dbCo->prepare("DELETE FROM association WHERE id_task = :id_task");
            $query -> execute([
                'id_task' => intval($_POST['id'])
            ]);
        foreach ($_POST['category'] as $id ) {
            
            $query = $dbCo->prepare("SELECT (COUNT(id_association)+1) AS row_ FROM association WHERE priority_task IS NOT NULL AND id_category = :id_category;");
            $query -> execute([
                'id_category' => $id
            ]);
            $nbRow = $query->fetch();
            
            $query = $dbCo->prepare("INSERT INTO association(id_task, id_category, priority_task) VALUES (:id_task, :id_category, :priority_task)");
            $query->execute([
                'id_task' => intval($_POST['id']),
                'id_category' => $id,
                'priority_task' => $nbRow['row_']
            ]);
        }
        $msg = 'modifyCategory';
    }
}
//FOR UNFINISH TASK
else if (isset($_GET['action']) && $_GET['action'] === 'state_unfinish' && isset($_GET['id'])){
    $query = $dbCo->prepare("SELECT (COUNT(id_task)+1) AS row_ FROM task WHERE priority_task IS NOT NULL;");
    $query -> execute();
    $nbRow = $query->fetch();

    $query = $dbCo->prepare(" UPDATE task SET state_task = 0 , priority_task = :nb_row WHERE id_task = :id_task;");
    $isQueryOk = $query->execute([
        'id_task' => intval($_GET['id']),
        'nb_row' => $nbRow['row_']
    ]);


    $query = $dbCo->prepare(" SELECT id_category FROM association WHERE id_task = :id_task;");
    $query->execute([
        'id_task' => intval($_GET['id']),
        ]);
        $idCategory = $query->fetchAll();

        foreach ($idCategory as  $key) {

$query = $dbCo->prepare(" SELECT (COUNT(id_association)+1) AS row_ FROM association WHERE priority_task IS NOT NULL AND id_category = :id_category;");
$query->execute([
    'id_category' => $key['id_category']
    ]);
$nb = $query->fetch();


$query = $dbCo->prepare("  UPDATE association SET priority_task = :priority_task WHERE id_task = :id_task AND id_category = :id_category;");
$query->execute([
    'priority_task' => $nb['row_'],
    'id_task' => intval($_GET['id']),
    'id_category' => $key['id_category']
]);
}

    if($isQueryOk && $query->rowCount()=== 1) {
        $msg='updateStateTask';
    };
}

//FOR UPDATE STATE TASK
else if (isset($_GET['action']) && $_GET['action'] === 'state_finish' && isset($_GET['id'])){
    
    // FOR CHANDE ORDER WHEN DELETE
    updateNbPriority($dbCo);
    updateNbPriorityCategorie($dbCo);
    $query = $dbCo->prepare(" UPDATE task SET state_task = 1 , priority_task = NULL WHERE id_task = :id_task;
                              UPDATE association SET priority_task = NULL WHERE id_task = :id_task");
    $isQueryOk = $query->execute([
        'id_task' => intval($_GET['id']),
    ]);
    if($isQueryOk && $query->rowCount()=== 1) {
        $msg='updateStateTask';
    };
} 



// FOR CHANGE ORDER
else if (isset($_GET['action']) && ($_GET['action'] === 'up'|| $_GET['action'] === 'down') && isset($_GET['id'])){ 
    // FOR CHANGE ORDER IN LIST BY CATEGORY

    if(isset($_GET['id_category'])) {

        $query = $dbCo->prepare ("SELECT priority_task FROM association WHERE id_task = :id_task AND id_category = :id_category;");
        $isQueryOk = $query->execute([
            'id_task' => intval($_GET['id']),
            'id_category' => $_GET['id_category']]);
        $nbPrioTask = $query->fetch();

        $query = $dbCo->prepare ("SELECT COUNT(id_association) as nb_row FROM association WHERE id_category = :id_category");
        $query->execute(['id_category' => $_GET['id_category']]);
        $nbrow = $query->fetch();

    // FOR UP
        if ($_GET['action'] === 'up' && $nbPrioTask['priority_task'] != 1) {

            $query = $dbCo->prepare("UPDATE association SET priority_task = (priority_task + 1)  WHERE priority_task = (:priority_task - 1) AND id_category = :id_category;
                                    UPDATE association SET priority_task = (priority_task - 1)  WHERE id_task = :id_task AND id_category = :id_category;");

            $isQueryOk = $query->execute([
                'id_task' => intval($_GET['id']),
                'id_category' => $_GET['id_category'],
                'priority_task' => intval($nbPrioTask['priority_task'])]);

            $msg='updatePriority';
        }
    // FOR DOWN
        else if ($_GET['action'] === 'down' && $nbPrioTask['priority_task'] != $nbrow['nb_row']) {
            $query = $dbCo->prepare("UPDATE association SET priority_task = (priority_task - 1)  WHERE priority_task = (:priority_task + 1) AND id_category = :id_category;
                                    UPDATE association SET priority_task = (priority_task + 1)  WHERE id_task = :id_task AND id_category = :id_category;") ;

            $isQueryOk = $query->execute([
                'id_task' => intval($_GET['id']),
                'id_category' => $_GET['id_category'],
                'priority_task' => intval($nbPrioTask['priority_task'])]);
            $msg='updatePriority';
        }

        else {
            $msg = 'nothing';
        }
}

    // FOR CHANGE ORDER GENERAL IN LIST 
    else {
    $query = $dbCo->prepare ("SELECT priority_task FROM task WHERE id_task = :id_task;");
$isQueryOk = $query->execute([
'id_task' => intval($_GET['id'])]);
$nbPrioTask = $query->fetch();

$query = $dbCo->prepare ("SELECT COUNT(id_task) as nb_row FROM task WHERE state_task = 0");
$query->execute();
$nbrow = $query->fetch();

    // FOR UP
    if ($_GET['action'] === 'up' && $nbPrioTask['priority_task'] != 1) {

        $query = $dbCo->prepare("UPDATE task SET priority_task = (priority_task + 1)  WHERE priority_task = (:priority_task - 1);
                                 UPDATE task SET priority_task = (priority_task - 1)  WHERE id_task = :id_task;");

        $isQueryOk = $query->execute([
            'id_task' => intval($_GET['id']),
            'priority_task' => intval($nbPrioTask['priority_task'])
            ]);

        $msg='updatePriority';
    }
    // FOR DOWN
    else if ($_GET['action'] === 'down' && $nbPrioTask['priority_task'] != $nbrow['nb_row']) {
        $query = $dbCo->prepare("UPDATE task SET priority_task = (priority_task - 1)  WHERE priority_task = (:priority_task + 1);
                                 UPDATE task SET priority_task = (priority_task + 1)  WHERE id_task = :id_task;");

        $isQueryOk = $query->execute([
            'id_task' => intval($_GET['id']),
            'priority_task' => intval($nbPrioTask['priority_task'])
            ]);
        $msg='updatePriority';
    }

    else {
        $msg = 'nothing';
    }

}}


// FOR DELETE TASK
else if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])){
    // FOR CHANDE ORDER WHEN DELETE
    updateNbPriority($dbCo);
    updateNbPriorityCategorie($dbCo);
    // FOR DELETE
    $query = $dbCo->prepare(" DELETE FROM task WHERE id_task = :id_task;
                              DELETE FROM association WHERE id_task = :id_task");
    $isQueryOk = $query->execute([
        'id_task' => intval($_GET['id'])
        ]);
        if($isQueryOk && $query->rowCount()=== 1) {
            $msg='deleteTask';
    }
};
$_SESSION['notif'] = $msg;
header('Location: '. $_SESSION['url']);
?>
