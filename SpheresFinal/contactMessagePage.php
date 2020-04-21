<?php session_start();

date_default_timezone_set('Africa/Johannesburg');

require_once "SpheresPDO.php";




if (!isset($_SESSION['SpheresUser']) || !isset($_SESSION['userId'])) { //Redirects to the Login Page if the user is not logged in or if their session has timed out
	$_SESSION['fallacy'] = "Please provide your Log In details beneath";
	header('Location: login.php');
	return;
}


$_SESSION["contactId"] = $_GET["contactId"];



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




$sql = " Select contactuserlist.userId AS userId, contactuserlist.contactId AS contactId, contactuserlist.unreadNotifications AS unreadNotifi, users.userName as contactName, users.department as contactDep, users.position as contactPos, users.signInId as contactsignInId FROM contactuserlist JOIN users ON contactuserlist.contactId = users.userId WHERE contactuserlist.userId = :userId AND contactuserlist.contactId = :contactId ORDER BY users.userName";

$statement = $pdo->prepare(
	$sql
);

$statement->execute(array(
	':userId' => $_SESSION['userId'],
	':contactId' => $_SESSION['contactId']
));

$resultRow = $statement->fetch(PDO::FETCH_ASSOC);

if ($resultRow === false) {
	header('Location: contacts.php');
	return;
} else {
	$_SESSION["contactsignInId"] = htmlentities($resultRow['contactsignInId']);
	$_SESSION["contactName"] = htmlentities($resultRow['contactName']);
	$_SESSION["contactPos"] = htmlentities($resultRow['contactPos']);
	$_SESSION["contactDep"] = htmlentities($resultRow['contactDep']);
}










if (isset($_POST['message'])) { // execute upon 'message' being a key in the newly submitted POST data


	error_log(date("Y-m-d"));
	error_log(date("H:i:s"));
	error_log($_POST['message']);
	error_log($_POST['userId']);


	$sql = "insert INTO `message` (`messageId`, `messageDate`, `messageTime`, `content`, `senderId`) VALUES (NULL, ";
	$sql = $sql . "'" . date('Y-m-d') . "','" . date("H:i:s") . "', :message ,'" . $_POST['userId'] . "');";


	error_log("The insert into message table sql: " . $sql);

	$statement = $pdo->prepare(
		$sql
	);

	$statement->execute(array(
		':message' => $_POST['message']
	));






	$messageId = $pdo->lastInsertId();


	$sql = "insert INTO `userbackup` (`receiverId`, `messageId`, `userBackupDate`) VALUES ('";
	$sql = $sql . $_POST['contactId'] . "','" . $messageId . "', NULL );";


	error_log("The insert into userbackup sql line88 contactMessagePage: " . $sql);

	$statement = $pdo->prepare(
		$sql
	);

	$statement->execute(array());

	$sql = "update contactuserlist SET unreadNotifications = unreadNotifications + 1 WHERE userId = " .  $_SESSION['userId'] . " and contactId = " . $_SESSION['contactId'] . ";";

	error_log("The unreadNotifications increase sql line98 contactMessagePage: " . $sql);
	$statement = $pdo->prepare(
		$sql
	);

	$statement->execute(array());


	$sql = "update users SET unreadPrivateMessageCount = unreadPrivateMessageCount + 1 WHERE userId = " .  $_SESSION['contactId'] . ";";

	error_log("The unreadPrivateMessageCount increase sql line108 contactMessagePage: " . $sql);
	$statement = $pdo->prepare(
		$sql
	);

	$statement->execute(array());

	$sql = "update contactuserlist SET lastMessageId = " . $messageId . " WHERE userId = " .  $_SESSION['userId'] . " and contactId = " . $_SESSION['contactId'] . ";";

	error_log($sql);
	$statement = $pdo->prepare(
		$sql
	);

	$statement->execute(array());

	$sql = "update users SET mostRecentMessageId = " . $messageId . " WHERE userId = " .  $_SESSION['contactId'] . ";";
	error_log("The mostRecentMessageId set sql line125 contactMessagePage: " . $sql);

	error_log($sql);
	$statement = $pdo->prepare(
		$sql
	);

	$statement->execute(array());

	$head = $_POST['link'];
	header($head);
	return;
}

/*THis code is a simple copy and paste*/



/*
For later use**

use spheresitsp;
SELECT message.messageId, messageDate, messageTime, content, sender, userId FROM message JOIN userbackup ON message.messageId = userbackup.messageId WHERE sender = 3 AND userId = 4 OR sender = 4 and userId = 3 ORDER BY messageDate;

*/

?>

<!DOCTYPE html>
<html>

<head>

	<link type="text/css" rel="stylesheet" href="contactMessagePage.css">

	<title>Contact Message Page</title>

</head>

