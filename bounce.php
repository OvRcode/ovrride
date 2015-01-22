<?php
require_once 'Mandrill.php';
$mandrillAPI = new Mandrill(getenv('MANDRILL_API'));
//$mandrillAPI = new Mandrill("***REMOVED***");
$mandrill = json_decode($_POST['mandrill_events'], true);
foreach($mandrill as $key => $value){
	if($key == "event"){
		error_log($value['event']);
		$bodyHTML = <<<AAA
		Looks like we had trouble sending and email: {$value['event']}<br>
		Sent To: {$value['msg']['email']}<br>
		Subject: {$value['msg']['subject']}<br>
AAA;
		$bodyText = <<<BBB
		Looks like we had trouble sending and email: {$value['event']}\n
                Sent To: {$value['msg']['email']}\n
                Subject: {$value['msg']['subject']}\n

BBB;
		$message = new stdClass();
		$message->html = $bodyHTML;
		$message->text = $bodyText;
		$message->subject = "Failed Email to " . $value['msg']['email'];
		$message->from_email = "info@ovrride.com.com";
		$message->from_name  = "OvR";                                                                                                                                  
		$message->to = array(array("email" => "devops@ovrride.com"));                                                                                                   
		$message->track_opens = true;                                                                                                                                  

		$response = $mandrillAPI->messages->send($message); 
	}
}
/*$event = $mandrill['event'];
$state = $mandrill['msg']['state'];
$subject = $mandrill['msg']['subject'];
$to = $mandrill['msg']['email'];
$from = $mandrill['msg']['sender'];

$body = "Looks like we had trouble sending and email. Status: " . $state . "<br>";
$body .= "Sent to: " . $to . "<br>";
$body .= "Subject: " . $subject . "<br>";
$message = new stdClass();
$message->html = $body;
$message->text = "text body";
$message->subject = "email subject";
$message->from_email = "info@ovrride.com.com";
$message->from_name  = "OvR";
$message->to = array(array("email" => "mikeb@ovrride.com"));
$message->track_opens = true;

$response = $mandrillAPI->messages->send($message);
*/
?>
