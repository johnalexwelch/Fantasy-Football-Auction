<?php
    $tradeDisplayList = '';
    $tradeButton = '';
    $id = preg_replace('#[^0-9]#i', '', $_SESSION['ID']);
    // All Auctions output construction
    $tradeQuery = mysqli_query($dbc,
                    "SELECT
						TR.FIRST_NAME,
						TR.LAST_NAME,
						TR.TEAM,
						TR.NEED,
						TR.POSITION,
						TR.ID,
						US.USERNAME,
						US.EMAIL,
                        BI.USER_ID
                        
                    FROM auction.TRADE TR
                        LEFT JOIN auction.USER US
							ON US.ID = TR.USER_ID
                    WHERE TR.STATUS IN (O)
                    ORDER BY TR.ID DESC") or die("Feed Query: ".mysql_error());
    
    
    while ($row = mysqli_fetch_array($tradeQuery)) {
        
        $firstName = $row["FIRST_NAME"];
        $lastName = $row["LAST_NAME"];
        $teamName = $row["TEAM"];
        $userName = $row["USERNAME"];
        $tradeID = $row["ID"];
        $playerPos = $row["POSITION"];
		$need = $row["NEED"];
        $uID = $row['USER_ID'];
		$email = $row["EMAIL"];
        
		if ($uID == $id){	
		  $tradeButton = '<input type="submit" value = "Remove Trade" class ="bidSubmit" id="remove'.$tradeID.'" name="="remove'.$tradeID.'">';
		}
		else{
		  $tradeButton = '<a class ="bidSubmit" href="mailto:'.<?php echo $email;?>.'" Contact Owner </a>'
		}
		
        $tradeDisplayList .= '
            <div id="auctionContainer">
                <div id="auctionBidAmount">
                    $'.$userName.'
                </div>
                <div id="auctionInfo">
                    <div id="playerInfo">
                        '.$firstName.' '.$lastName.'</br>'.$playerPos.' - '.$teamName.'
                    </div>
                    <div id="winnerName">
                        '.$need.'
                    </div>
                </div>'.
                if ($uID = $id){
                    .'<form method="post" action="scripts/updateTrade.php" id="trade'.$tradeID.'>" name="trade'.$tradeID.'">
                        <div id="newBid">
                            <div id="auctionBid">
								<input type="hidden" name ="tradeID" id="tradeID" value="'.$tradeID.'">
								<input type="hidden" name ="userID" id="userID" value="'.$uID.'">
                                <input type="submit" value = "Remove Trade" class ="bidSubmit" id="trade'.$tradeID.'" name="trade'.$tradeID.'">
                                <input type="submit" value = "Mark as Completed" class ="bidSubmit" id="trade'.$tradeID.'" name="trade'.$tradeID.'">
                            </div>		
                        </div>
                        </form>
                    </div>'.
                }else{
                    .'<form method="post" action="scripts/contact.php" id="trade'.$tradeID.'>" name="trade'.$tradeID.'">
                        <div id="newBid">
                            <div id="auctionBid">
                                .'$tradeButton.'
                            </div>		
                        </div>
                        </form>
                    </div>'
                }
    }
?>