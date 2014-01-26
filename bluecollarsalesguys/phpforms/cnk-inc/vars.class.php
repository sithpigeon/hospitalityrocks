<?php

/*
+--------------------------------------------------------------------------
|
|   Conkurent Engine part
|   
|   =========================================
|
|   Module written by Cyrill Polikarpov
|   Conkurent, LLC, Web Programming Department
|   Email: clio@conkurent.com
|   ICQ #: 99885395
|
|   =========================================
|
|   (c) 2004-2007 Conkurent, LLC
|   Visit: http://conkurent.com
|   Email: company@conkurent.com
|
|   =========================================
|
+--------------------------------------------------------------------------
*/

class VARS
{

	var $__aSYS = array();
    
    Function VARS()
	{
		VARS::_RedefineGlobals();
	}
	
	Function _RedefineGlobals()
	{
		global  $HTTP_POST_VARS,$_POST,
				$HTTP_GET_VARS,$_GET,
				$HTTP_COOKIE_VARS,$_COOKIE,
			    $HTTP_SERVER_VARS,$_SERVER,
				$HTTP_POST_FILES,$_FILES,
				$HTTP_SESSION_VARS,$_SESSION;
			   
		if ( !isset($HTTP_GET_VARS) )
		{
			if ( isset($_GET) )
			{
				$this->__aSYS["GET"] = &$_GET;
			}
		}
		else
		{
			$this->__aSYS["GET"] = &$HTTP_GET_VARS;
		}
		
		if ( !isset($HTTP_POST_VARS) )
		{
			if ( isset($_POST) )
			{
				$this->__aSYS["POST"] = &$_POST;
			}
		}
		else
		{
			$this->__aSYS["POST"] = &$HTTP_POST_VARS;
		}

		if ( !isset($HTTP_POST_FILES) )
		{
			if ( isset($_FILES) )
			{
				$this->__aSYS["FILES"] = &$_FILES;
			}
		}
		else
		{
			$this->__aSYS["FILES"] = &$HTTP_POST_FILES;
		}

		if ( !isset($HTTP_SERVER_VARS) )
		{
			if ( isset($_SERVER) )
			{
				$this->__aSYS["SERVER"] = &$_SERVER;
			}
		}
		else
		{
			$this->__aSYS["SERVER"] = &$HTTP_SERVER_VARS;
		}

		if ( !isset($HTTP_COOKIE_VARS) )
		{
			if ( isset($_COOKIE) )
			{
				$this->__aSYS["COOKIE"] = &$_COOKIE;
			}
		}
		else
		{
			$this->__aSYS["COOKIE"] = &$HTTP_COOKIE_VARS;
		}

		if ( !isset($HTTP_SESSION_VARS) )
		{
			if ( isset($_SESSION) )
			{
				$this->__aSYS["SESSION"] = &$_SESSION;
			}
		}
		else
		{
			$this->__aSYS["SESSION"] = &$HTTP_SESSION_VARS;
		}
	}
	
