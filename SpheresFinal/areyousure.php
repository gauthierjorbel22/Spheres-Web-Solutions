<?php session_start();
    require_once"SpheresPDO.php";

        
    if ( ! isset($_SESSION['SpheresUser']) || ! isset($_SESSION['userId'])) { //Redirects to the Login Page if the user is not logged in or if their session has timed out
        $_SESSION['fallacy'] = "Please provide your Log In details beneath";
        header('Location: login.php');
        return;
    }   

/* Flash message start
    if(!isset($_SESSION['fallacy'])){//We assume no error message is present
        $_SESSION['fallacy'] = false;
    }  */
?>

<!DOCTYPE html>
<html>
    <head>
    
        <link type="text/css" rel="stylesheet" href="areyousure.css">
        <link rel="icon" href="logoBall.png">
        <title>Spheres General Page</title>
    
    </head>
    <body>

	    <script type="text/javascript" src="jquery.min.js"> //JQuery
		</script>
		
		<div id = "back">
			<div id = "top">
					<h1 class="glow">Are You  Sure You Wish To Log Out?</h1>
		    </div>

		    <div id = "home">
<div style = "width:33%;"> </div>
					<a href = "general.php" style = "width:33%; text-decoration: none;"> <h3 class="no"><<< NO</h3> </a>
					<a href="general.php" style = "width:33%">
						<img class = "toprightHome"
							src = "Favicons/Home.png" 
							style = "
							width:70%;
								height: 100%;
								"
				        >
				      
				     </a>	
		    </div>

		    <div id = "out">
			
				<a href="logout.php" style = "width:33%">
				<img 
					class = "toprightLogout"
					src = "Favicons/Logout.png" 
					style = "
						width:70%;
						height: 100%;"
				> 
				</a>
				<a href = "logout.php" style = "width:33%; text-decoration: none;"> <h3 class="yes">YES >>></h3> </a>
				<div style = "width:33%;"> </div>
		    </div>

		</div>

    </body>
    <footer>
    	<strong>&copy; Spheres Web Solutions 2019</strong>
    </footer>
</html>