<?php
if(isset($_POST['send_email']) && $_POST['send_email'] == 1 && isset($_POST['send_to_email']) && $_POST['send_to_email'] != '') {
	//sleep(2); echo ''; exit;
	
	$to_email = trim($_POST['send_to_email']);
	require_once 'lib/swift_required.php';
	require_once 'config.php';
	
	$transporter = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
	  ->setUsername($gmail_username)
	  ->setPassword($gmail_password);

	$mailer = Swift_Mailer::newInstance($transporter);

	$message = Swift_Message::newInstance('Work Status Report')
	  ->setFrom(array($from_email => $from_name))
	  ->setTo(array($to_email))
	  ->setBody('Please find the attached work report.');
	  
	$message->attach(
	Swift_Attachment::fromPath('events.xml')->setFilename('Events.xml')
	);  

	$result = $mailer->send($message);
	echo $result;
}
else {
	echo 'Invalid request!';
}
?>