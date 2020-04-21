<?php session_start();
    require_once"SpheresPDO.php";

    date_default_timezone_set('Africa/Johannesburg');


    if ( ! isset($_SESSION['SpheresUser']) || ! isset($_SESSION['userId'])) { //Redirects to the Login Page if the user is not logged in or if their session has timed out
        $_SESSION['fallacy'] = "Please provide your Log In details beneath";
        header('Location: login.php');
        return;
    }   

    $_SESSION["groupId"] = $_GET["groupId"]; 


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


 	

$sql = "Select groups.groupId AS groupId, groupuserlist.groupId, groupuserlist.unreadNotifications AS unreadNotifi, groups.groupName AS groupName, groups.repository AS groupRepository, groupuserlist.userId, groups.photo AS photo, groups.description AS description, groups.groupReminders AS remPhoto FROM groups JOIN groupuserlist ON groups.groupId = groupuserlist.groupId WHERE groupuserlist.userId = :userId AND groups.groupId = :groupId ORDER BY groups.groupName";

$statement = $pdo->prepare(
	            $sql
	        );

	        $statement->execute(array( 
	            ':userId' => $_SESSION['userId'],
	            ':groupId' => $_GET['groupId']
	        ));
$resultRow = $statement->fetch(PDO::FETCH_ASSOC);
	        if ( $resultRow === false ) { 
	            header( 'Location: general.php' ) ;
	            return;
	        }else{

				$_SESSION["groupId"] = htmlentities($resultRow['groupId']);
	        	$_SESSION["groupName"] = htmlentities($resultRow['groupName']);
	        	$_SESSION["unreadNotifi"] = htmlentities($resultRow['unreadNotifi']);
	        	$_SESSION["description"] = htmlentities($resultRow['description']);
	        	$_SESSION["photo"] = htmlentities($resultRow['photo']);	        	
	        	$_SESSION["remPhoto"] = htmlentities($resultRow['remPhoto']);
	        	//Retrieve The group's info and store in session

$sql = "Select groupuserlist.userId AS userId, users.userName AS userName FROM groupuserlist JOIN users ON groupuserlist.userId = users.userId WHERE groupuserlist.groupId= :groupId AND groupuserlist.userId != :userId";

$statement = $pdo->prepare(
	            $sql
	        );

	        $statement->execute(array( 
	            ':userId' => $_SESSION['userId'],
	            ':groupId' => $_SESSION['groupId']
	        ));

	        	
	        	$_SESSION["groupMemberIDs"]= array(); //Create groupMembers linear array of associative arrays in Session
				$_SESSION["groupMemberNames"]= array(); //Create groupMembers linear array of associative arrays in Session

	        while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) { 
	        	
	        	
	        	array_push($_SESSION["groupMemberIDs"], htmlentities($resultRow['userId']));

	        	array_push($_SESSION["groupMemberNames"], htmlentities($resultRow['userName']));


	        }

	    }










	if ( isset($_POST['message']) ) {//line 8 till 15 execute upon 'message' being a key in the newly submitted POST data
   
	error_log(date("Y-m-d"));
	error_log(date("H:i:s"));
	error_log($_POST['message']);
	error_log($_POST['userId']);

	$sql = "insert INTO `message` (`messageId`, `messageDate`, `messageTime`, `content`, `senderId`) VALUES (NULL, ";
	$sql = $sql ."'" . date('Y-m-d') ."','" . date("H:i:s") ."', :message ,'" . $_POST['userId'] . "');";
	/*+ ", "+ date("H:i:s") + ", "+ $_POST['message'] + ", " +$_POST['userId']+ ");";
	*/

	error_log($sql);

	$statement = $pdo->prepare(
		            $sql
		        );

	$statement->execute(array(

 				':message' => $_POST['message']

	));
	$messageId = $pdo -> lastInsertId();

	$sql = "insert INTO `groupbackup` (`groupId`, `messageId`, `groupBackupDate`) VALUES ('";
	$sql = $sql .$_POST['groupId']."','" . $messageId ."', NULL );";
	/*+ ", "+ date("H:i:s") + ", "+ $_POST['message'] + ", " +$_POST['userId']+ ");";
	*/
	error_log($sql);
	$statement = $pdo->prepare(
	            $sql
	        );

    $statement->execute(array());


		$sql = "update groupuserlist SET unreadNotifications = unreadNotifications + 1 WHERE groupId = " .  $_POST['groupId'] . " AND userId = :userId;";

		$sql2 = "update  users SET unreadGroupMessageCount = unreadGroupMessageCount + 1 WHERE userId = :userId ";

		$sql3 = "update  users SET latestGroupMessageID = :messageId WHERE userId = :userId;";

		$sql4 = "update  users SET latestGroupMessageID = :messageId WHERE userId = :userId;";


		$countingMem = 0;

		while($countingMem < count($_SESSION['groupMemberIDs'])){
			

			$statement = $pdo->prepare(
			            $sql
	        );
	        $statement->execute(array(//Updates the unread counter
			':userId' => $_SESSION['groupMemberIDs'][$countingMem]
	        ));



			$statement = $pdo->prepare(
			            $sql2
	        );
	        $statement->execute(array( //Updates the total unread counter
			':userId' => $_SESSION['groupMemberIDs'][$countingMem],

	        ));

$statement = $pdo->prepare(
			            $sql3
	        );
	        $statement->execute(array( //Updates the total unread counter
			':userId' => $_SESSION['groupMemberIDs'][$countingMem],
			':messageId'=> $messageId
	        ));
$statement = $pdo->prepare(
			            $sql4
	        );
	        $statement->execute(array( //Updates the total unread counter
			':userId' => $_SESSION['userId'],
			':messageId'=> $messageId
	        ));


	        $countingMem = $countingMem + 1;
	}

    $head = $_POST['link'];
    header($head);
    return;
  }


