<?php
    error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
    
    session_start(); // Start Session First Thing
    require_once('scripts/connectvars.php');

	if (!isset($_SESSION['errorMsg'])){
		$_SESSION['errorMsg'] ='';
	}
    
    //Establish the page id
	if (isset($_SESSION['ID'])) {
		$id = preg_replace('#[^0-9]#i', '', $_SESSION['ID']); // filter everything but numbers
	}
	else {
		header('Location: signup.html');
		exit();
	}
    
    //Variable Initialization
	$errorMsg='';
	$winnerDisplayList= "";
	$firstName = "";
	$lastName = "";
	$teamName = "";
	$bidAmount = "";
	$userName = "";
	$date="";
	$playerPos="";
    
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Cannot Connect: ' . mysql_error());
    
    $winnerInfo = mysqli_query($dbc,"
				    SELECT *
				    FROM BIDS
				    WHERE WINNER IS NOT NULL
                    AND CLOSED = '1'
				    ORDER BY ID DESC ") or die('Invalid query: ' . mysql_error());
	
        while($row = mysqli_fetch_array($winnerInfo)){
		$firstName = $row["FIRST_NAME"];
            $lastName = $row["LAST_NAME"];
            $teamName = $row["TEAM"];
            $bidAmount = $row["BID"];
		$date= $row["DATE"];
		$playerPos=$row["POSITION"];
		$userName=$row["WINNER"];
            
            $winnerDisplayList .= '
                    <div id="auctionContainer">
                        <div id="auctionBidAmount">
                            $'.$bidAmount.'
                        </div>
                        <div id="auctionInfo">
                            <div id="playerInfo">
                                '.$firstName.' '.$lastName.'</br>'.$playerPos.' - '.$teamName.'
                            </div>
                            <div id="winnerName">
                                '.$userName.' on '.$date.'
                            </div>
				</div>
			  </div>';
        }
?>
<html>
    <head>
	  <link rel="stylesheet" type="text/css" href="style/landing.css" />
	  <link rel="stylesheet" type="text/css" href="style/home.css" />
    </head>
    <body>
	  <div id="mainWrapper">
		<div id="mainNav">
			<nav>
				<ul>
				    <li><a href="home.php"> Home </a></li>
					<li><a href="scripts/logout.php"> Log Out </a></li>
				</ul>
			</nav>
		</div>
		
		<div id="mainInfo">
			<header>
				<h1 class="teamName">Past Winners</h1>
			</header>
			  <div id="liveAuctions">
				<?php  echo $winnerDisplayList; ?>
			  </div>
		</div>
    </body>
</html>