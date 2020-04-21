<?php
    session_start();
    
    require_once"SpheresPDO.php";
    sleep(1.5);
    header('Content-Type: application/json; charset=utf-8'); 

	$returnRow = array();

	$sql = "select message.messageId AS mID, messageDate AS mD, messageTime AS mT, content as mC, senderId as mS, groupId as gID FROM message JOIN groupbackup ON message.messageId = groupbackup.messageId WHERE groupId = :group ORDER BY mID ASC;";



		
	$statement = $pdo->prepare(
	            $sql
	        );

	        $statement->execute(array(

	        	':group' => $_SESSION['groupId']

	        
	        ));
	        // statement prepare & execute 

	        while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) {
	        	$resultRow['mID'] = htmlentities($resultRow['mID']);

	        	$resultRow['mD'] = htmlentities($resultRow['mD']);
	        	$resultRow['mT'] = htmlentities($resultRow['mT']);

	        	$resultRow['mC'] = htmlentities($resultRow['mC']);

	        	$resultRow['mS'] = htmlentities($resultRow['mS']);

	        	$resultRow['gID'] = htmlentities($resultRow['gID']);

	        	$returnRow[] = $resultRow;

				error_log("messageID groupMessageList.php". $resultRow['mID']);
        }
        //if > 0 make zero and deduct that value from total unread and from that unread counter 
	if(count($returnRow)!= 0 ){
	error_log("highest MID originally: ".$returnRow[count($returnRow)-1]['mID']);

	$_SESSION['groupHighestMessageId'] = $returnRow[count($returnRow)-1]['mID'];
	}else{
		$_SESSION['groupHighestMessageId'] = 0;
	}

	$sql2 = "select groupuserlist.unreadNotifications AS unreadNotifications FROM groupuserlist WHERE groupId = :group AND userId = :user;";
	$statement = $pdo->prepare(
	    $sql2
	);

	$statement->execute(array(
	  	':group' => $_SESSION['groupId'],
		':user' => $_SESSION['userId']        
	));

	while( $resultCount = $statement->fetch(PDO::FETCH_ASSOC)){
	$returnValue = $resultCount;
	}
	if($returnValue > 0){
		$sql = "update groupuserlist SET groupuserlist.unreadNotifications = groupuserlist.unreadNotifications - :nowSeen WHERE groupuserlist.groupId = " .  $_SESSION['groupId'] . " AND userId = :userId;";

		
		$statement = $pdo->prepare(
			            $sql
	        );
	        $statement->execute(array(//Updates the unread counter
			':userId' => $_SESSION['userId'],
			':nowSeen' => $returnValue['unreadNotifications']
	        ));

$sql2 = "update users SET unreadGroupMessageCount = unreadGroupMessageCount - :nowSeen WHERE userId = :userId;";
		
	    $statement = $pdo->prepare(
			            $sql2
	        );
	        $statement->execute(array(//Updates the total unread counter
			':userId' => $_SESSION['userId'],
			':nowSeen' => $returnValue['unreadNotifications']			
	    ));
}
    echo(json_encode($returnRow));
