<?php
     session_start();
    
    require_once"SpheresPDO.php";
    header('Content-Type: application/json; charset=utf-8'); 

	$returnValue= array();

	$sql = "select contactuserlist.unreadNotifications AS unreadNotifications FROM contactuserlist WHERE contactId = :user AND userId = :contact;";

	$statement = $pdo->prepare(
	    $sql
	);

	$statement->execute(array(
	  	':contact' => $_SESSION['contactId'],
		':user' => $_SESSION['userId']        
	));

	while( $resultRow = $statement->fetch(PDO::FETCH_ASSOC)){
	$returnValue[] = $resultRow;
	error_log("contactMessageListCheck resultRow : ".$resultRow['unreadNotifications']);
	}
	echo(json_encode($returnValue));
