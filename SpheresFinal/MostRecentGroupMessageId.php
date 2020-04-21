<?php
     session_start();
    
    require_once"SpheresPDO.php";
    header('Content-Type: application/json; charset=utf-8'); 

	$returnValue= array();

	
$sql = "select users.latestGroupMessageID AS mostRecent FROM `users` WHERE users.userId = :user;";

$statement = $pdo->prepare(
	$sql
);
error_log("SQL for retrieval : ".$sql);

$statement->execute(array(
':user' => $_SESSION['userId'],
));

while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) {
$_SESSION["lastGroupMessageID"] = $resultRow["mostRecent"];
}

$returnValue[0] =$_SESSION["lastGroupMessageID"] ;


	echo(json_encode($returnValue));
