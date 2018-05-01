<?php
	
function captcha_check()
{
	global $conf;
	
	// Google reCaptcha service
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	
	if ( $conf['captcha_private_key'] != '' && $conf['captcha_public_key'] != '' )
	{
		$ch = curl_init( $url . '?secret=' . $conf['captcha_private_key'] . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR'] );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
		$response = curl_exec( $ch );
		curl_close( $ch );
		
		$captcha_output = json_decode( $response );
		$captcha_response = $captcha_output->success;
		
		if ( $captcha_response == true )
		{
			return true;
		}
	}
	
	return false;	
}

?>