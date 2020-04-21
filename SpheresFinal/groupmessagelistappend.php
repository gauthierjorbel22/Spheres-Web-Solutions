<?php
     session_start();
    
    require_once"SpheresPDO.php";
    sleep(1.5);
    header('Content-Type: application/json; charset=utf-8'); 
    
	$returnRow = array();

	$sql = "select message.messageId AS mID, messageDate AS mD, messageTime AS mT, content as mC, senderId as mS, groupId as gID FROM message JOIN groupbackup ON message.messageId = groupbackup.messageId WHERE groupId = :group AND message.messageId > :lastGreatest ORDER BY message.messageId ASC;";

	
	$statement = $pdo->prepare(
	            $sql
	        );

	        $statement->execute(array(

	        	':group' => $_SESSION['groupId'],
				':lastGreatest' => $_SESSION['groupHighestMessageId']
	        ));
error_log("previous highest MID appended is seen to be: ".$_SESSION['groupHighestMessageId']);

	        while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) {
	        	$resultRow['mID'] = htmlentities($resultRow['mID']);

	        	$resultRow['mD'] = htmlentities($resultRow['mD']);
	        	$resultRow['mT'] = htmlentities($resultRow['mT']);

	        	$resultRow['mC'] = htmlentities($resultRow['mC']);

	        	$resultRow['mS'] = htmlentities($resultRow['mS']);

	        	$resultRow['gID'] = htmlentities($resultRow['gID']);

	        	$returnRow[] = $resultRow;

				error_log("messageID groupMessageListappend.php". $resultRow['mID']);
        }
	error_log("highest MID appended: ".$returnRow[count($returnRow)-1]['mID']);
$_SESSION['groupHighestMessageId']  = $returnRow[count($returnRow)-1]['mID'];
	//we will minus the count($returnRow) from the unread notification count and total unread notification counts
	$sql = "update groupuserlist SET groupuserlist.unreadNotifications = groupuserlist.unreadNotifications - :nowSeen WHERE groupuserlist.groupId = " .  $_SESSION['groupId'] . " AND groupuserlist.userId = :userId;";

		
		
		$statement = $pdo->prepare(
			            $sql
	        );
	        $statement->execute(array(//Updates the unread counter
			':userId' => $_SESSION['userId'],
			':nowSeen' => count($returnRow),

	        ));
$sql2 = "update  users SET unreadGroupMessageCount = unreadGroupMessageCount - :nowSeen WHERE userId = :userId;";
	    $statement = $pdo->prepare(
			            $sql2
	        );
	        $statement->execute(array(//Updates the total unread counter
			':userId' => $_SESSION['userId'],
			':nowSeen' => count($returnRow),
			
	    ));

    echo(json_encode($returnRow));
