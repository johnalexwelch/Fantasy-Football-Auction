<?php
    
    error_reporting(E_ALL); 
    ini_set( 'display_errors','1');
    session_start(); // Start Session First Thing
    $id = preg_replace('#[^0-9]#i', '', $_SESSION['ID']); // filter everything but numbers

    require_once('connectvars.php');
    
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Could not connect ' . mysql_error());
    $userInfo = mysqli_query($dbc,"
					SELECT
						USERNAME,
						MONEY
					FROM USER
					WHERE ID='$id'") or die('Invalid query first: ' . mysql_error());
	
    while($row = mysqli_fetch_array($userInfo)){
		$name= $row["USERNAME"];
        $money = $row["MONEY"];
    }
    
    $auctionID = mysqli_real_escape_string($dbc, trim($_POST['auctionID']));

    $newBid = mysqli_real_escape_string($dbc, trim($_POST['bidAmount']));


    $detailQuery = mysqli_query($dbc,"
					SELECT
					   BID
					FROM BIDS
					WHERE ID = '$auctionID'") or die ('Invalid query bid: '. mysql_error());
        
    while ($row = mysqli_fetch_array($detailQuery)) { 
		$bidAmount = $row["BID"];
		$bidUpdate ='';
	 
		if (!is_numeric($newBid)){
			$_SESSION['errorMsg']='Your bid must be a whole number';
			header("Location: ../home.php");
		}
	   
		else if ($newBid > $money){
			$_SESSION['errorMsg']='You dont have enough money';
			header("Location: ../home.php");
		}
		else if($newBid <= $bidAmount){
			$_SESSION['errorMsg']='You did not bid enough';
			header("Location: ../home.php");
		}
		else{
			$checkTime2='23';
			if( date( 'H' ) >= $checkTime2 ) {
				$bidUpdate = mysqli_query($dbc,"
					UPDATE auction.BIDS
					SET BID = '$newBid', USER_ID = '$id', BID_TIME = now() , WINNER = '$name'
					WHERE ID = '$auctionID'") or die("Invalid query: 1".mysql_error());
		   
				$_SESSION['errorMsg']='';
				$_SESSION['auctionID'] = $auctionID;
				include_once('newBidEmail.php');
				header("Location: ../home.php");
			}
			else {
				$bidDate = date("Y-m-d");
				$bidDateFinal = $bidDate.' 22:00:00';
				$bidUpdate = mysqli_query($dbc,"
					UPDATE auction.BIDS
					SET BID = '$newBid', USER_ID = '$id', BID_TIME = '$bidDateFinal', WINNER = '$name'
					WHERE ID = '$auctionID'") or die("Invalid query: 1".mysql_error());
		   
			    $_SESSION['errorMsg']='';
			    $_SESSION['auctionID'] = $auctionID;
			    include_once('newBidEmail.php');
			    header("Location: ../home.php");
			   
			}
		}
	}
	mysqli_close($dbc);
?>