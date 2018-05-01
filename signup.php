<?php
define( 'PMR', true );
include 'config.php';
include PATH . '/defaults.php';
// Title tag content
$title = $conf['website_name_short'] . ' - ' . $lang['Menu_User_Login'];
// Destroy user/admin session if we logout
if ( $_GET['action'] == 'logout' )
{
	$session->destroy();
}

// If they are trying to log in
if ( $_REQUEST['submit'] == true )
{
	
}

?>


	<link rel="alternate" type="application/rss+xml" title="RSS" href="<?php echo URL . '/rss.php'; ?>" />

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
    <link rel="shortcut icon" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/img/favicon.png" type="image/png">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/css/bootstrap-responsive.css" type="text/css">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/libraries/chosen/chosen.css" type="text/css">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/libraries/bootstrap-fileupload/bootstrap-fileupload.css" type="text/css">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/libraries/jquery-ui-1.10.2.custom/css/ui-lightness/jquery-ui-1.10.2.custom.min.css" type="text/css">
    <link rel="stylesheet" href="<?php echo URL; ?>/templates/<?php echo $cookie_template; ?>/assets/css/realia-blue.css" type="text/css" id="color-variant-default">
	
	<link rel="stylesheet" href="<?php echo URL; ?>/includes/jsCalendar/calendar.css" type="text/css">

	<script type="text/javascript" src="<?php echo URL; ?>/includes/jsCalendar/calendar.js"></script>
	<?php 
	if (empty($iso_language_codes[$cookie_language])) {
	    $_jsCalendarLang = 'en';
	} else {
	    $_jsCalendarLang = $iso_language_codes[$cookie_language];
	} 
	if (!file_exists(PATH.'/includes/jsCalendar/lang/calendar-'.$_jsCalendarLang.'.js')) {
	    if (file_exists(PATH.'/includes/jsCalendar/lang/calendar-'.$_jsCalendarLang.'-utf8.js')) {
	        $_jsCalendarLang .= '-utf8';
	    } elseif (file_exists(PATH.'/includes/jsCalendar/lang/calendar-'.$_jsCalendarLang.'-win.js')) {
	        $_jsCalendarLang .= '-win';
	    } else {
	        $_jsCalendarLang = 'en';
	    }
	}
	?>
	<script type="text/javascript" src="<?php echo URL; ?>/includes/jsCalendar/lang/calendar-<?php echo $_jsCalendarLang; ?>.js"></script>
	<script type="text/javascript" src="<?php echo URL; ?>/includes/jsCalendar/calendar-setup.js"></script>
	<script type="text/javascript">
		var fileLoadingImage = "<?php echo URL; ?>/includes/lightbox/images/loading.gif";		
		var fileBottomNavCloseImage = "<?php echo URL; ?>/includes/lightbox/images/closelabel.gif";
	</script>
	
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript">
		var path_var = "<?php echo URL; ?>";
	</script>
	<script type="text/javascript" src="<?php echo URL; ?>/includes/js/site.js"></script>

	<!-- AJAX Bulk Image Upload Script -->
	<link href="<?php echo URL; ?>/includes/css/uploadfilemulti.css" rel="stylesheet">
	

<?php


// User isn't logged in
$tpl = PATH . '/templates/' . $cookie_template . '/tpl/signup.tpl';
$template = new Template;
$template->load ( $tpl );
if(isset($_POST['submit'])){
$sql	=" 
	INSERT INTO ".ADMINS_TABLE."
	(
		first_name,
		last_name,
		email,
		company,
		jobtitle,
		phone,
		wesite_url,
		website_radio_text,
		website_textarea
		)
	VALUES
	(
		'".$_REQUEST['first_name']."',
		'".$_REQUEST['last_name']."',
		'".$_REQUEST['email']."',
		'".$_REQUEST['company']."',
		'".$_REQUEST['jobtitle']."',
		'".$_REQUEST['phone']."',
		'".$_REQUEST['wesite_url']."',
		'".$_REQUEST['website_radio_text']."',
		'".$_REQUEST['website_textarea']."'
	)";
$q = $db->query( $sql );
//var_dump($q);

$to 	="surya@neurons-it.in";
$subject="Account Notification";
$message .="Link";
$header = "From:Real Estate<surya@neurons-it.in> \r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html\r\n";
$mail	= mail($to,$subject,$header,$message);
$flag = 0;
	if($mail) 
		{	$flag = 1;	
					
		} 
		else 
		{	
			$flag = 0;
			echo "Mailer Error: " . $mail->ErrorInfo;
		}

}

//var_dump($_REQUEST['website_radio_text']);
$template->set( 'heading', $lang['Signup_Heading'] );
$template->set( '@first_name', $lang['Signup_First_Name'] );
$template->set( '@last_name', $lang['Signup_Last_Name'] );
$template->set( '@email', $lang['Signup_Email'] );
$template->set( '@sign_text', $lang['Signup_Text'] );
$template->set( 'first_name', $_REQUEST['first_name'] );
$template->set( 'last_name', $_REQUEST['last_name'] );
$template->set( 'email', $lang['email'] );
$template->set( '@company', $lang['Signup_Company'] );
$template->set( 'company', $_REQUEST['company'] );
$template->set( '@jobtitle', $lang['Signup_Jobtitle'] );
$template->set( 'jobtitle', $_REQUEST['jobtitle'] );
$template->set( '@phone', $lang['Signup_Phone'] );
$template->set( 'phone', $_REQUEST['phone'] );
$template->set( '@website_url', $lang['Signup_website_url'] );
$template->set( 'website_url', $_REQUEST['website_url'] );
//$template->set( '@website_lead_text', $lang['Signup_website_lead_text'] );
//$template->set( '@website_radio_text', $lang['Signup_radio_text'] );
//$template->set( 'website_radio_text', $_REQUEST['website_radio_text'] );
//$template->set( '@website_radio_text2', $lang['Signup_radio_text2'] );
//$template->set( 'website_radio_text', $_REQUEST['website_radio_text'] );
$template->set( '@website_textarea', $lang['Signup_website_textarea'] );
$template->set( 'website_textarea', $_REQUEST['website_textarea'] );
$template->set( 'output_message', $output_message );	
$template->publish();

//include PATH . '/templates/' . $cookie_template . '/footer.php';

?>