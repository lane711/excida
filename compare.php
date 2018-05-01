<?php

define( 'PMR', true );

$page = 'compare';

include 'config.php';
include PATH . '/defaults.php';

$title = $conf['website_name_short'];

include PATH . '/templates/' . $cookie_template . '/header.php';

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/compare.tpl';
$template = new Template;
$template->load ( $tpl );

$template->set( 'output_message', $output_message );
$template->set( 'compare_text', $lang['Compare_Intro'] );
$template->set( 'header', $lang['Compare_Header'] );
$template->set( 'header_agent', $lang['Package_Agent'] );
$template->set( 'header_property', $lang['Package_Listing'] );

$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>