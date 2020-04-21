<?php
    session_start();
    
    require_once"SpheresPDO.php";
    sleep(1.5);
    header('Content-Type: application/json; charset=utf-8'); //Thus has to be expressed on a single line
    
    /*if ( !isset($_SESSION['chats']) ){ 
    	$_SESSION['chats'] = array(); //Instead of a blank array we will set the array to retrieve all 
	}
	//Here we will have a SQL query to append any new entries onto the $_SESSION array
*/
	$returnRow = array();

$sql = "select message.messageId AS mID, messageDate AS mD, messageTime AS mT, content as mC, senderID as mS, receiverId as mR FROM message JOIN userbackup ON message.messageId = userbackup.messageId WHERE senderId = :sender AND receiverId = :receiver OR senderId = :senderRev and receiverId = :receiverRev ORDER BY mID ASC;";


$statement = $pdo->prepare(
	            $sql
	        );

	        $statement->execute(array(

	        	':receiver' => $_SESSION['userId'], //receiver id
	        	':sender' => $_SESSION['contactId'],
	        	':receiverRev' => $_SESSION['contactId'],
	        	':senderRev' => $_SESSION['userId']

	        
	        ));

	        while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) {
	        	
	        	$resultRow['mID'] = htmlentities($resultRow['mID']);

	        	$resultRow['mD'] = htmlentities($resultRow['mD']);
	        	$resultRow['mT'] = htmlentities($resultRow['mT']);

	        	$resultRow['mC'] = htmlentities($resultRow['mC']);

	        	$resultRow['mS'] = htmlentities($resultRow['mS']);

	        	$resultRow['mR'] = htmlentities($resultRow['mR']);

error_log("messageID contactMessageList.php".$resultRow['mID']);
$returnRow[] = $resultRow;
/*
error_log('mID');
*/	        }
if(count($returnRow) != 0){
	error_log("original number messages contactmessagelist : ".count($returnRow));
	error_log("highest MID originally: ".$returnRow[count($returnRow)-1]['mID']);

	$_SESSION['personalHighestMessageId'] = $returnRow[count($returnRow)-1]['mID'];
}else{
	$_SESSION['personalHighestMessageId'] =0;
}









	$sql2 = "select contactuserList.unreadNotifications AS unreadNotifications FROM contactuserlist WHERE userId = :user AND contactId = :contact;";
	$statement = $pdo->prepare(
	    $sql2
	);

	$statement->execute(array(
	  	':user' => $_SESSION['contactId'],
		':contact' => $_SESSION['userId']        
	));

	while( $resultCount = $statement->fetch(PDO::FETCH_ASSOC)){
	$returnValue = $resultCount;
	}

if($returnValue > 0){
		$sql = "update contactuserlist SET contactuserlist.unreadNotifications = contactuserlist.unreadNotifications - :nowSeen WHERE contactuserlist.contactId = :contact AND userId = :user;";

		$statement = $pdo->prepare(
			            $sql
	        );
	        $statement->execute(array(//Updates the unread counter
			':user' => $_SESSION['contactId'],
			':nowSeen' => $returnValue['unreadNotifications'],
			':contact' => $_SESSION['userId']

	        ));

		$sql2 = "update users SET unreadPrivateMessageCount = unreadPrivateMessageCount - :nowSeen WHERE userId = :userId;";
		
	    $statement = $pdo->prepare(
			            $sql2
	        );

	        $statement->execute(array(//Updates the total unread counter
			':userId' => $_SESSION['userId'],
			':nowSeen' => $returnValue['unreadNotifications']			
	    ));
}









    echo(json_encode($returnRow));