/*
For later use**

use spheresitsp;
SELECT message.messageId, messageDate, messageTime, content, sender, userId FROM message JOIN userbackup ON message.messageId = userbackup.messageId WHERE sender = 3 AND userId = 4 OR sender = 4 and userId = 3 ORDER BY messageDate;

*/

?>

<!DOCTYPE html>
<html>
    <head>
    
        <link type="text/css" rel="stylesheet" href="groupMessagePage.css">
              
        <title>Group Message Page</title>
    
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
     <a href = "Contacts.php">
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
        <div class = "rightBottomChat" >
			<div id="chatcontent">
		          <img  src="Loading.gif" alt="Loading..." class="Loading" />
		      </div>
				<script type="text/javascript" src="jquery.min.js"></script> 
				<!--- The script tag above includes the jQuery library-->

				<script type="text/javascript">
					 /* var initialLoad = 0;*/
					<?php
					$countingMembers = 0;

					echo("var gMIDs = [];");
					echo("var gMNames = [];");
					while($countingMembers<count($_SESSION["groupMemberIDs"])){
						echo("gMIDs[".$countingMembers."] = ".$_SESSION["groupMemberIDs"][$countingMembers].";");
						echo("gMNames[".$countingMembers."] = \"".$_SESSION["groupMemberNames"][$countingMembers]."\";");
						$countingMembers = $countingMembers + 1;
					}
					?>


					function updateMsg() { //Executes originally 
					  window.console && console.log('Requesting JSON'); 
					  $.getJSON('groupmessagelist.php', function(rowz){
					      window.console && console.log('JSON Received'); 
					      window.console && console.log(rowz);
					      $('#chatcontent').empty(); //The clear
					      for (var i = 0; i < rowz.length; i++) {
					        arow = rowz[i];



					        if(arow['mS'] == <?php echo($_SESSION['userId']); ?>){
					        $('#chatcontent').append(
					        	'<div class = "leftAlignMessage">\
					        	<div class = "leftMessageInfo">DATE:&nbsp;&nbsp;'+arow['mD']+'&nbsp;&nbsp;&nbsp;&nbsp;\
					        	TIME:&nbsp;&nbsp;'+arow['mT']+'<br/></div>\
					        	<div class = "leftMessageContent">'+arow['mC']+'</div></div>\n'
					        );
					    	}else{
					    		var fail = 1;
					    		 for (var findSend = 0; findSend < gMNames.length; findSend++) {
					    		 	if(gMIDs[findSend] == arow['mS']){
					    		 		fail = 0;
										$('#chatcontent').append(
							        	'<div class = "rightAlignMessage">\
							        	<div class = "rightMessageInfo">DATE:&nbsp;&nbsp;'+arow['mD']+'&nbsp;&nbsp;&nbsp;&nbsp;\
							        	TIME:&nbsp;&nbsp;'+arow['mT']+'<br/>\
							        	<strong>'+gMNames[findSend]+'</strong></div>\
							        	<div class = "rightMessageContent">'+arow['mC']+'</div></div>\n'
							        	);
									}
					    		 }
					    		 if(fail == 1){ 
					    		 	for (var findSend = 0; findSend < gMNames.length; findSend++) {
					    		 	if(gMIDs[findSend] == arow['mS']){
					    		 		fail = 0;
										$('#chatcontent').append(
							        	'<div class = "rightAlignMessage">\
							        	<div class = "rightMessageInfo">DATE:&nbsp;&nbsp;'+arow['mD']+'&nbsp;&nbsp;&nbsp;&nbsp;\
							        	TIME:&nbsp;&nbsp;'+arow['mT']+'<br/>\
							        	<strong>Unknown Sender</strong></div>\
							        	<div class = "rightMessageContent"><pre>'+arow['mC']+'</pre></div></div>\n'
							        	);
									}
					    		 }
					    		 }
					    }
					      }












					      /*var objDiv = document.getElementById("chatcontent");
					      objDiv.scrollTop = objDiv.scrollHeight;
					      *//*$("chatcontent").scrollTop();*/
					      


					      scrollDown();







					       window.console && console.log('updateMsgCheck() called from updateMsg()');
					      setTimeout('updateMsgCheck()', 4000);
					  });
					}

					function scrollDown(){//auto Scrolls down when a message is sent
					  var chatArea = document.querySelector('.rightBottomChat');
					  chatArea.scrollTop = chatArea.scrollHeight;
					}

					function updateMsgCheck() { //The most executing expecting for unread messages and by extent conditional appending follows
					  window.console && console.log('Requesting JSON Update Message Check'); 
					  $.getJSON('groupmessagelistcheck.php', function(rowz){
					      window.console && console.log('JSON CheckValue Received'); 
					      arow = rowz[0];
					      
					    
					        if(arow['unreadNotifications'] > 0){
					     		window.console && console.log('appendToMsg() called');
					        	appendToMsg();
					        }	 
					        checkMessageID();
					      setTimeout('updateMsgCheck()', 4000);//Only executes if the previous if statement doesnt
					  });
					}








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
					if(temp2 != arow){
						window.console && console.log('diffrent, temp was: ' + temp2 + ' and new is ' + arow);
						temp2 = arow;
						updateTheRecentChats();
					}
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



