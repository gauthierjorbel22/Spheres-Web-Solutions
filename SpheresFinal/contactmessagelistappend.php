<?php
     session_start();
    
    require_once"SpheresPDO.php";
    sleep(1.5);
    header('Content-Type: application/json; charset=utf-8'); 
    
	$returnRow = array();
$sql = "select message.messageId AS mID, messageDate AS mD, messageTime AS mT, content as mC, senderID as mS, receiverId as mR FROM message JOIN userbackup ON message.messageId = userbackup.messageId WHERE message.messageId > :lastGreatest AND ((senderId = :sender AND receiverId = :receiver) OR (senderId = :senderRev AND receiverId = :receiverRev)) ORDER BY mID ASC;";


$statement = $pdo->prepare(
	            $sql
	        );

	        $statement->execute(array(

	        	':receiver' => $_SESSION['userId'], //receiver id
	        	':sender' => $_SESSION['contactId'],
	        	':receiverRev' => $_SESSION['contactId'],
	        	':senderRev' => $_SESSION['userId'],
	        	':lastGreatest' => $_SESSION['personalHighestMessageId']

	        
	        ));

error_log("previous highest MID appended is seen to be: ".$_SESSION['personalHighestMessageId']);

	        while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) {
	        	$resultRow['mID'] = htmlentities($resultRow['mID']);

	        	$resultRow['mD'] = htmlentities($resultRow['mD']);
	        	$resultRow['mT'] = htmlentities($resultRow['mT']);

	        	$resultRow['mC'] = htmlentities($resultRow['mC']);

	        	$resultRow['mS'] = htmlentities($resultRow['mS']);

	        	$resultRow['mR'] = htmlentities($resultRow['mR']);

	        	$returnRow[] = $resultRow;

				error_log("messageID contactMessageListappend.php". $resultRow['mID']);
        }
    error_log("return Row contactmessagelistAppend line 45: ".count($returnRow));
	error_log("highest MID appended line 46: ".$returnRow[count($returnRow)-1]['mID']);

$_SESSION['personalHighestMessageId']  = $returnRow[count($returnRow)-1]['mID'];
	//we will minus the count($returnRow) from the unread notification count and total unread notification counts
	$sql = "update contactuserlist SET contactuserlist.unreadNotifications = contactuserlist.unreadNotifications - :nowSeen WHERE contactuserlist.contactId = :userId AND contactuserlist.userId = ".$_SESSION['contactId'];

		
		
		$statement = $pdo->prepare(
			            $sql
	        );
	        $statement->execute(array(//Updates the unread counter
			':userId' => $_SESSION['userId'],
			':nowSeen' => count($returnRow),

	        ));
$sql2 = "update  users SET unreadPrivateMessageCount = unreadPrivateMessageCount - :nowSeen WHERE userId = :userId;";
	    $statement = $pdo->prepare(
			            $sql2
	        );
	        $statement->execute(array(//Updates the total unread counter
			':userId' => $_SESSION['userId'],
			':nowSeen' => count($returnRow),
			
	    ));

    echo(json_encode($returnRow));
