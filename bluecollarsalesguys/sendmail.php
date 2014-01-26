<?php
  $email = $_REQUEST['email'] ; //get email
  $phone = $_REQUEST['phone'] ; //get phone
  $name = $_REQUEST['name'] ; //get name
  $message = $_REQUEST['message'] ;//get message
  $message = wordwrap($message, 70);
  /*$disclaimer = $_REQUEST['disclaimer'] ; */ //optional check box
  if (!isset($_REQUEST['email'])) { 
    header( "Location: email.shtml" ); //this is where the html file is
  }
  elseif (empty($email) || empty($phone) || empty($name) || empty($message)) {
    header( "Location: emailError.shtml" ); //this is where an error redirects to
  }
  elseif ( preg_match( "/[\r\n]/", $name ) || preg_match( "/[\r\n]/", $email ) ) { //this is a simple email validation to prevent spamming
	header( "Location: emailError.shtml" ); //this is where an error redirects to
}
  else {
    mail( "Stu@BlueCollarSalesGuys.com", "BCSG Website Contact Form", "From $name,\n\n Phone: $phone, email: $email\n\n $message\n\n This message was sent using the contact form on BlueCollarSalesGuys.com", "From: $name <$email>"  //mailed
		);
	/*$myFile = "testFile.txt";  //this is for creating a list of people who input stuff
$fh = fopen($myFile, 'a') or die("can't open file");  
fwrite($fh, $email);
fwrite($fh, "\n");
fclose($fh); */
    header( "Location: thankyou.shtml" ); //this is where page goes if successfully mailed
  }
?>

