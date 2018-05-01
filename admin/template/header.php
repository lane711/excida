<?php 

include PATH . '/includes/common_header.php';
include PATH . '/admin/template/tables.php'; 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en" dir="<?php echo $lang['Direction']; ?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $lang['Encoding']; ?>" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<title><?php echo $title; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="<?php echo URL . '/admin/template/style.css'; ?>" type="text/css" />
	
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript">
		var path_var = "<?php echo URL; ?>";
	</script>
	<script type="text/javascript" src="<?php echo URL; ?>/includes/js/site.js"></script>
	
	<!-- AJAX Bulk Image Upload Script -->
	<link href="<?php echo URL; ?>/includes/css/uploadfilemulti.css" rel="stylesheet">
	<script src="<?php echo URL; ?>/includes/js/jquery.fileuploadmulti.min.js"></script>
	<!-- END AJAX Image Script -->
	
	<script type="text/javascript" src="<?php echo URL; ?>/includes/ckeditor/ckeditor.js"></script>
	
	<link rel="stylesheet" href="<?php echo URL; ?>/admin/template/fonts/elegant_font/style.css" />
	<!--[if lte IE 7]><script src="<?php echo URL; ?>/admin/template/fonts/elegant_font/lte-ie7.js"></script><![endif]-->
	
	<link rel="stylesheet" type="text/css" media="screen" href="http://cdn.jsdelivr.net/qtip2/2.2.0/jquery.qtip.min.css">
	
	<script src="http://cdn.jsdelivr.net/qtip2/2.2.0/jquery.qtip.min.js" type="text/javascript"></script>
	
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
	
	<style type="text/css">
	.help
	{
		width:20px;
		height:20px;
	}
	.ui-tooltip .ui-tooltip-content,
	.ui-tooltip p,
	.ui-tooltip ul,
	.ui-tooltip li,
	.ui-tooltip,
	.qtip {
	    max-width: 300px;
	    min-width: 75px;
	    font-size: 16px;
	    line-height: 20px;
	    text-align: center;
	    border-color: #02344A;
	    font-family: arial;
	    color: #fff;
	    background-color: #02344A;
	}
	</style>
	
	<script type="text/javascript">
	$(document).ready(function(){
		$('.tooltip').qtip({
			show: 'click',
			hide: 'click'
		});
    });
	</script>
</head>

<body>

<div align="center">

<br />

<table width="90%" cellpadding="5" cellspacing="0" border="0">
<tr>
	<td width="100%" align="center" valign="top">

	<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<tr>
		<td width="100%" valign="top">
		
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td align="center" valign="middle">