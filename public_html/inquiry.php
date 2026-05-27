<?php

function getIp()
{
	$ip = $_SERVER['REMOTE_ADDR'];
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	return $ip;
}
$ip1 = getIp();
if (!empty($_SERVER['HTTP_REFERER'])) {
	$ref_page = $_SERVER['HTTP_REFERER'];
}

if (count($_POST) > 0) {
    $input_name = $_POST["name"];
    $input_email = $_POST["email"];
    $input_phone = $_POST["phone"];
    $input_message = $_POST["message"];
    $break = urlencode("\n");
    $emailList = "leads@clientsnow.co.in";
    $headers .= "Bcc: " . $emailList . "\r\n" .
		'From: web@hotelcosmopolitan.in' . "\r\n";
        
    $body = "
		Hi Hotel Cosmo,
		You have one inquiry,
		Below are the details,
    	Name =  " . $input_name . " 
		Email =  " . $input_email . "
		Phone =  " . $input_phone . "
		Message =  " . $input_message . "
		IP Address =  " . $ip1 . "
		";
		
		$recaptcha_secret = '6LfXNQAqAAAAAOS4242-AGL1IVN8m--YfOqOfLw7';
	$recaptcha_response = $_POST['g-recaptcha-response'];
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array(
		'secret' => $recaptcha_secret,
		'response' => $recaptcha_response
	);
	$options = array(
		'http' => array(
			'method' => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context = stream_context_create($options);
	$response = file_get_contents($url, false, $context);
	$result = json_decode($response);

	if ($result && $result->success && $result->score >= 0.5) {
		if (strpos($textarea_message, "http") !== false) {
			header("Location: /");
		} else 	if (empty($input_name)) {
			header("Location: /");
		} else if (empty($input_phone)) {
			header("Location: /");
		} else  if (strpos($input_name, "http") !== false) {
			header("Location: /");
		} else if (mail("hotelprojects@citizenindia.com", "Contact Inquiry", $body, $headers)) {
			header("Location: https://wa.me/919099914802?text=Hey Hotel Cosmo, I need some information about Your Product. My details are "
            . $break . "Name= " . $input_name
            . $break . "Email =  " . $input_email
            . $break . "Phone =  " . $input_phone
            . $break . "Message =  " . $input_message . "");
		}
		// 		echo '<script>alert("success")</script>';
	} else {
		echo '<script>alert("reCAPTCHA validation failed. Please confirm that you are not a bot.")</script>';
		header("Location: /");
		exit();
	}
}