	//----------------------------------------------------------------------------------//
	// Grab input from GET & POST. Additionaly put there REMOTE_ADDR & REQUEST_METHOD   //
	//----------------------------------------------------------------------------------//
	Function GrabInput() 
	{
    	global $HTTP_X_FORWARDED_FOR,$HTTP_PROXY_USER,$REMOTE_ADDR,$REQUEST_METHOD;
    	static $_aReturn = array();
    	static $_kSYS  , $_vSYS;
    	static $_kSYS2 , $_vSYS2;

		if(is_array($this->__aSYS["GET"])) 
		{
	  		while( list($_kSYS, $_vSYS) = each($this->__aSYS["GET"]) ) 
			{
				if( is_array($this->__aSYS["GET"][$_kSYS]) ) 
				{
		  			while( list($_kSYS2, $_vSYS2) = each($this->__aSYS["GET"][$_kSYS]) ) 
					{
						$_aReturn[$_kSYS][ VARS::_CleanKey($_kSYS2) ] = VARS::_CleanValue($_vSYS2);
		  			}
				} 
				else 
				{
		   			$_aReturn[$_kSYS] = VARS::_CleanValue($_vSYS);
		  		}
	  		}
		}

		if(is_array($this->__aSYS["POST"])) 
		{
	  		while( list($_kSYS, $_vSYS) = each($this->__aSYS["POST"]) ) 
			{
				if ( is_array($this->__aSYS["POST"][$_kSYS]) ) 
				{
		  			while( list($_kSYS2, $_vSYS2) = each($this->__aSYS["POST"][$_kSYS]) ) 
					{
						$_aReturn[$_kSYS][ VARS::_CleanKey($_kSYS2) ] = VARS::_CleanValue($_vSYS2);
		  			}
				} 
				else 
				{
		   			$_aReturn[$_kSYS] = VARS::_CleanValue($_vSYS);
		  		}
	  		}
		}

		/* {{{ Get REMOTE IP }}} */

		$_aReturn['IP_ADDRESS'] = VARS::_SelectVar(
	                                            	array(
							  							1 => $this->__aSYS["SERVER"]['REMOTE_ADDR'],
							  							2 => $HTTP_X_FORWARDED_FOR,
							  							3 => $HTTP_PROXY_USER,
							  							4 => $REMOTE_ADDR
							  						)
						 						  );

		/* {{{ validate IP address }}} */
		$_aReturn['IP_ADDRESS'] = preg_replace( "/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", "\\1.\\2.\\3.\\4", $_aReturn['IP_ADDRESS'] );
        /* {{{ Get Request Method }}} */				
		$_aReturn['REQUEST_METHOD'] = ( $this->__aSYS["SERVER"]["REQUEST_METHOD"] != "" ) ? strtolower($this->__aSYS["SERVER"]["REQUEST_METHOD"]) : strtolower($REQUEST_METHOD);
        /* {{{ Get User Agent }}} */
		$_aReturn['USER_AGENT'] = substr($this->__aSYS["SERVER"]["HTTP_USER_AGENT"],0,100);
		/* {{{ Get hostname }}} */
		$_aReturn['USER_HOST'] = $this->__aSYS["SERVER"]["HTTP_HOST"];
		/* {{{ Parse LAST_CLICK }}} */
		$_aReturn['LAST_CLICK'] = time();
		
		return $_aReturn;
	}


	Function _CleanKey($key) 
	{
  		if ($key == "") 
		{
    		$key = false;
  		}
  		else 
  		{
    		$key = preg_replace( "/\.\./"            , ""   , $key );
        	$key = preg_replace( "/\_\_(.+?)\_\_/"   , ""   , $key );
    	    $key = preg_replace( "/^([\w\.\-\_]+)$/" , "$1" , $key );
  		}
   		return $key;
	}

	Function _CleanValue($val) 
	{

  		if ($val == "") 
		{
    		$val = false;
  		}
        else 
        {
//    	   $val = preg_replace  ( "/\"(\s{0,}>)/"          , "&quot;\\1"        , $val );
//    	   $val = preg_replace  ( "/'(\s{0,}>)/"           , "&#39;\\1"        , $val );

    	   $val = str_replace  ( "&"            , "&amp;"         , $val );
    	   $val = str_replace  ( "<!--"         , "&#60;&#33;--"  , $val );
    	   $val = str_replace  ( "-->"          , "--&#62;"       , $val );
    	   $val = str_replace  ( "\""           , "&quot;"        , $val );
    	   $val = str_replace  ( "'"            , "&#39;"         , $val );
    	   $val = str_replace  ( ">"            , "&gt;"          , $val );
    	   $val = str_replace  ( "<"            , "&lt;"          , $val );
//    	   $val = str_replace  ( "!"            , "&#33;"         , $val );
    	   $val = preg_replace ( "/\|/"         , "&#124;"        , $val );
    	   $val = preg_replace ( "/\r/"         , ""              , $val );
           $val = str_replace ( "%00"           , ""              , $val );
        }
  		return $val;
	}

	Function _SelectVar($aVars) 
	{
    	static $_mChosen;
    	static $_kVars, $_vVars;
	    
	    if ( !is_array( $aVars ) )
    	{
    	    $_mChosen = false;
    	}
        else 
        {
    	   ksort($aVars);

    	   $_mChosen = -1;

    	   foreach ($aVars as $_kVars => $_vVars)
    	   {
    		  if (isset($_vVars))
    		  {
    			 $_mChosen = $_vVars;
    			 break;
    		  }
    	   }
        }
    	return $_mChosen;
	}

}

?>
