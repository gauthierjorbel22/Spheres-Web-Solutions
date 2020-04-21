<?php
    session_start();
    
    require_once"SpheresPDO.php";
    header('Content-Type: application/json; charset=utf-8'); 

	$returnValue= array();

	$sql = "select groupuserlist.unreadNotifications AS unreadNotifications FROM groupuserlist WHERE groupId = :group AND userId = :user;";

	$statement = $pdo->prepare(
	    $sql
	);

	$statement->execute(array(
	  	':group' => $_SESSION['groupId'],
		':user' => $_SESSION['userId']        
	));

	while( $resultRow = $statement->fetch(PDO::FETCH_ASSOC)){
	$returnValue[] = $resultRow;
	error_log("groupMessageListCheck resultRow : ".$resultRow['unreadNotifications']);
	}
	echo(json_encode($returnValue));
