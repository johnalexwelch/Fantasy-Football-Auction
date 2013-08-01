<?php
    error_reporting(E_ALL); 
    ini_set( 'display_errors','1');
    require_once('connectvars.php');

    //Sign up

    $errorMsg = "";
    $success = "";
    
    if (isset ($_POST['submit'])){
    //connect to database
        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)or die("Unable to connect to MySQL");;

        $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
        $name = mysqli_real_escape_string($dbc, trim($_POST['name']));
        $password = mysqli_real_escape_string($dbc, trim($_POST['password']));
        $code = mysqli_real_escape_string($dbc, trim($_POST['code']));
    
        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\._\-&!?=#]*@/', $email)) {
            // $email is invalid because LocalName is bad
                header("Location: ../signup.html");  
        }
        else {
            // Strip out everything but the domain from the email
            $domain = preg_replace('/^[a-zA-Z0-9][a-zA-Z0-9\._\-&!?=#]*@/', '', $email);
            // Now check if $domain is registered
            if (!checkdnsrr($domain)) {
                header("Location: ../signup.html");   
            }
        }

        //Error Messages for missing data
        if ((!$email) || (!$name) || (!$password) || (!$code)) {
            if (!$email) {
                header("Location: ../signup.html");}   
            else if (!$name) {
                header("Location: ../signup.html");}   
            else if (!$password) {
                header("Location: ../signup.html");}   
            else if (!$code) {
                header("Location: ../signup.html");}   
        }

        else if($code == 'success123'){
            //Add user info into the database 
            $query = "INSERT INTO USER (EMAIL, PASSWORD, USERNAME) VALUES ('$email', SHA('$password'), '$name')" or die("Could not connect: ".mysql_error());
            mysqli_query($dbc,$query);
            header("Location: ../home.php");
            exit;
        }
        //else{
        //    header("Location: http://localhost:8888/savi/beta.php?errorMsg=Your+email+address+is+already+in+use");
        //}
    }
?>