<?php

    $unith =3600;        
    $unitm =60;
    $endAuction="";
    $winnerMessage="Place Your Bid";
    $hh="";
    $mm="";
    $ss="";
    $playerPos="";
    $money2="";
    
    $id = preg_replace('#[^0-9]#i', '', $_SESSION['ID']);
    // All Auctions output construction
    $feedQuery = mysqli_query($dbc,
                    "SELECT
						BI.FIRST_NAME,
						BI.LAST_NAME,
						BI.TEAM,
						BI.BID,
						BI.POSITION,
						BI.ID,
                                    BI.CLOSED,
                                    BI.WINNER,
						TIMESTAMPDIFF(second,BI.BID_TIME,now()) AS BID_TIME,
						US.USERNAME,
                                    US.MONEY,
                                    BI.USER_ID
                        
                    FROM auction.BIDS BI
                        LEFT JOIN auction.USER US
							ON US.ID = BI.USER_ID
                    WHERE BI.DATE = DATE(NOW())
                    ORDER BY BI.ID DESC") or die("Feed Query: ".mysql_error());
    
    
    while ($row = mysqli_fetch_array($feedQuery)) {
        
        $firstName = $row["FIRST_NAME"];
        $lastName = $row["LAST_NAME"];
        $teamName = $row["TEAM"];
        $bidAmount = $row["BID"];
        $userName = $row["USERNAME"];
        $bidID = $row["ID"];
        $totalTime = $row["BID_TIME"];
	  $playerPos = $row["POSITION"];
        $money = $row['MONEY'];
        $winner = $row['WINNER'];
        $close = $row['CLOSED'];
        $uID = $row['USER_ID'];
        
        if( date('H') >= '23' ){
            $hh = intval($totalTime / $unith);    
            $ss_remaining = ($totalTime - ($hh * 3600));        
            
            $mm = intval($ss_remaining / $unitm);    
            $ss = ($ss_remaining - ($mm * 60));
            
            $hh="";
            
            if ($mm < 0){
                $mm= '';
            }
            else if ($mm< 10) {
                  $mm="0".$mm.":";
            }
            else if ($mm>=10){
                $endAuction='disabled="disabled"';
                $winnerMessage='This Auction is Closed';
                
                $money2 = $money - $bidAmount;
                
                if ($close <> 1){		
                    $moneyUpdate = mysqli_query($dbc,"
                        UPDATE USER SET MONEY = '$money2'
                        WHERE ID = '$uID'") or die("Could not connect: 2".mysql_error());
                    $closequery = mysqli_query($dbc,"
                        UPDATE BIDS SET CLOSED = '1'
                        WHERE ID = '$bidID'") or die("Could not close bid".mysql_error());
                }
                
                $hh='';
                $ss='';
            }
            else {
                $endAuction="";
                $winnerMessage="Place Your Bid";
            }
            
            if ($ss < 0){
                $ss= '';
            }
            else if ($ss< 10) {
                if ($mm>=10){
                    $ss='';
                            $mm='';
                }
                else{
                  $ss="0".$ss;
                }
            }
        }
    
        $feedDisplayList .= '
            <form method="post" action="scripts/update.php" id="auction'.$bidID.'>" name="auction'.$bidID.'">
			  <div id="auctionContainer">
                        <div id="auctionBidAmount">
                            $'.$bidAmount.'
                        </div>
                        <div id="auctionInfo">
                            <div id="playerInfo">
                                '.$firstName.' '.$lastName.'</br>'.$playerPos.' - '.$teamName.'
                            </div>
                            <div id="winnerName">
                                '.$winner.'
                            </div>
				</div>
				<div id="newBid">
					<div id="auctionBid">
				
							    <input type="text" class="bidInput" name="bidAmount" id="bidAmount"/>
							    <input type="hidden" name ="auctionID" id="auctionID" value="'.$bidID.'">
							    <input type="submit" class ="bidSubmit" id="auction'.$bidID.'" name="="auction'.$bidID.'" value="'.$winnerMessage.'" '. $endAuction.'>
							</div>		
                                          <div id="timeLeft">
								'.$hh.''.$mm.''.$ss.'</br>
							</div>
							
                        </div>
			  </div>
            </form>';
    }
?>