function updateTheRecentChats(){
window.console && console.log('updateTheRecentChats() called <-------------');
$('#RecentC').empty();
window.console && console.log('Requesting recentChatUpdate');
$.getJSON('recentChatsUpdate.php', function(rowz) {
window.console && console.log('JSON Received');
window.console && console.log(rowz);
for (var i = 0; i < rowz.length; i++) {
	arow = rowz[i];
	string = 
    $('#RecentC').append('<tr> <td  style="border:1px solid black" width = 400% ><a href = "contactMessagePage.php?contactId=' + arow['userID'] + '">' + arow['users']+'</a><br>'+arow['content']+'<br></td></tr>');
}

});
}


					function appendToMsg() { //conditionally called from updateMsgCheck()
					  window.console && console.log('Requesting JSON'); 
					  $.getJSON('groupmessagelistappend.php', function(rowz){
					      window.console && console.log('JSON Received'); 
					      window.console && console.log(rowz);
					      for (var i = 0; i < rowz.length; i++) {
					        arow = rowz[i];
					        //No clear
					        if(arow['mS'] == <?php echo($_SESSION['userId']); ?>){
					        $('#chatcontent').append(
					        	'<div class = "leftAlignMessage">\
					        	<div class = "leftMessageInfo">DATE:&nbsp;&nbsp;'+arow['mD']+'&nbsp;&nbsp;&nbsp;&nbsp;\
					        	TIME:&nbsp;&nbsp;'+arow['mT']+'<br/></div>\
					        	<div class = "leftMessageContent"><pre>'+arow['mC']+'</pre></div></div>\n\
					        	'
					        );
					    	}else{
					    		var fail = 1;
					    		 for (var findSend = 0; findSend < gMNames.length; findSend++) {
					    		 	if(gMIDs[findSend] == arow['mS']){
					    		 		fail = 0;
										$('#chatcontent').append(
							        	'<div class = "rightAlignMessage">\
							        	<div class = "rightMessageInfo">DATE:&nbsp;&nbsp;'+arow['mD']+'&nbsp;&nbsp;&nbsp;&nbsp;\
							        	TIME:&nbsp;&nbsp;'+arow['mT']+'<br/>\
							        	<strong>'+gMNames[findSend]+'</strong></div>\
							        	<div class = "rightMessageContent"><pre>'+arow['mC']+'</pre></div></div>\n\
							        	'
							        	);
									}
					    		 }

					    		 if(fail == 1){ 
					    		 	for (var findSend = 0; findSend < gMNames.length; findSend++) {
					    		 	if(gMIDs[findSend] == arow['mS']){
					    		 		fail = 0;
										$('#chatcontent').append(
							        	'<div class = "rightAlignMessage">\
							        	<div class = "rightMessageInfo">DATE:&nbsp;&nbsp;'+arow['mD']+'&nbsp;&nbsp;&nbsp;&nbsp;\
							        	TIME:&nbsp;&nbsp;'+arow['mT']+'<br/>\
							        	<strong>Unknown Sender</strong></div>\
							        	<div class = "rightMessageContent"><pre>'+arow['mC']+'</pre></div></div>\n\
							        	'
							        	);
									}
					    		 }
					    		 }
					    }
					      }
					       window.console && console.log('updateMsgCheck() called from appendToMsg()');
					      setTimeout('updateMsgCheck()', 4000);
					  });
					}


					// Make sure JSON requests are not cached
					$(document).ready(function() {
					  $.ajaxSetup({ cache: false });
temp2 = <?php echo($_SESSION["lastPersonalmessageID"]);?>;
				window.console && console.log('Original Temp ' + temp2);
					  window.console && console.log('updateMsg() called Originally');
					  updateMsg();
					});
				</script>

