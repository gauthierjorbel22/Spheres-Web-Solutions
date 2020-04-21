<?php session_start();
require_once "SpheresPDO.php";


if (!isset($_SESSION['SpheresUser']) || !isset($_SESSION['userId'])) { //Redirects to the Login Page if the user is not logged in or if their session has timed out
	$_SESSION['fallacy'] = "Please provide your Log In details beneath";
	header('Location: login.php');
	return;
}

/* Flash message start
    if(!isset($_SESSION['fallacy'])){//We assume no error message is present
        $_SESSION['fallacy'] = false;
    }  */

$sql = "select users.latestGroupMessageID AS mostRecent FROM `users` WHERE users.userId = :user;";

$statement = $pdo->prepare(
	$sql
);

$statement->execute(array(
':user' => $_SESSION['userId'],
));

while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) {
$_SESSION["lastGroupMessageID"] = $resultRow["mostRecent"];
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

?>

<!DOCTYPE html>
<html>

<head>

	<link type="text/css" rel="stylesheet" href="general.css">

	<title>Spheres General Page</title>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"> //JQuery
	<script>
		//this is the jQuery for the slide toggle on the group title
		$(document).ready(function() {
			$("#groupTitle").click(function() {
				$("#slide").slideToggle("slow");

			});
		});
	</script>

</head>

<body>


	<!--	<script type="text/javascript" src="jquery.min.js"> //JQuery
			</script>-->

	<a href="general.php">
		<img class="toprightHome" src="Favicons/Home.png" style="
			width:10%;
				height: 7%;">

	</a>
	<a href="contacts.php">
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
	<!--The following script tag contains the code for the profile photo that will execute upon the document's initial load (super similair)-->
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
	




<script type="text/javascript"> //Dynamic rewriting of the contact list when lastMessageID changes
			$(document).ready(function() {
				
				window.console && console.log('Original unread GroupMessageID= '+<?php echo($_SESSION["lastGroupMessageID"]);?> );
				window.console && console.log('Original unread messageID= '+<?php echo($_SESSION["lastPersonalmessageID"]);?> );
				window.console && console.log('checkGroupMessageID() called Originally');
				temp = <?php echo($_SESSION["lastGroupMessageID"]);?>;
				window.console && console.log('Original Temp ' + temp);
				temp2 = <?php echo($_SESSION["lastPersonalmessageID"]);?>;
				window.console && console.log('Original Temp ' + temp2);
				checkGroupMessageID();
			});

			function checkGroupMessageID() { //conditionally called from updateMsgCheck()
				$.ajaxSetup({
					cache: false
				});

				window.console && console.log('Requesting MostRecentGroupMessageId');
					$.getJSON('MostRecentGroupMessageId.php', function(rowz) {
					window.console && console.log('JSON Received');
					window.console && console.log(rowz);
					arow = rowz[0];
					if(temp != arow){
						window.console && console.log('diffrent, temp was: ' + temp + ' and new is ' + arow);
						temp = arow;
						updateTheGroupChatlist();
					}
					checkMessageID();

					window.console && console.log('Gona Call Again - temp was: ' + temp + ' and new is ' + arow);
					setTimeout('checkGroupMessageID()', 4000);
				});
			}

function updateTheGroupChatlist() {
window.console && console.log('updateTheGroupChatlist() was called');
location.reload();

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







</script>

<div class="rightBottom">









		<div id="groupTitle" style = "cursor: pointer;"><strong>Groups</strong></div>
		<div id="slide" > This is where you can see all the groups that you have been allocated to by the administrator</div>
		<center>
			<table style="width:90%">

				<?php
				/*
 	use spehersitsp;
SELECT  groups.groupId, groupuserlist.groupId, groups.groupName AS groupName, groupuserlist.userId AS userId , groups.photo AS photo FROM groups JOIN groupuserlist ON groups.groupId = groupuserlist.groupId WHERE groupuserlist.userId = 1 ORDER BY groups.groupName;*/

				$sql = "Select groups.groupId AS groupId, groupuserlist.groupId, groupuserlist.unreadNotifications AS unreadNotifi, groups.groupName AS groupName, groups.repository AS groupRepository, groupuserlist.userId, groups.photo AS photo FROM groups JOIN groupuserlist ON groups.groupId = groupuserlist.groupId WHERE groupuserlist.userId = :userId  ORDER BY unreadNotifi DESC, groupName ASC";
				$statement = $pdo->prepare(
					$sql
				);

				$statement->execute(array(
					':userId' => $_SESSION['userId']
				));

				while ($resultRow = $statement->fetch(PDO::FETCH_ASSOC)) { //If Statement will only execute in the case where there exists no user matched to the provided Username and Password

					echo ("<tr><td style = \"width:6%; height:6%;margin:0px; padding:0px;\"><center> <br><strong><a href = \"groupMessagePage.php?groupId=" . htmlentities($resultRow['groupId']) . "\">");
					echo ("

<img src = \"Photos/" . htmlentities($resultRow['photo']) . ".jpg\" style = \"width:100%; height: 100%;\"></a>");

					echo ("</strong></center></td>");


					echo ("<td><center> <br><strong>");
					echo ("<a href = \"groupMessagePage.php?groupId=" . htmlentities($resultRow['groupId']) . "\">");
					echo (htmlentities($resultRow['groupName']));
					echo ("</a></strong></center></td>");

					echo ("<td><center> <br><strong>");
					echo ("New Messages:  <a href = \"groupMessagePage.php?groupId=" . htmlentities($resultRow['groupId']) . "\" style= \"text-decoration: none; color:red;\">");
					echo (htmlentities($resultRow['unreadNotifi']));
					echo ("</a></strong></center></td>");

					echo ("<td><center><br><strong>Repository:  <a href = \"");
					echo (htmlentities($resultRow['groupRepository']));
					echo ("\" target = \"blank\">" . htmlentities($resultRow['groupName']) . "</strong></center></td></tr>");
				}


				?>

			</table>

	</div>
	<center>
		<div class="rightTop">
			<div class="galleryContainer">
    <div class="slideShowContainer">
        <div id="playPause" onclick="playPauseSlides()"></div>
        <div onclick="plusSlides(-1)" class="nextPrevBtn leftArrow"><span class="arrow arrowLeft"></span></div>
        <div onclick="plusSlides(1)" class="nextPrevBtn rightArrow"><span class="arrow arrowRight"></span></div>
        <div class="captionTextHolder"><p class="captionText slideTextFromTop"></p></div>
        <div class="imageHolder">
            <img src="reminders/img_1.jpg">
            <p class="captionText">Financial Team Reminder - 01</p>
        </div>
        <div class="imageHolder">
            <img src="reminders/img_2.jpg">
            <p class="captionText">Space Rangers Group - 02</p>
        </div>
        <div class="imageHolder">
            <img src="reminders/img_3.jpg">
            <p class="captionText">Spheres Web Solutions Reminder - 03</p>
        </div>
        <div class="imageHolder">
            <img src="reminders/img_4.jpg">
            <p class="captionText">Random Reminder - 04</p>
        </div>
    </div>
    <div id="dotsContainer"></div>
</div>
<script src="remindersSlideshow.js"></script>
		</div>
		</div>
	</center>

</body>
<footer>
	<strong>&copy; Spheres Web Solutions 2019</strong>
</footer>

</html>