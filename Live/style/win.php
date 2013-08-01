<?php
    include_once('scripts/connectvars.php');
    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die('Invalid query: ' . mysql_error());
    $id = preg_replace('#[^0-9]#i', '', $_SESSION['ID']); // filter everything but numbers
    
    $money = $money - $bid;
			
	$moneyUpdate = mysqli_query($dbc,"
		UPDATE USER SET MONEY = '$money'
		WHERE id = '$id'") or die("Could not connect: 2".mysql_error());
    
    

?>