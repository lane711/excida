<?php

// COMMON CONTENT TABLE HEADER
// - this html code is added to all the content tables
// as a header by using echo table_header('Name');
function table_header ( $message )

 {

  global $cookie_template;

  $return = '<div class="table-header">' . $message . '</div>
  				<div class="table-body">
  					<div class="table-inner">';

  return $return;

 }

// COMMON CONTENT TABLE FOOTER
// - this html code is added to all the content tables
// as a footer by using echo table_footer();

function table_footer (  )

 {

  global $cookie_template;

  $return = '<div class="clearfix"></div></div></div>';

  return $return;

 }

?>