<?php
	//Getting all the information for the email

    require_once('connectvars.php');
    
    $fName="";
    $lName="";
    $team="";
    $amount="";
    $leader="";
    $playerPos="";
    $headers="";
    $auctionID="";
	
	 $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Could not connect ' . mysql_error());
	//Pulling auction info for the body
	$auctionID = $_SESSION['auctionID'];
	$playerInfo = mysqli_query($dbc,"
					SELECT
						BI.WINNER,
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
		$leader = $row["WINNER"];
		$playerPos = $row["POSITION"];
	}
	
	//Email Construction
	$subject = 'Higher Bid for ' . $fName . ' ' . $lName . ' - '.$playerPos.' -  ' . $team . ' for $' . $amount;
	$body = $leader.' has placed a higher bid for ' . $fName . ' ' . $lName . ' from the ' . $team. ' of $' .$amount.
			' Go to www.fantasyfranchisemode.com to place another bid.';

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