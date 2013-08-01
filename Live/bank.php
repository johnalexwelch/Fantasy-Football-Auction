<?php
      error_reporting(E_ALL); 
    ini_set( 'display_errors','1');
    session_start(); // Start Session First Thing
    $id = preg_replace('#[^0-9]#i', '', $_SESSION['ID']); // filter everything but numbers

    require_once('scripts/connectvars.php');
    $bankDisplayList = '';
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Could not connect ' . mysql_error());
    $bankInfo = mysqli_query($dbc,"
					SELECT
						USERNAME,
						MONEY
					FROM USER
                              ORDER BY USERNAME ASC") or die('Invalid query first: ' . mysql_error());
	
      while($row = mysqli_fetch_array($bankInfo)){
            $name= $row["USERNAME"];
            $money = $row["MONEY"];
            
            $bankDisplayList .= '<div id="auctionContainer">
                        <div id="auctionBidAmount">
                            $'.$money.'
                        </div>
                        <div id="auctionInfo">
                            <div id="winnerName">
                                '.$name.'
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
				<h1 class="teamName">The Bank</h1>
			</header>
			  <div id="liveAuctions">
				<?php  echo $bankDisplayList; ?>
			  </div>
		</div>
    </body>
</html>