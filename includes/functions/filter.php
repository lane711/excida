<?php

// ----------------------------------------------------------------------------
// removehtml($input, $allowed)
//
// remove html tags from the message
//
// $input - string with hmtl tags
// $allowed - allowed hmtl tags, e.g br
//

function removehtml($input,$allowed='') {

 $input = preg_replace ( "/<((?!\/?($allowed)\b)[^>]*>)/xis", "", $input );
 $input = preg_replace ( "/<($allowed).*?>/i", " \\1 ", $input );
 $input = preg_replace("/ +/", " ", $input);

 return $input;

}

// ----------------------------------------------------------------------------
// safehtml($input)
//
// converts all the html special characters to html entities, UTF-8 compatible
//
// $input - string
//

function safehtml($input)
{	
	if ( !is_array( $input ) )
	{
		$text = trim($input);
		
		$text = preg_replace("/(\r\n|\n|\r)/", "\n", $text); // cross-platform newlines
		$text = preg_replace("/\n\n\n\n+/", "\n", $text); // take care of duplicates 
		
		$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
		$text = stripslashes($text);
		
		$text = str_replace ( "\n", " ", $text );
		$text = str_replace ( "\t", " ", $text );
	}

	return $text;
}

// ----------------------------------------------------------------------------
// safehtml_cms($input)
//
// converts all the html special characters to html entities, UTF-8 compatible
// safe for CMS tool
//
// $input - string
//

function safehtml_cms($input) {

 $text = trim($input);

 $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
 $text = stripslashes($text);

 //$text = str_replace ( "\n", " ", $text );

 return $text;

}

// ----------------------------------------------------------------------------
// unsafehtml($input)
//
// converts html entities back to html special characters, UTF-8 compatible
//
// $input - string
//

function unsafehtml($input) {

 $input = html_entity_decode( $input, ENT_COMPAT, 'UTF-8' );
 $input = str_replace ( "\n", " ", $input );
 $input = str_replace ( "\r", " ", $input );

 return ($input);
}

// ----------------------------------------------------------------------------
// unsafehtml_pdf($input)
//
// converts html entities back to html special characters, UTF-8 compatible
// safe for pdf output
//
// $input - string
//

function unsafehtml_pdf($input) {

 $input = html_entity_decode( $input, ENT_COMPAT, 'UTF-8' );

 $input = str_replace ( "<p>", "\n", $input );
 $input = str_replace ( "<br>", "\n", $input );
 $input = str_replace ( "<br />", "\n", $input );

 return ($input);

}

// ----------------------------------------------------------------------------
// unsafehtml_xml($input)
//
// converts html entities back to html special characters, UTF-8 compatible
// safe for XML feeds
//
// $input - string
//

function unsafehtml_xml($input) {

 $input = html_entity_decode( $input, ENT_COMPAT, 'UTF-8' );
 $input = str_replace ( "&nbsp;", " ", $input );
 $input = str_replace ( "&quot;", " ", $input );

 return ($input);

}

?>