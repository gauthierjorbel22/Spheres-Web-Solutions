<?php

	//Fix the "SpheresUser" naming convention (its not intuitive)

	session_start();
	require_once"SpheresPDO.php";
	if(!isset($_SESSION['fallacy'])){
	    $_SESSION['fallacy'] = false;
	}  

	if(isset($_POST['password'])&& isset($_POST['userId'])){
		
		if (strlen($_POST['password'])< 1 || strlen($_POST['userId'])< 1) {//[validation on the sever inspecting vacant mandatory fields -> this if() statement should not execute provided that the user's JavaScript is enabled due to the script tag spanning across line 87 till 115] 
		        $_SESSION['fallacy'] = "ERROR: Neither Username nor Password should be vacant";
		        header("Location: login.php");
		        return; 
		}else{

	        $inspectingPass = hash('md5', 'spheres123'.$_POST['password']);

	        $statement = $pdo->prepare(
	            'SELECT userName, userId 
	            FROM users
	            WHERE signInId = :signInId AND password = :password'
	        );

	        $statement->execute(array( 
	            ':signInId' => $_POST['userId'], ':password' => $inspectingPass
	        ));

	        $resultRow = $statement->fetch(PDO::FETCH_ASSOC);

	       if ( $resultRow === false ) { //If Statement will only execute in the case where there exists no user matched to the provided Username and Password
	            $_SESSION['fallacy'] = 'ERROR: The provided Username and Password does not match those of any valid users';
	            header( 'Location: login.php' ) ;
	            return;
	        }else{
	            $_SESSION['SpheresUser'] = $resultRow['userName'];
	            $_SESSION['userId'] = $resultRow['userId'];
	            $_SESSION['signInId'] = $_POST['userId'];
	            header( 'Location: general.php' ) ;
	            return;
	        }   
	    }
	}
?>
<!DOCTYPE html>
<html>
    <head>
        <link type="text/css" rel="stylesheet" href="login.css">
        <link rel="icon" href="logoBall.png">
              
		<title>Spheres Login Page</title>
		
			<!--load jQuery library-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script>
		// this script is just to higlight the input field the focus point with a blue color and the blur field with the transparent one.
		$(document).ready(function() {
			$("input").focus(function() {
				$(this).css("background-color", "white");
			});
			$("input").blur(function() {
				$(this).css("background-color", "transparent");
			});
		});
	</script>

    </head>
    <body>
	    	<script type="text/javascript" src="jquery.min.js"> //JQuery
			</script>

	        <img src="Favicons/logoTransparent.png" align="middle" >
	        
	        <br><br>

	        <form method="POST" class = "myForm">
	            <br><div id = "error" style="color: red;">
	       		<?php
		            if ( $_SESSION['fallacy'] !== false ) { //Flashmessage of the error returned by server-side validation
		                echo('<p>'.$_SESSION['fallacy']."</p>");
		            }else{
		            	echo ("<br>");
		            }
		            unset($_SESSION['fallacy']);
	            ?>
            </div><br>
	            <label for="userId">Username</label>
	            <input type="text" name="userId" id="userId"><br>
	            <label for="pw">Password&nbsp</label>
	            <input type="password" name="password" id="password"><br/>
	            <input type="submit" value="Log In" id="submit" onclick="return validateUsernameAndPassword();"><br>
 
				<!--this is the checkbox to show and hide the password-->
		<input type="checkbox" onclick="myFunction()">Show Password<br>
	           	<a href="help.html" target = "blank">
	               <img src="helpLogo.png" id="helpLogo">
	            </a>
	        </form>
			<!--this one is the javascript code to show and hide the password-->
	<script>
		function myFunction() {
			var x = document.getElementById("password");
			if (x.type === "password") {
				x.type = "text";
			} else {
				x.type = "password";
			}
		}
	</script>	        

	        <p>
	           <a href="help.html" id="helpMe" target="blank"><strong>Forgotten username or password?</strong></a>
	        </p>


			<script> //[validation on the client's browser inspecting vacant mandatory fields] 
				$(document).ready(function(){
					vacantE = true;
				});
				function validateUsernameAndPassword() {
				    console.log('Validating mandatory fields');
				        try {
				            userId = document.getElementById('userId').value;
				            password = document.getElementById('password').value;
				            console.log("Performing Client-side validation for userId:"+userId+" password="+password);
				            if (userId == "" || userId == null || password == "" || password == null) {
				    	        if(vacantE == true){
				    	        $('#error').empty();
				    	        $('#error').append("ERROR: Neither Username nor Password should be vacant");
				    	        vacantE = false;				    	   
				            }
								console.log('Client-side validation');
				    	        return false;
				        }
				            //console.log('provided mandatory fields');
				            return true; 
				        } catch(e) {
				        	//console.log('catch');
				            return false;
				        }
				        //console.log('outside try');
				    return false;
				}
			</script>

    </body>
    <footer>
    	<strong>&copy; Spheres Web Solutions 2019</strong>
    </footer>
</html>



