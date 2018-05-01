<?php

class Template 
{
	public $template = '';
	public $placeholders = array();

	function load( $file )
	{
		$this->template = $file;
	}

	function set( $var, $content ) 
	{
		$this->placeholders[$var] = $content;
	}

	function publish( $return = false ) 
	{
		global $db, $conf, $lang, $custom, $cookie_template, $meta_title, $meta_description, $meta_keywords, $title;
	
		ob_start();
		include $this->template;
		$contents = ob_get_contents();
		
		// Now do all string replacement
		if ( is_array( $this->placeholders ) )
		{
			foreach( $this->placeholders AS $key => $value )
			{
				$contents = str_replace( '{' . $key . '}', $value, $contents );
			}
		}
		
		ob_end_clean();
		
		if ( $return == false )
		{
			echo $contents;
		}
		else
		{
			return $contents;
		}
	}
}

?>