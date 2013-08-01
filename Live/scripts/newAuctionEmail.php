<?php
	//Getting all the information for the email
    require_once('connectvars.php');
    $playerPos="";
    $amount ="";
    $leader="";
    $amount="";
    $headers="";
	 $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Could not connect ' . mysql_error());
	//Pulling auction info for the body
	$auctionID = $_SESSION['newAuctionId'];
	$playerInfo = mysqli_query($dbc,"
					SELECT
						US.USERNAME,
						BI.FIRST_NAME,
						BI.LAST_NAME,
						BI.TEAM,
						BI.POSITION,
						BI.BID
					FROM auction.BIDS BI
						LEFT JOIN auction.USER US
							ON US.ID = BI.USER_ID
					WHERE BI.ID='$auctionID'") or die ('Invalid query 1: ' . mysql_error());
	
	while($row = mysqli_fetch_array($playerInfo)){
		$fName= $row["FIRST_NAME"];
		$lName = $row["LAST_NAME"];
		$team= $row["TEAM"];
		$amount= $row["BID"];
		$leader = $row["USERNAME"];
		$playerPos = $row["POSITION"];
	}
	
	//Email Construction
	$subject = 'New auction for ' . $fName . ' ' . $lName . ' - '.$playerPos.' -  ' . $team . ' for $' . $amount;
	$body = $leader. ' has bid $'. $amount.' for ' . $fName . ' ' . $lName . ' on the ' . $team.
			'The clock will start at 10 p.m.'.
			'Go to www.fantasyfranchisemode.com to place a higer bid.';

	//Pulling all email address so that everyone gets the email
    $userInfo = mysqli_query($dbc,"
					SELECT 
						EMAIL
					FROM USER") or die('Invalid query: ' . mysql_error());
	
	while($row = mysqli_fetch_array($userInfo)){
		$email = $row["EMAIL"];
		$headers = "From: auctionhouse@fantasyfranchisemode.com" .
	 mail($email,$subject,$body,$headers);
	}
	
	mysqli_close($dbc);
?>