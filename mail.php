<?php
if(isset($_POST['send_email']) && $_POST['send_email'] == 1 && isset($_POST['send_to_email']) && $_POST['send_to_email'] != '') {
	//sleep(2); echo ''; exit;
	
	//echo date("M-d-y", strtotime('last monday', strtotime('next week', time()))); exit;
	
	$week_days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday');
	$week_timestamps = $week_search_timestamps = array();
	foreach($week_days as $week_name) {
		$current = $week_name == 'friday'?'this':'last';
		$dt = new DateTime("$current $week_name");
		$str = strtotime($dt->format('Y-m-d'));
		$week_timestamps[] = $str;
		$week_search_timestamps[] = "contains(timestamp, $str)";
	}
	//print_r($week_timestamps); exit;
	//print_r($week_search_timestamps); exit;
	
	$contains = implode(" or ", $week_search_timestamps);
	//echo $week_search_timestamps; exit;
	$mail_body = 'Weekly Work:<br><br>';
	
	$xml=simplexml_load_file('events.xml'); 
	$nodes = $xml->xpath("//notes[$contains]");
	foreach($nodes as $node)
	{
		foreach($node as $name => $prop) {
			if($name == 'timestamp') {
				$mail_body .= date("Y-m-d", "$prop").'<br>';
			}
			else if($name == 'desc') {
				$mail_body .= $prop;
			}
		}
		echo "\n";
	}
		
	$to_email = trim($_POST['send_to_email']);
	require_once 'lib/swift_required.php';
	require_once 'config.php';
	
	$transporter = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
	  ->setUsername($gmail_username)
	  ->setPassword($gmail_password);

	$mailer = Swift_Mailer::newInstance($transporter);

	$message = Swift_Message::newInstance('Weekly Work Status Report')
	  ->setFrom(array($from_email => $from_name))
	  ->setTo(array($to_email))
	  ->setBody($mail_body);
	
	$message->setContentType("text/html");	
	//$message->attach(Swift_Attachment::fromPath('events.xml')->setFilename('Events.xml'));

	$result = $mailer->send($message);
	echo $result;
}
else {
	echo 'Invalid request!';
}
?>