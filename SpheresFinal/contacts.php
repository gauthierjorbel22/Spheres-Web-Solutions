<?php session_start();
    require_once"SpheresPDO.php";

        
    if ( ! isset($_SESSION['SpheresUser']) || ! isset($_SESSION['userId'])) { //Redirects to the Login Page if the user is not logged in or if their session has timed out
        $_SESSION['fallacy'] = "Please provide your Log In details beneath";
        header('Location: login.php');
        return;
    }   

$sql = "select users.mostRecentMessageId AS mostRecent FROM `users` WHERE users.userId = :user;";

$statement = $pdo->prepare(
	$sql
);

$statement->execute(array(
':user' => $_SESSION['userId'],
));

while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) {
$_SESSION["lastPersonalmessageID"] = $resultRow["mostRecent"];
}
/* Flash message start
    if(!isset($_SESSION['fallacy'])){//We assume no error message is present
        $_SESSION['fallacy'] = false;
    }  */
?>

<!DOCTYPE html>
<html>
    <head>
    
        <link type="text/css" rel="stylesheet" href="contacts.css">
              
        <title>Spheres General Page</title>
    
    </head>
    <body>


	    	<script type="text/javascript" src="jquery.min.js"> //JQuery
			</script>

    <a href="general.php">
		<img class = "toprightHome"
			src = "Favicons/Home.png" 
			style = "
			width:10%;
				height: 7%;"
        >
      
     </a>
     <a href = "contacts.php">
		<img class = "toprightContacts" 
			src = "Favicons/Contacts.png" 
			style = "
			width:10%;
            height: 7%;"
        >
        </a>
		<a href = "help.html" target="blank">
			<img 
				class = "toprightHelp"
				src = "Favicons/Help.png" 
				style = "
				width:10%;
                height: 7%;"
            >
            
        </a>
        <a href="general.php">
		<img 
			class = "toprightLogo"
			src = "Favicons/logoBall.png" 
			style = "
				width:10%;
				height: 9%;"
        >
</a>
		<a href="areyousure.php">
		<img 
			class = "toprightLogout"
			src = "Favicons/Logout.png" 
			style = "
				width:10%;
				height: 7%;"
		> 
</a>
  
        <div class = "leftTop">
        
        	<span id = "pfp"></span> <!--This image is a place holder for the profile photo-->
        
        </div>

<!--The following script tag contains the template for the img tag that will populate the span tag above after the script tags beneath this one executes-->
		<script id="img-template" type="text"> 
		        	<img <?php
			            echo("src = \"Photos/".
			            $_SESSION['signInId'].".jpg\" style = 
			                width:@VAL@%;
			                height: 100%;

						");
		            ?> 
		        >	
        </script>
<!--The following script tag contains the code for the profile photo that will execute upon the window's resizing-->
		<script type="text/javascript">
				$(window).resize(function() {
				  $('#pfp').empty();
				  if($(window).width() > $(window).height()){
				  	ratio = 100 / ($(window).width() / $(window).height()); 
				  	var sourceScript = $('#img-template').html();
		                        $('#pfp').append(sourceScript.replace(/@VAL@/g, ratio));
				  }else{

				  var sourceScript = $('#img-template').html();
		                        $('#pfp').append(sourceScript.replace(/@VAL@/g, 100));
				}});
		</script>
<!--The following script tag contains the code for the progile photo that will execute upon the document's initial load (super similair)-->
		<script type="text/javascript">
				$(document).ready(function() {
				  $('#pfp').empty();
				  if($(window).width() > $(window).height()){
				  	ratio = 100 / ($(window).width() / $(window).height()); 
				  	var sourceScript = $('#img-template').html();
		                        $('#pfp').append(sourceScript.replace(/@VAL@/g, ratio));
				  }else{
				  	
				  	var sourceScript = $('#img-template').html();
		                        $('#pfp').append(sourceScript.replace(/@VAL@/g, 100));
				  }

				  
				});
		</script>

<div class = "leftMiddleTop">
        	<strong><?php echo($_SESSION['SpheresUser']); ?></strong>
       </div>
            <div class = "LeftMiddleMiddleTop">
        	<strong>Personal Messages: <a href = "contacts.php" id = "personalCounter"></a></strong>
        	
        </div>
 		
 		<div class = "LeftMiddleMiddleBottom">
        	<strong>Group Messages: <a href = "general.php" id = "groupCounter"></a></strong>
        	
        </div> 
        <div class = "leftMiddleBottom">
        	<strong>Personal Inbox</strong>
        	
        </div>
     <div class="leftBottom">
		<table id= "RecentC" style="border:1px solid black" width = 99%>

			<?php

			$sql = "select users.userId AS userID, users.userName AS users, message.content AS content 
			FROM `contactuserlist`
			JOIN message ON contactuserlist.lastMessageId = message.messageId 
			JOIN users ON users.userId = contactuserlist.userId 
			WHERE contactuserlist.contactId = :user 
			AND lastMessageId IS NOT 
			NULL ORDER BY `lastMessageId` DESC;";

			error_log($sql);

			$statement = $pdo->prepare(
				$sql
			);

			$statement->execute(array(

				':user' => $_SESSION['userId'],
			));

			while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) {

				echo ("<tr> 

							<td  style=\"border:1px solid black\" width = 400% >");
				echo ("<a href = \"contactMessagePage.php?contactId=" . $resultRow['userID'] . "\">" . $resultRow['users'] . "</a>");
				echo ('<br>');
				echo (htmlentities($resultRow['content']));
				echo ('<br>');
				echo ("</td>");
				echo ("</tr>");
			}
			?>
		</table>

		

	</div>
        <div class = "rightBottom" >
        	<br>
    
