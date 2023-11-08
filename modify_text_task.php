<?php


if(isset($_POST['task'])) {  
    if(isset($_POST['token']) && isset($_SESSION['token']) && $_SESSION['token'] === $_POST['token']) {

        if(strlen($_POST['task']) > 0) {                        
$query = $dbCo->prepare(" UPDATE task SET name_task = :name_task   WHERE id_task = :id ");
            $isQueryOk = $query->execute([
            'name_task' => strip_tags($_POST['task']), 
            'id' => intval($_POST['id']) ]

        );
            if($isQueryOk && $query->rowCount()=== 1) {
                $msg = 'La tache à été ajouté à la liste';
            };
    }
        else {
            $msg = 'Nom de tache vide';
        };
    }
    else {
        $msg = 'Token non valide ';
    }
}
?>