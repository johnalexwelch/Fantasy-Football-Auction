<?php

    error_reporting(E_ALL); 
    ini_set( 'display_errors','1');
    require_once('connectvars.php');

    //Sign up
    $errorMsg = "";
    $success = "";

        if (isset ($_POST['submit'])){
        //connect to database
            $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)or die("Unable to connect to MySQL");
    
            $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
            $password = mysqli_real_escape_string($dbc, trim($_POST['password']));
                    
            if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\._\-&!?=#]*@/', $email)) {
                // $email is invalid because LocalName is bad
                header("Location: signin.html"); 
            }
            else {
                // Strip out everything but the domain from the email
                $domain = preg_replace('/^[a-zA-Z0-9][a-zA-Z0-9\._\-&!?=#]*@/', '', $email);
    
                // Now check if $domain is registered
                if (!checkdnsrr($domain)) {
                header("Location: signin.html");  
                }
            }
    
            //Error Messages for missing data
            if ((!$email) || (!$password)) {
                if (!$email) {
                    header("Location: signin.html"); 
                }   
                else if (!$password) {
                header("Location: signin.html"); 
                }
            }
    
            else {
                $result = "SELECT ID, EMAIL
                           FROM USER
                           WHERE EMAIL = '$email'
                                AND PASSWORD = SHA('$password')";
                $data = mysqli_query($dbc, $result);
                $rowCheck = mysqli_num_rows($data);

                if($rowCheck > 0){ 
                    while($row = mysqli_fetch_array($data)){ 
                //if (mysqli_num_rows($data) == 1) {
                // The log-in is OK so set the user ID and username session vars (and cookies), and redirect to the home page
                    session_start(); // Start Session First Thing
                        $userID = $row['ID'];
                        $userEmail = $row['EMAIL'];
                        $_SESSION['EMAIL'] = $userEmail;
                        $_SESSION['ID'] = $userID;
                        setcookie('email', $row['EMAIL'], time() + (60 * 60 * 24 * 30));  // expires in 30 days
                        setcookie('id', $row['ID'], time() + (60 * 60 * 24 * 30));  // expires in 30 days
                      
                        header("Location: home.php");
                      exit();
                    }
                }
            }
        }


?>