<script type="text/javascript"> //Dynamic rewriting of the contact list when lastMessageID changes
			$(document).ready(function() {
				window.console && console.log('Original unread messageID= '+<?php echo($_SESSION["lastPersonalmessageID"]);?> );
				window.console && console.log('checkMessageID() called Originally');
				temp = <?php echo($_SESSION["lastPersonalmessageID"]); ?>;
				window.console && console.log('Original Temp ' + temp);
				checkMessageID();
			});

			function checkMessageID() { //conditionally called from updateMsgCheck()
				$.ajaxSetup({
					cache: false
				});

				updateUnreadCounters();
				window.console && console.log('Requesting CheckMessage');
					$.getJSON('MostRecentPersonalMessageId.php', function(rowz) {
					window.console && console.log('JSON Received');
					window.console && console.log(rowz);
					arow = rowz[0];
					if(temp != arow){
						window.console && console.log('diffrent, temp was: ' + temp + ' and new is ' + arow);
						temp = arow;
						updateTheChatlist();
					}
					window.console && console.log('Gona Call Again - temp was: ' + temp + ' and new is ' + arow);
					setTimeout('checkMessageID()', 4000);
				});
			}


function updateUnreadCounters(){
window.console && console.log('Requesting unreadNotificationCount');
					$.getJSON('unreadNotificationCount.php', function(rowz) {
					window.console && console.log('JSON for unreadNotificationCount Received');
					window.console && console.log(rowz);
					arow = rowz[0];
					$('#personalCounter').empty();
					$('#personalCounter').append(arow);
					window.console && console.log(arow);
					arow = rowz[1]
					$('#groupCounter').empty();
					$('#groupCounter').append(arow);
					window.console && console.log(arow);
});
				}



function updateTheChatlist() {
window.console && console.log('updateTheChatlist() was called');
location.reload();

			}


</script>

    <center><table style="width:90%" id = "ChatList">
  
<?php 
 	/*
 	use spehersitsp;
Select contactuserlist.userId AS userId, contactuserlist.contactId AS contactId, contactuserlist.unreadNotifi AS unreadNotifi, users.userName as contactName, users.department as contactDep, users.position as contactPos, users.photo as contactPhoto FROM contactuserlist JOIN users ON contactuserlist.contactId = users.userId WHERE contactuserlist.userId = 1 ORDER BY users.userName*/


$sql = "
Select contactuserlist.userId AS userId, contactuserlist.contactId AS contactId, contactuserlist.unreadNotifications AS unreadNotifi, users.userName as contactName, users.department as contactDep, users.position as contactPos, users.signInId as contactsignInId FROM contactuserlist JOIN users ON contactuserlist.userId = users.userId WHERE contactuserlist.contactId = :userId ORDER BY unreadNotifi DESC, users.userName ASC";
//CREATE A SEPERATE STATEMENT FOR THE NOTIFICATION NUMBER

$statement = $pdo->prepare(
	            $sql
	        );

	        $statement->execute(array( 
	            ':userId' => $_SESSION['userId']
	        ));

	       while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) { //If Statement will only execute in the case where there exists no user matched to the provided Username and Password
	           
	       		echo("<tr><td style = \"width:6%; height:6%;margin:0px; padding:0px;\"><center> <br><strong><a href = \"contactMessagePage.php?contactId=".htmlentities($resultRow['userId'])."\">");
	           echo("

<img src = \"Photos/".htmlentities($resultRow['contactsignInId']).".jpg\" style = \"width:100%; height: 100%;\"></a>");
	        
echo("</strong></center></td>");


	           echo("<td><center> <br><strong>");
	           echo("<a href = \"contactMessagePage.php?contactId=".htmlentities($resultRow['userId'])."\">");
	           echo(htmlentities($resultRow['contactName']));
	           echo("</a></strong></center></td>");

			   echo("<td><center> <br><strong>");
	           echo("New Messages:  <a href = \"contactMessagePage.php?contactId=".htmlentities($resultRow['userId'])."\" style= \"text-decoration: none; color:red;\">");
	           echo(htmlentities($resultRow['unreadNotifi']));
	           echo("</a></strong></center></td>");

	           echo("<td><center><br><strong>".htmlentities($resultRow['contactPos'])." [".htmlentities($resultRow['contactDep'])."] </strong></center></td></tr>");
	        } 


 ?>

</table> 

    	</div>
    		<div class = "rightTop">
    			<strong>Contacts</strong>
    	</div>
	    
    </body>
    <footer>
    	<strong>&copy; Spheres Web Solutions 2019</strong>
    </footer>
</html>