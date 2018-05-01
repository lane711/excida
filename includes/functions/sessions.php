<?php

class Session
{
	function Session() 
	{
		session_start();
	}
                
	function set( $name, $value ) 
	{
		$_SESSION[$name] = $value;
	}

	function fetch( $name ) 
	{
		if ( isset( $_SESSION[$name] ) )
		{
			return $_SESSION[$name];
		}
		else
		{
			return false;
		}
	}

	function varunset( $name ) 
	{
        if ( isset( $_SESSION[$name] ) )
        {
            unset( $_SESSION[$name] );
            return true;
        } 
        else 
        {
            return false;
    	}
	}

	function destroy() 
	{
		$_SESSION = array();
		session_destroy();
	}
}

?>