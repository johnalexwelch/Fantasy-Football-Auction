<?php

    $unith =3600;        
    $unitm =60;
    $endAuction="";
    $winnerMessage="Place Your Bid";
    
    // All Auctions output construction
    $feedQuery = mysqli_query($dbc,
                      "SELECT
                        BI.FIRST_NAME,
                        BI.LAST_NAME,
                        BI.TEAM,
                        BI.BID,
                        BI.ID,
                        TIMESTAMPDIFF(second,BI.BID_TIME,now()) AS BID_TIME,
                        US.USERNAME
                        
                       FROM AUCTION.BIDS BI
                       LEFT JOIN AUCTION.USER US
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
        $bidTime = $row["BID_TIME"];
        
        $totalTime = $bidTime;
        //$totalTime = $bidTime / 60;
        
        $hh = intval($totalTime / $unith);    
        $ss_remaining = ($totalTime - ($hh * 3600));        
        
        $mm = intval($ss_remaining / $unitm);    
        $ss = ($ss_remaining - ($mm * 60));
        
        if ($hh < 0){
            $hh= '';
        }
        elseif ($hh < 10) {
              $hh="0".$hh.":";
        }
        
        if ($mm < 0){
            $mm= '';
        }
        else if ($mm< 10) {
              $mm="0".$mm.":";
        }
        else if ($mm>=10){
		$endAuction='disabled="disabled"';
            $winnerMessage='The Winner is: '.$userName;
            
            $winnerQuery = mysqli_query($dbc,
                    "UPDATE AUCTION.BIDS
			  SET WINNER = '$userName'
			  WHERE ID = '$bidID'") or die("Invalid query: 1".mysql_error());
    
            $hh='';
            $mm='';
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
            }
            else{
              $ss="0".$ss;
            }
        }
        
    
        $feedDisplayList .= '
            <form method="post" action="scripts/update.php" id="auction'.$bidID.'>" name="auction'.$bidID.'">
                  <div id="auctionContainer">
                        <div id="auctionHeader">
                            <h3>'.$firstName.' '.$lastName.' - '.$teamName.'</h3>
                        </div>
                        <div id="auctionSubHeader">
                            '.$userName.'
                        </div>
                        <div id="auctionBidAmount">
                            '.$bidAmount.'
                        </div>
                        <div id="auctionBid">
                            <input type="text" name="bidAmount" id="bidAmount"/>
                            <label for="bidAmount">Your Bid</label>
                            <input type="hidden" name ="auctionID" id="auctionID" value="'.$bidID.'">
                            <input type="submit" id="auction'.$bidID.'" name="="auction'.$bidID.'" value="'.$winnerMessage.'" '. $endAuction.'>
                        </div>
                        <div id="timeLeft">
                            '.$hh.''.$mm.''.$ss.'</br>
                        </div>
                  </div>
            </form>';
    }
?>