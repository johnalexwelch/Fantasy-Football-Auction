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
	//Checks to see if it is past the cut off time of 9:00 pm
	$checkTime = '0400';
	if( date( 'Hi' ) >= $checkTime ) {
		$disabled='disabled="disabled"';
		$message ='Submissions are not allowed after 9 pm CST';
	}
	else{ $disabled="";}
	
	//Put on an image URL will help always show new when changed
	$cacheBuster = rand(999999999,9999999999999);
	
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
						MONEY
					FROM USER
					WHERE ID='$id'") or die('Invalid query: ' . mysql_error());;
	
      while($row = mysqli_fetch_array($userInfo)){
			$name= $row["USERNAME"];
            $money = $row["MONEY"];
		}
		
	if (isset($_POST['submit'])) {
		$fName = mysqli_real_escape_string($dbc, trim($_POST['fName']));
		$lName = mysqli_real_escape_string($dbc, trim($_POST['lName']));
		$team = mysqli_real_escape_string($dbc, trim($_POST['teams']));
		$bid = mysqli_real_escape_string($dbc, trim($_POST['bid']));
		
		if (!is_numeric($bid)){
			$errorMsg='Your bid must be a whole number';
		}
		else if ($bid > $money){
			$errorMsg='You dont have enough money';
		}
		else{
			$checkTime2='05';
			if( date( 'H' ) >= $checkTime2 ) {
				$bidInsert = mysqli_query($dbc,"
					INSERT INTO BIDS (USER_ID,FIRST_NAME,LAST_NAME,TEAM,BID,DATE,BID_TIME)
						VALUES('$id','$fName','$lName','$team','$bid',now(),now())") or die("Could not connect: 1".mysql_error());
				
				$money = $money - $bid;
				
				$moneyUpdate = mysqli_query($dbc,"
					UPDATE USER SET MONEY = '$money'
					WHERE id = '$id'") or die("Could not connect: 2".mysql_error());
	
				header("Location: http://localhost:8888/Auction/home.php");
			}
			else{
				$bidInsert = mysqli_query($dbc,"
					INSERT INTO BIDS (USER_ID,FIRST_NAME,LAST_NAME,TEAM,BID,DATE,BID_TIME)
						VALUES('$id','$fName','$lName','$team','$bid',now(),'2012-09-03 22:00:00')") or die("Could not connect: 1".mysql_error());
				
				$money = $money - $bid;
				
				$moneyUpdate = mysqli_query($dbc,"
					UPDATE USER SET MONEY = '$money'
					WHERE id = '$id'") or die("Could not connect: 2".mysql_error());
	
				header("Location: http://localhost:8888/Auction/home.php");
			}
		}	
	}
	
	echo $_SESSION['errorMsg'];
	include_once('scripts/auction.php');
	
?>
<html>
    <head>
<!--		<meta http-equiv="refresh" content="5; URL=http://localhost:8888/Auction/home.php">-->
        <title></title>
		<link rel="stylesheet" type="text/css" href="style/landing.css" />
		<script type="text/javascript" src="js/validate.js"></script>
		<script type="text/javascript" src="js/counter.js"></script>
    </head>
    <body>
        <h1><?php echo $name; ?></h1>
        <h2><?php echo $money; ?></h2>
	  <a href="scripts/logout.php"> Log Out </a>	
		
		<section>
			<div class="error_box"></div>
			<?php echo $errorMsg;?>
			<form id="nominateForm" method="post" name="nominateForm" action="<?php echo $_SERVER['PHP_SELF'];?>">
				<input type="text" name="fName" id="fName"/>
				<label for="lName">Player's First Name </label>	
				<input type="text" name="lName" id="lName"/>
				<label for="lName">Player's Last Name </label>
				<select name="teams" id="teams">
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
				</select>
				<label for="teams"> Team </label>
				<input type="text" name="bid" id="bid">
				<label for="bid">Starting Bid </label>
				<?php echo $message;?>
				<input type="submit" name="submit" id="submit" value="Submit" <?php echo $disabled; ?>/>
			</form>
			<?php echo $feedDisplayList; ?>
		</section>
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
