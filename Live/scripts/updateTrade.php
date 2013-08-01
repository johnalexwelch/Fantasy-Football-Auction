<?php
    
    error_reporting(E_ALL); 
    ini_set( 'display_errors','1');
    session_start(); // Start Session First Thing
    $id = preg_replace('#[^0-9]#i', '', $_SESSION['ID']); // filter everything but numbers

    require_once('connectvars.php');
    
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Could not connect ' . mysql_error());
    
    $tradeID = mysqli_real_escape_string($dbc, trim($_POST['tradeID']));
	$userID = mysqli_real_escape_string($dbc, trim($_POST['userID']));

	$bidUpdate = mysqli_query($dbc,"
		UPDATE auction.TRADE
		SET STATUS = 'C'
		WHERE ID = '$tradeID'") or die("Invalid query: 1".mysql_error());
	   
		$_SESSION['errorMsg']='';
		$_SESSION['tradeID'] = $tradeID;
		mysqli_close($dbc);
		header("Location: ../trading.php");
?>