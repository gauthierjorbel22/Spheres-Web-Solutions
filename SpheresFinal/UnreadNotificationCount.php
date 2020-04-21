<?php
     session_start();
    
    require_once"SpheresPDO.php";
    header('Content-Type: application/json; charset=utf-8'); 

	$returnValue= array();

	
$sql = "select users.unreadPrivateMessageCount AS unreadPrivate, users.unreadGroupMessageCount AS unreadGroup FROM `users` WHERE users.userId = :user;";

$statement = $pdo->prepare(
	$sql
);
error_log("SQL for retrieval : ".$sql);

$statement->execute(array(
':user' => $_SESSION['userId'],
));

while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) {
$returnValue[0] =$resultRow["unreadPrivate"];
$returnValue[1] =$resultRow["unreadGroup"];
}




	echo(json_encode($returnValue));