<body>


	<script type="text/javascript" src="jquery.min.js">
		//JQuery
	</script>

	<a href="general.php">
		<img class="toprightHome" src="Favicons/Home.png" style="
			width:10%;
				height: 7%;">

	</a>
	<a href="Contacts.php">
		<img class="toprightContacts" src="Favicons/Contacts.png" style="
			width:10%;
            height: 7%;">
	</a>
	<a href="help.html" target="blank">
		<img class="toprightHelp" src="Favicons/Help.png" style="
				width:10%;
                height: 7%;">

	</a>
	<a href="general.php">
		<img class="toprightLogo" src="Favicons/logoBall.png" style="
				width:10%;
				height: 9%;">
	</a>
	<a href="areyousure.php">
		<img class="toprightLogout" src="Favicons/Logout.png" style="
				width:10%;
				height: 7%;">
	</a>

	<div class="leftTop">

		<span id="pfp"></span>
		<!--This image is a place holder for the profile photo-->

	</div>

	<!--The following script tag contains the template for the img tag that will populate the span tag above after the script tags beneath this one executes-->
	<script id="img-template" type="text">
		<img <?php
				echo ("src = \"Photos/" .
					$_SESSION['signInId'] . ".jpg\" style = 
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
			if ($(window).width() > $(window).height()) {
				ratio = 100 / ($(window).width() / $(window).height());
				var sourceScript = $('#img-template').html();
				$('#pfp').append(sourceScript.replace(/@VAL@/g, ratio));
			} else {

				var sourceScript = $('#img-template').html();
				$('#pfp').append(sourceScript.replace(/@VAL@/g, 100));
			}
		});
	</script>
	<!--The following script tag contains the code for the progile photo that will execute upon the document's initial load (super similair)-->
	<script type="text/javascript">
		$(document).ready(function() {
			$('#pfp').empty();
			if ($(window).width() > $(window).height()) {
				ratio = 100 / ($(window).width() / $(window).height());
				var sourceScript = $('#img-template').html();
				$('#pfp').append(sourceScript.replace(/@VAL@/g, ratio));
			} else {

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
	<div class="rightBottomChat">
		<div id="chatcontent">
			<div class="Loading">
				<div class="sk-cube1 sk-cube"></div>
				<div class="sk-cube2 sk-cube"></div>
				<div class="sk-cube4 sk-cube"></div>
				<div class="sk-cube3 sk-cube"></div>
				<h3 class="Loading-txt">Loading,Please wait...</h3>
			</div>
		</div>

		<script type="text/javascript" src="jquery.min.js"></script>
		<!--- The script tag above includes the jQuery library-->
		<script type="text/javascript">
			function updateMsg() {
				window.console && console.log('Requesting JSON');
				$.getJSON('contactmessagelist.php', function(rowz) {
					window.console && console.log('JSON Received');
					window.console && console.log(rowz);
					$('#chatcontent').empty();
					for (var i = 0; i < rowz.length; i++) {
						arow = rowz[i];



						if (arow['mS'] == <?php echo ($_SESSION['userId']); ?>) {
							$('#chatcontent').append( //THe sender's infomraiton from session needs to be shown 
								'<div class = "leftAlignMessage">\
					        	<div class = "leftMessageInfo">DATE:&nbsp;&nbsp;' + arow['mD'] + '\
					        	&nbsp;&nbsp;&nbsp;&nbsp;TIME:&nbsp;&nbsp;' + arow['mT'] + '</div>\
					        	<div class = "leftMessageContent">' + arow['mC'] + '</div></div>\n'
							);
						} else {
							$('#chatcontent').append( //THe sender's infomraiton from the associative array in session needs to be shown
								'<div class = "rightAlignMessage">\
					        	<div class = "rightMessageInfo">DATE:&nbsp;&nbsp;' + arow['mD'] + '\
					        	&nbsp;&nbsp;&nbsp;&nbsp;TIME:&nbsp;&nbsp;' + arow['mT'] + '<br/>\
					        	<strong>' + <?php echo ("'" . $_SESSION['contactName'] . "'"); ?> + '</strong></div>\
					        	<div class = "rightMessageContent">' + arow['mC'] + '</div></div>\n'
							);
						}
					}

					scrollDown();
					setTimeout('updateMsgCheck()', 4000);
				});
			}

			function scrollDown() { //auto Scrolls down when a message is sent
				var chatArea = document.querySelector('.rightBottomChat');
				chatArea.scrollTop = chatArea.scrollHeight;
			}

			function updateMsgCheck() { //The most executing expecting for unread messages and by extent conditional appending follows
				window.console && console.log('Requesting JSON Update Message Check');
				$.getJSON('contactmessagelistcheck.php', function(rowz) {
					window.console && console.log('JSON CheckValue Received');
					arow = rowz[0];


					if (arow['unreadNotifications'] > 0) {
						window.console && console.log('appendToMsg() called');
						appendToMsg();
					}
					checkMessageID();
					setTimeout('updateMsgCheck()', 4000); //Only executes if the previous if statement doesnt
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
				$.getJSON('contactmessagelistappend.php', function(rowz) {
					window.console && console.log('JSON Received');
					window.console && console.log(rowz);
					for (var i = 0; i < rowz.length; i++) {
						arow = rowz[i];

						//No clear
						if (arow['mS'] == <?php echo ($_SESSION['userId']); ?>) {
							$('#chatcontent').append( //THe sender's infomraiton from session needs to be shown 
								'<div class = "leftAlignMessage">\
					        	<div class = "leftMessageInfo">DATE:&nbsp;&nbsp;' + arow['mD'] + '\
					        	&nbsp;&nbsp;&nbsp;&nbsp;TIME:&nbsp;&nbsp;' + arow['mT'] + '</div>\
					        	<div class = "leftMessageContent"><pre>' + arow['mC'] + '</pre></div></div>\n'
							);
						} else {
							$('#chatcontent').append( //THe sender's infomraiton from the associative array in session needs to be shown
								'<div class = "rightAlignMessage">\
					        	<div class = "rightMessageInfo">DATE:&nbsp;&nbsp;' + arow['mD'] + '\
					        	&nbsp;&nbsp;&nbsp;&nbsp;TIME:&nbsp;&nbsp;' + arow['mT'] + '<br/>\
					        	<strong>' + <?php echo ("'" . $_SESSION['contactName'] . "'"); ?> + '</strong></div>\
					        	<div class = "rightMessageContent"><pre>' + arow['mC'] + '</pre></div></div>\n'
							);
						}
					}
					window.console && console.log('updateMsgCheck() called from appendToMsg()');
					setTimeout('updateMsgCheck()', 4000);
				});
			}
















































































			// Make sure JSON requests are not cached
			$(document).ready(function() {
				$.ajaxSetup({
					cache: false
				});

temp2 = <?php echo($_SESSION["lastPersonalmessageID"]);?>;
				window.console && console.log('Original Temp ' + temp2);

				window.console && console.log('updateMsg() called Originally');
				updateMsg();
			});
		</script>

	</div>
	<div class="rightBottomTextArea">

		<textarea id="messageVisible" name="Vmessage" style="width:70%; height: 65%;float: left; left:7.5%; position :absolute; bottom:15%;"></textarea>
		<?php
		echo ("<form method=\"post\" action=\"contactMessagePage.php?contactId=");
		print_r($_GET['contactId']);
		echo ("\">");

		echo ("<p><input type=\"hidden\" name=\"link\" value =\"Location: contactMessagePage.php?contactId=");
		print_r($_GET['contactId']);
		echo ("\">");

		echo ("<p><input type=\"hidden\" name=\"contactId\" value =\"");
		print_r($_GET['contactId']);
		echo ("\">");

		echo ("<p><input type=\"hidden\" name=\"date\" value =\"");
		echo (date('Y-m-d'));
		echo ("\">");

		echo ("<p><input type=\"hidden\" name=\"time\" value =\"");
		echo (date("h:i:s"));
		echo ("\">");

		echo ("<p><input type=\"hidden\" name=\"userId\" value =\"");
		print_r($_SESSION['userId']);
		echo ("\">");

		?>
		<input id="messageInvisible" type=hidden name="message" value="">

		<input id="sendB" type="submit" value="Send" style="width:10%; height:45%; position: absolute; bottom: 27.5%;" />

		<script>
			$(document).ready(function() {

				window.console && console.log('ready SendB');
				$('#sendB').click(function(event) {
					window.console && console.log('register click');
					// event.preventDefault(); //prevent the default behaviour of the tag -> in this case to submit the POST request in which the tag is encapsulated

					if ($('#messageVisible').val() != "") {
						document.getElementById("messageInvisible").value = $('#messageVisible').val();
						window.console && console.log('YEs Not Empty SendB');
						return true;
					} else {
						event.preventDefault();
						window.console && console.log('prevent Default');
						return;
					}

				});
			});
		</script>


		</form>
	</div>


	<div class="rightTop">
		<div style="height: 100%; width : 15%; float:left;"></div><?php /*22.27 height*/

																	/*
 	use spehersitsp;
Select contactuserlist.userId AS userId, contactuserlist.contactId AS contactId, contactuserlist.unreadNotifi AS unreadNotifi, users.userName as contactName, users.department as contactDep, users.position as contactPos, users.photo as contactPhoto FROM contactuserlist JOIN users ON contactuserlist.contactId = users.userId WHERE contactuserlist.userId = 1 ORDER BY users.userName*/



																	echo ("<strong><a href = \"contactMessagePage.php?contactId=" . $_SESSION['contactId'] . "\">");
																	echo (" <span style = \"height = 100%;\"><img src = \"Photos/" . $_SESSION['contactsignInId'] . ".jpg\" style = \" height: 100%; float: left;\"></span></a>");
																	echo ("</strong>");

																	?>
		<div style="height: 100%; width : 15%; float:left;"></div>
		<?php
		echo ("<table><tr><td><br><strong>");
		echo ("Contact:  <a href = \"contactMessagePage.php?contactId=" . $_SESSION['contactId'] . "\">");
		echo ($_SESSION['contactName']);
		echo ("</a></strong></td></tr>");



		echo ("<tr><td><br><strong>Job Title: " . $_SESSION['contactPos'] . " [" . $_SESSION['contactDep'] . "] </strong></td></tr></table>");



		?>
	</div>

</body>
<footer>
	<strong>&copy; Spheres Web Solutions 2019</strong>
</footer>

</html>