</div>

<div class = "rightBottomTextArea">
        		
      			<textarea id = "messageVisible" name="Vmessage" style = "width:70%; height: 65%;float: left; left:7.5%; position :absolute; bottom:15%;" ></textarea>
      			
      			<?php
      			echo("<form method=\"post\" action=\"groupMessagePage.php?groupId=");
      			  print_r($_GET['groupId']);
      			echo("\">");
      			
      			echo("<p><input type=\"hidden\" name=\"link\" value =\"Location: groupMessagePage.php?groupId=");
      			  print_r($_GET['groupId']);
      			echo("\">");
      			
      			echo("<p><input type=\"hidden\" name=\"groupId\" value =\"");
      			  print_r($_GET['groupId']);
      			echo("\">");

      			echo("<p><input type=\"hidden\" name=\"date\" value =\"");
      			  echo(date('Y-m-d'));
      			echo("\">");

      			echo("<p><input type=\"hidden\" name=\"time\" value =\"");
      			  echo(date("h:i:s"));
      			echo("\">");

      			echo("<p><input type=\"hidden\" name=\"userId\" value =\"");
      			  print_r($_SESSION['userId']);
      			echo("\">");

      			?>
			       <input id = "messageInvisible"  type = hidden name = "message" value = "">
			       
				     <input id = "sendB" type="submit" value="Send" style = "width:10%; height:45%; float: left; right:5%; position: absolute; bottom: 27.5%;"/>
			      
				<script>
				$(document).ready(function(){

                    window.console && console.log('ready SendB');
                    $('#sendB').click(function(event){
						window.console && console.log('register click');
                       // event.preventDefault(); //prevent the default behaviour of the tag -> in this case to submit the POST request in which the tag is encapsulated
                        
                        if ( $('#messageVisible').val() != "") {
                        	document.getElementById("messageInvisible").value = $('#messageVisible').val();
                            window.console && console.log('YEs Not Empty SendB');
                            return true;
                        }else{
                        	 event.preventDefault();
                        	 window.console && console.log('prevent Default');
                        	 return;
                        }
                        
                    });
                });  
</script>

			      
      			</form>
</div>




















    	</div>
    		<div class = "rightTop" >
    			<div style = "height: 100%; width : 15%; float:left;"></div><?php /*22.27 height*/
    			

	           
	       		echo("<strong><a href = \"groupMessagePage.php?groupId=".$_SESSION['groupId']."\">");
	           echo(" <span style = \"height = 100%;\"><img src = \"Photos/".$_SESSION['photo'].".jpg\" style = \" height: 100%; float: left;\"></span></a>");  
				echo("</strong>");

?>
<div style = "height: 100%; width : 15%; float:left;"></div>
<?php
	           echo("<table><tr><td><br><strong>");
	           echo("Group:  <a href = \"groupMessagePage.php?groupId=".$_SESSION['groupId']."\">");
	           echo($_SESSION['groupName']);
	           echo("</a></strong></td></tr>");

			   

	           echo("<tr><td><br><strong>Group Description: ".$_SESSION['description']."</strong></td></tr></table>");
	           //group reminders image
	           echo("<strong><br><a href = \"reminders/groupReminders/".$_SESSION['remPhoto']."\" target = \"_blank\"/>");
	           echo("Click here for group reminders");
	           echo(" <span style = \"height = 60%;\"><img src = \"reminders/groupReminders/".$_SESSION['remPhoto'].".png\" style = \" height: 10%; float: middle;\"></span></a>");
	           echo("</strong>");
 ?>
    	</div>
	    
    </body>
    <footer>
    	<strong>&copy; Spheres Web Solutions 2019</strong>
    </footer>
</html>