<?php
     session_start();
    
    require_once"SpheresPDO.php";
    sleep(1.5);
    header('Content-Type: application/json; charset=utf-8'); 
    
	$returnRow = array();

error_log("before sql");
$sql = "
Select contactuserlist.userId AS userId, contactuserlist.contactId AS contactId, contactuserlist.unreadNotifications AS unreadNotifi, users.userName as contactName, users.department as contactDep, users.position as contactPos, users.signInId as contactsignInId FROM contactuserlist JOIN users ON contactuserlist.userId = users.userId WHERE contactuserlist.contactId = :userId ORDER BY unreadNotifi DESC, users.userName ASC";
//CREATE A SEPERATE STATEMENT FOR THE NOTIFICATION NUMBER

$statement = $pdo->prepare(
	            $sql
	        );
error_log("contactListAppend.php sQL ->>>>". $sql);


	        $statement->execute(array( 
	            ':userId' => $_SESSION['userId']
	        ));

while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) { 
 $resultRow['contactsignInId'] = $resultRow['contactsignInId'];
 $resultRow['contactName'] =  htmlentities($resultRow['contactName']);
 $resultRow['userId'] = $resultRow['userId'];
 $resultRow['unreadNotifi']= $resultRow['unreadNotifi'];
 $resultRow['contactPos'] = htmlentities($resultRow['contactPos']);
$resultRow['contactDep'] = htmlentities($resultRow['contactDep']);
$returnRow[] = $resultRow;
error_log("contactListAppend.php". $resultRow['contactName']);
}


    echo(json_encode($returnRow));
