<?php
     session_start();
    
    require_once"SpheresPDO.php";
    sleep(1.5);
    header('Content-Type: application/json; charset=utf-8'); 
    
	$returnRow = array();

error_log("before sql");

$sql = "select users.userId AS userID, users.userName AS users, message.content AS content 
			FROM `contactuserlist`
			JOIN message ON contactuserlist.lastMessageId = message.messageId 
			JOIN users ON users.userId = contactuserlist.userId 
			WHERE contactuserlist.contactId = :user 
			AND lastMessageId IS NOT 
			NULL ORDER BY `lastMessageId` DESC;";





//Select contactuserlist.userId AS userId, contactuserlist.contactId AS contactId, contactuserlist.unreadNotifications AS unreadNotifi, users.userName as contactName, users.department as contactDep, users.position as contactPos, users.signInId as contactsignInId FROM contactuserlist JOIN users ON contactuserlist.userId = users.userId WHERE contactuserlist.contactId = :userId ORDER BY unreadNotifi DESC, users.userName ASC";
//CREATE A SEPERATE STATEMENT FOR THE NOTIFICATION NUMBER

$statement = $pdo->prepare(
	            $sql
	        );
$statement->execute(array(

				':user' => $_SESSION['userId'],
			));


/*
error_log("contactListAppend.php sQL ->>>>". $sql);


	        $statement->execute(array( 
	            ':userId' => $_SESSION['userId']
	        ));*/

	        while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) {

				$resultRow['userID'] =  $resultRow['userID'];
				$resultRow['users'] = htmlentities($resultRow['users']);
				$resultRow['content'] = htmlentities($resultRow['content']);
				$returnRow[] = $resultRow;
			}

    echo(json_encode($returnRow));
