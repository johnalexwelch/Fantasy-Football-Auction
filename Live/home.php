<?php
	error_reporting(E_ALL); 
	ini_set( 'display_errors','1');
    
      session_start(); // Start Session First Thing
      require_once('scripts/connectvars.php');

	if (!isset($_SESSION['errorMsg'])){
		$_SESSION['errorMsg'] ='';
	}

	//Variable Initialization
	$errorMsg='';
	$feedDisplayList= "";
	$firstName = "";
	$lastName = "";
	$teamName = "";
	$bidAmount = "";
	$userName = "";
	$disabled = "";
	$message = "";
	$bank = "";
	
	//Checks to see if it is past the cut off time of 9:00 pm
	$checkTime = '2230';
	if( date( 'Hi' ) >= $checkTime ) {
		$disabled='disabled="disabled"';
		$errorMsg ='Submissions are not allowed after 9:30 pm CST';
	}
	else{ $disabled="";}
	
	//Establish the page id
	if (isset($_SESSION['ID'])) {
		$id = preg_replace('#[^0-9]#i', '', $_SESSION['ID']); // filter everything but numbers
	}
	else {
		header('Location: signup.html');
		exit();
	}

	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Invalid query: ' . mysql_error());

      $userInfo = mysqli_query($dbc,"
					SELECT
						USERNAME,
						MONEY,
						ADMIN
					FROM USER
					WHERE ID='$id'") or die('Invalid query: ' . mysql_error());;
	
      while($row = mysqli_fetch_array($userInfo)){
			$name= $row["USERNAME"];
			$bank = $row["MONEY"];
			$admin = $row["ADMIN"];
		}
	
	if ($admin == 1){
		$adminLink = '<a href="bank.php">Money</a>';
	}
	else{
		$adminLink = '';
	}
	if (isset($_POST['submit'])) {
		$fName = mysqli_real_escape_string($dbc, trim($_POST['fName']));
		$lName = mysqli_real_escape_string($dbc, trim($_POST['lName']));
		$team = mysqli_real_escape_string($dbc, trim($_POST['teams']));
		$bid = mysqli_real_escape_string($dbc, trim($_POST['bid']));
		$position = mysqli_real_escape_string($dbc, trim($_POST['position']));
		
		if (!is_numeric($bid)){
			$errorMsg='Your bid must be a whole number';
		}
		else if ($bid > $bank){
			$errorMsg='You dont have enough money';
		}
		else{
			$checkTime2='23';
			$bidDate = date("Y-m-d");
			$bidDateFinal = $bidDate.' 22:00:00';
			if( date( 'H' ) >= $checkTime2 ) {
				$bidInsert = mysqli_query($dbc,"
					INSERT INTO BIDS (USER_ID,FIRST_NAME,LAST_NAME,TEAM,POSITION,BID,DATE,BID_TIME,WINNER)
						VALUES('$id','$fName','$lName','$team','$position','$bid',now(),'$bidDateFinal','$name')") or die("Could not connect: 1".mysql_error());
	
				header("Location: home.php");
			}
			else{
				$bidInsert = mysqli_query($dbc,"
					INSERT INTO BIDS (USER_ID,FIRST_NAME,LAST_NAME,TEAM,POSITION,BID,DATE,BID_TIME,WINNER)
						VALUES('$id','$fName','$lName','$team','$position','$bid',now(),'$bidDateFinal','$name')") or die("Could not connect: 1".mysql_error());
			
				$aid = mysqli_insert_id($dbc);
				$_SESSION['newAuctionId']=$aid;
				include_once('scripts/newAuctionEmail.php');
				header("Location: home.php");
			}
		}	
	}
	
	include_once('scripts/auction.php');
	mysqli_close($dbc);
?>
<html>
    <head>
<!--		<meta http-equiv="refresh" content="5; URL=http://localhost:8888/Auction/home.php">-->
        <title></title>
		<link rel="stylesheet" type="text/css" href="style/landing.css" />
		<link rel="stylesheet" type="text/css" href="style/home.css" />
		
		<script type="text/javascript" src="js/validate.js"></script>
		<script type="text/javascript" src="js/counter.js"></script>
    </head>
    <body>
		<div id="sideNav">
			<div id="start""><h2>Start A New Auction</h2></div>
			<div id="live"><h2>Current Auctions</h2></div>
		</div>
		<div id="mainWrapper">
			<div id="mainNav">
				<nav>
					<ul>
						<li><?php echo $adminLink;?></li>
						<!--<li><a href="tradingBlock.php">Trading Block </a></li>-->
						<li><a href="winners.php">Past Winners</a></li>
						<li><a href="scripts/logout.php"> Log Out </a></li>
						
					</ul>
				</nav>
			</div>
			
			<div id="mainInfo">
				<header>
					<h1 class="teamName"><?php echo $name;?></h1>
					<h2 class="bankRoll">$<?php echo $bank;?></h2>
				</header>
			</div>
			
			<section>
				<div id="playerSelect">
					<div class="error_box"></div>
					<div id="errors">
						<?php echo $errorMsg;?> </br>
						<?php echo $_SESSION['errorMsg'];?>
					</div>
					<form id="nominateForm" method="post" name="nominateForm" action="<?php echo $_SERVER['PHP_SELF'];?>">
						<div id="formRow">
							<div id="formSection"><label for="lName">Player's First Name </label></div>
							<div id="formSection"><input type="text" name="fName" id="fName"/></div>
						</div>
						<div id="formRow">
							<div id="formSection"><label for="lName">Player's Last Name </label></div>
							<div id="formSection"><input type="text" name="lName" id="lName"/></div>
						</div>
						<div id="formRow">
							<div id="formSection"><label for="teams"> Team </label></div>
							<div id="formSection">
								<select class="styled-select" name="teams" id="teams">
									<option value="Cardinals"> Arizona Cardinals</option>
									<option value="Falcons"> Atlanta Falcons</option>
									<option value="Bill"> Buffalo Bills</option>
									<option value="Ravens"> Baltimore Ravens</option>
									<option value="Bears"> Chicago Bears</option>
									<option value="Bengals"> Cincinnati Bengals</option>
									<option value="Browns"> Cleveland Browns</option>
									<option value="Panthers"> Carolina Panthers</option>
									<option value="Cowboys"> Dallas Cowboys</option>
									<option value="Broncos"> Denver Broncos</option>
									<option value="Lions"> Detroit Lions</option>
									<option value="Packers"> Green Bay Packers</option>
									<option value="Texans"> Houston Texans</option>
									<option value="Colts"> Indianapolis Colts</option>
									<option value="Jaguars"> Jacksonville Jaguars</option>
									<option value="Cheifs"> Kansas City Cheifs</option>
									<option value="Dolphins"> Miami Dolphins</option>
									<option value="Vikings"> Minnesota Vikings</option>
									<option value="Patriots"> New England Patriots</option>
									<option value="Giants"> New York Giants</option>
									<option value="Jets"> New York Jets</option>
									<option value="Saints"> New Orleans Saints</option>
									<option value="Raiders"> Oakland Raiders</option>
									<option value="Eagles"> Philadelphia Eagles</option>
									<option value="Steelers"> Pittsburgh Steelers </option>
									<option value="Chargers"> San Diego Chargers</option>
									<option value="49ers"> San Francisco 49ers</option>
									<option value="Rams"> St Louis Rams</option>
									<option value="Seahawks"> Seattle Seahawks</option>
									<option value="Buccaneers"> Tampa Bay Buccaneers</option>
									<option value="Titans"> Tennessee Titans</option>
									<option value="Redskins"> Washington Redskins</option>
								</select></div>
						</div>
						<div id="formRow">
							<div id="formSection"><label for="position"> Position </label></div>
							<div id="formSection">
								<select class="styled-select" name="position" id="position">
									<option value="QB"> QB </option>
									<option value="RB"> RB </option>
									<option value="WR"> WR </option>
									<option value="TE"> TE </option>
									<option value="K"> K </option>
									<option value="DEF"> DEF </option>
									
								</select></div>
						</div>
						<div id="formRow">
							<div id="formSection"><label for="bid">Starting Bid </label></div>
							<div id="formSection"><input type="text" name="bid" id="bid"></div>
						</div>
						<!--<?php echo $message;?>-->
						<div id="formRow">
							<div id="formSection">
								<div id="buttonRow">
									<p class="submit">
										<input type="submit" id= "submit" name="submit"  value="Submit" <?php echo $disabled; ?>/>
									</p>
								</div>
							</div>
						</div>
					</form>
				</div>
			</section>
			<div id="liveAuctions">
						<?php echo $feedDisplayList; ?>
					</div>
		</div>
		<script type="text/javascript">
		new FormValidator('nominateForm', [
            {name: 'fName', rules: 'required'},
            {name: 'lName', rules: 'required'},
            {name: 'bid', rules: 'required'},
            //{name: 'code', rules: 'required'},
            ],
            
            
            function(errors, event) {
                var SELECTOR_ERRORS = $('.error_box');
                    
                if (errors.length > 0) {
                    SELECTOR_ERRORS.empty();
                    
                    for (var i = 0, errorLength = errors.length; i < errorLength; i++) {
                        SELECTOR_ERRORS.append(errors[i].message + '<br />');
                    }
                    SELECTOR_ERRORS.fadeIn(200);
                } else {
                    SELECTOR_ERRORS.css({ display: 'none' });
                }
                
            }
            );
    </script>
      </body>
</html>
