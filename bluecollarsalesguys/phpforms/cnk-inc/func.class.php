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

class FUNC
{

    //----------------------------------------------------//
    // Checks if $iNumber is realy number                 //
    //----------------------------------------------------//
    Function IsNumber($iNumber="")
    {
        static $_mReturn;

        if ($iNumber == "")
        {
            $_mReturn = false;
        }

        if ( preg_match( "/^([0-9]+)$/", $iNumber ) )
        {
            $_mReturn = $iNumber;
        }
        else
        {
            $_mReturn = false;
        }

        return $_mReturn;
    }

    //----------------------------------------------------//
    // Generate UniqueID                                  //
    //----------------------------------------------------//
    Function my_UniqId()
    {
        mt_srand((double)microtime() * 1000000);
        return uniqid(mt_rand());
    }

    //----------------------------------------------------//
    // Safe input/output ()                               //
    //----------------------------------------------------//
    Function TxtSafeInput( $sVal )
    {
       $aHtml = array('textarea', 'script', 'table', 'select', 'optgroup');
       $sVal = preg_replace ( "/<(br)>/ies"    , "&lt;\\1&gt;", $sVal );
       foreach ($aHtml as $sTag)
       {
          $sVal = preg_replace( "/<(\s*{$sTag})/is", "&lt;\\1", $sVal );
       }

       $sVal = str_replace  ( '/'            , "&#047;"         , $sVal );
       $sVal = str_replace  ( "\n"           , "<br>"           , $sVal );
       return $sVal;
    }
    Function TxtSafeOutput( $sVal )
    {
       $sVal = str_replace  ( "<br>"      , "\n"       , $sVal );
//       $sVal = str_replace  ( "&"         , "&amp;"    , $sVal );
       $sVal = str_replace  ( "<"         , "&lt;"     , $sVal );
       $sVal = str_replace  ( ">"         , "&gt;"     , $sVal );
       $sVal = str_replace  ( "\""        , "&quot;"   , $sVal );
       return $sVal;
    }

    // to write into html-code textarea
    Function TxtGenOutput( $sVal )
    {
       $sVal = str_replace  ( "<"         , "&lt;"     , $sVal );
       $sVal = str_replace  ( ">"         , "&gt;"     , $sVal );
       $sVal = str_replace  ( "\""        , "&quot;"   , $sVal );
       return $sVal;
    }
    Function TxtUnSafeOutput( $sVal )
    {
       $sVal = str_replace  ( "<br>"         , "\n"       , $sVal );
       //---
       $sVal = str_replace  ( "&gt;"         , ">"        , $sVal );
       $sVal = str_replace  ( "&lt;"         , "<"        , $sVal );
       $sVal = str_replace  ( "&#33;"        , "!"        , $sVal );
       $sVal = str_replace  ( "&#124;"       , "|"        , $sVal );
       $sVal = str_replace  ( "&#39;"        , "'"        , $sVal );
       $sVal = str_replace  ( "&quot;"       , "\""       , $sVal );
       $sVal = str_replace  ( "--&#62;"      , "-->"      , $sVal );
       $sVal = str_replace  ( "&#60;&#33;--" , "<!--"     , $sVal );
       $sVal = str_replace  ( "&amp;"        , "&"        , $sVal );
       $sVal = str_replace  ( "&#047;"       , '/'        , $sVal );
       $sVal = str_replace  ( "&#60;"        , '<'        , $sVal );

       return $sVal;
    }

    // TO-DO: remove in more stable version
    // TEMP: A little patch for
    // \', \" and  \\ problem
    //
    function _FixEmailMsgBug( $sText )
    {
       $sText = str_replace( '\\&#39;',   "'",     $sText );
       $sText = str_replace( '&#39;',   "'",     $sText );
       $sText = str_replace( '\\&quot;',  '"',     $sText );
       $sText = str_replace( '&#124;',     '|',     $sText );
       $sText = str_replace( '\\\\',      '\\',    $sText );
       $sText = str_replace( '\\&amp;',     '&',     $sText );
       $sText = str_replace( '&amp;',     '&',     $sText );

       return $sText;
    }


    //----------------------------------------------------//
    // Return integer value of ( $iX / $iY )              //
    //----------------------------------------------------//
    Function IsInteger($iX,$iY)
    {
        static $_mReturn;

        if(empty($iX) or empty($iY))
        {
            $_mReturn = false;
        }
        if(($iX % $iY) == 0)
        {
            $_mReturn = ($iX/$iY);
        }
        else
        {
            $_mReturn = ((int)($iX/$iY) + 1);
        }

        return $_mReturn;
    }

    //----------------------------------------------------//
    // Decodes and encodes special chars in given string  //
    //----------------------------------------------------//
    Function DecodeString($sStr)
    {
        $sStr = preg_replace("/!/","&#33;",$sStr);
        $sStr = preg_replace("/'/","&#39;",$sStr);
        $sStr = preg_replace('/"/',"&#34;",$sStr);
        $sStr = preg_replace("/</","&#60;",$sStr);
        $sStr = preg_replace("/>/","&#62;",$sStr);
        return $sStr;
    }

    Function EncodeString($sStr)
    {
        $sStr = preg_replace("/&#33;/","!",$sStr);
        $sStr = preg_replace("/&#39;/","'",$sStr);
        $sStr = preg_replace("/&#34;/",'"',$sStr);
        $sStr = preg_replace("/&#60;/","<",$sStr);
        $sStr = preg_replace("/&#62;/",">",$sStr);
        return $sStr;
    }

    //----------------------------------------------------//
    // Wraps string length to $iWln param                 //
    //----------------------------------------------------//
    Function WrapString($iWln=70, $sStr)
    {
        return preg_replace("/^(.{$iWln}\S*)/m","\\1\n", $sStr);

        /* Depricated!

        static $_iStrLen   = 0;
        static $_iStrParts = 0;
        static $_sBuffer   = "";
        static $_iCnt      = 0;
        static $_c         = 0;

        $_iStrLen   = strlen($sStr);
        $_iStrParts = FUNC::IsInteger($_iStrLen, $iWln);

        for( $_c=0; $_c < $_iStrParts; $_c++ )
        {
            $_sBuffer .= substr($sStr, $_iCnt, $iWln) . " ";
            $_iCnt = $_iCnt + $iWln;
        }
        return $_sBuffer;

        */
    }

    //----------------------------------------------------//
    // Validate email format                              //
    //----------------------------------------------------//
    Function CleanEmail($sEmail = "")
    {
        static $_mReturn;

        $sEmail = preg_replace( "#[\n\r\*\'\"<>&\%\!\(\)\{\}\[\]\?\\/]#",
                                "",
                                $sEmail
                              );

        if ( preg_match( "/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/",
                         $sEmail)
                       )
        {
            $_mReturn = $sEmail;
        }
        else
        {
            $_mReturn = FALSE;
        }

        return $_mReturn;
    }

    //----------------------------------------------------//
    // Valiade date format                                //
    //----------------------------------------------------//
    Function ValidateDate($sDate="")
    {
        static $_mReturn;

        if ( preg_match( "/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/" , $sDate ) )
        {
            $_mReturn = $sDate;
        }
        else
        {
            $_mReturn = false;
        }

        return $_mReturn;
    }

    //----------------------------------------------------//
    // Create random password                             //
    //----------------------------------------------------//
    Function MakePassword()
    {
        static $_sPass = "";
        static $_iCount = 0;
        static $c;
        static $_aChars = array(
        "1","2","3","4","5","6","7","8","9","0",
        "a","A","b","B","c","C","d","D","e","E",
        "f","F","g","G","h","H","i","I","j","J",
        "k","K","l","L","m","M","n","N","o","O",
        "p","P","q","Q","r","R","s","S","t","T",
        "u","U","v","V","w","W","x","X","y","Y",
        "z","Z"
        );

        srand((double)microtime()*1000000);

        for($c = 0; $c < 8; $c++)
        {
            $_sPass .= $_aChars[ rand(0, ( count($_aChars) - 1 ) ) ];
        }
        return $_sPass;
    }

    //----------------------------------------------------//
    // Set Cookie with our cookie-name                    //
    //----------------------------------------------------//
    Function my_SetCookie( $sName, $sValue = "", $iSticky = 1 )
    {
        global $CNK;
        static $_sExpires;

        if ($iSticky == 1)
        {
            $_sExpires = time() + 3600*24*60;
        }

        $CNK->__aCfg['COOKIE_DOMAIN'] =
                ( $CNK->__aCfg['COOKIE_DOMAIN'] == "" ) ? ""  : $CNK->__aCfg['COOKIE_DOMAIN'];
        $CNK->__aCfg['COOKIE_PATH']   =
                ( $CNK->__aCfg['COOKIE_PATH']   == "" ) ? "/" : $CNK->__aCfg['COOKIE_PATH'];

        $sName = $CNK->__aCfg['COOKIE_ID'] . $sName;

        setcookie($sName, $sValue,
                  $_sExpires,
                  $CNK->__aCfg['COOKIE_PATH'],
                  $CNK->__aCfg['COOKIE_DOMAIN']
                 );

    }

    //----------------------------------------------------//
    // Get Cookie with only our cookie-name               //
    //----------------------------------------------------//
    Function my_GetCookie($sName)
    {
        global $CNK;
        $_mReturn = False;;

        if(isset($CNK->__aSys["COOKIE"][$CNK->__aCfg['COOKIE_ID'].$sName]))
        {
            $_mReturn = urldecode(
                        $CNK->__aSys["COOKIE"][$CNK->__aCfg['COOKIE_ID'].$sName]
                        );
        }

        return $_mReturn;
    }

    //----------------------------------------------------//
    // Write configuration file                           //
    //----------------------------------------------------//
    function WriteConfig( $sSection, $aNewCfgData )
    {
        global $CNK;
        $_aNewConfig = array();
        $_aCfgFileCache = array();
        $_hFile = '';
        $_sLine = '';

        $_aCfgFileCache = array();

        if ( empty( $sSection ) ) return false;

        if ( file_exists( CONF.$sSection.'.cfg' ) )
        {
            // Chache old config
            $_hFile = @fopen( CONF.$sSection.'.cfg', 'r' );
            while ( !feof( $_hFile ) )
            {
                $_sLine = trim( @fgets( $_hFile, 0xFFF ) );
                $_sLine = trim($_sLine);
                if ( !empty( $_sLine ) )
                {
                    list( $sKey, $sVal ) = split( '=', $_sLine, 2 );
                    $_aCfgFileCache[ trim( $sKey ) ] = trim( $sVal );
                }
            }
            @fclose( $_hFile );
        }
        else
        {
            $_aCfgFileCache = array();
        }

        // Merge old with update into new
        $_aNewConfig = array_merge( (array)$_aCfgFileCache, (array) $aNewCfgData  );

        // delete old cfg file
        if ( file_exists( CONF.$sSection.'.cfg' ) )
        {
           @chmod( CONF.$sSection.'.bak', 0777 );
           @unlink( CONF.$sSection.'.cfg' );
        }

        // Prepare file content
        $_iSize = sizeof( $_aNewConfig );
        $_iCnt = 0;
        foreach ( $_aNewConfig as $sKey=>$sVal )
        {
            if ( $_iCnt == $_iSize )
            {
                $_sNewCfgFC .= "{$sKey}={$sVal}";
            }
            else
            {
                $_sNewCfgFC .= "{$sKey}={$sVal}\x0A";
            }
            $_iCnt++;
        }
        // Write file
        $_hFile = @fopen( CONF.$sSection.'.cfg', 'wb' );
        @fputs( $_hFile, $_sNewCfgFC );
        @fclose( $_hFile );
        @chmod( CONF.$sSection.'.cfg', 0666 );

        return true;
    }

    //-------------------------------------------------------//
    // Get server load into space-separeted string           //
    //-------------------------------------------------------//
    Function GetServerLoad()
    {
        static $_fStream;
        static $_sLine;
        static $_aServerLoad = array();
        static $_mReturn;

        if ( file_exists('/proc/loadavg') )
        {
            if ( $_fStream = @fopen('/proc/loadavg', 'r' ) )
            {
                $_sLine = @fread( $_fStream, 30 );
                @fclose( $_fStream );
                $server_load = explode( " ", $_sLine );
                $_mReturn =  "{$_aServerLoad[0]}
                              {$_aServerLoad[1]}
                              {$_aServerLoad[2]}
                              {$_aServerLoad[3]}
                              {$_aServerLoad[4]}";
            }
            else
            {
                $_mReturn = false;
            }
        }
        else
        {
            $_mReturn = false;
        }

        return $_mReturn;
    }

    //-------------------------------------------------------//
    // Check server load and return boolean result           //
    //-------------------------------------------------------//
    Function CheckServerLoad()
    {
        static $_fStream;
        static $_sLine;
        static $_aServerLoad = array();
        static $_aLoadAvg    = array();
        static $_mReturn;

        if ( file_exists('/proc/loadavg') )
        {
            if ( $_fStream = @fopen( '/proc/loadavg', 'r' ) )
            {
                $_sLine = @fread( $_fStream, 6 );
                @fclose( $_fStream );
                $_aLoadAvg = explode( " ", $_sLine );
                $_aServerLoad = trim($_aLoadAvg[0]);

                if ($_aServerLoad > 3)
                {
                    $_mReturn = false;
                }
                else
                {
                    $_mReturn = true;
                }
            }
            else
            {
                $_mReturn = true;
            }
        }
        else
        {
            $_mReturn = true;
        }

        return $_mReturn;
    }

    //----------------------------------------------------//
    // Translate relative path to real                    //
    // ex.: './some_path/' -> '/w/htdocs/some_path/'      //
    //----------------------------------------------------//
    Function RelativeToReal( $sPath )
    {
       global $_SERVER;
       static $_sReturn;
       static $_sMePath;
       static $_aMePath = array();

       $_aMePath = pathinfo( $_SERVER["SCRIPT_FILENAME"] );
       $_sMePath = $_aMePath["dirname"];

       $sPath = preg_replace( "#^\.#", "", $sPath );

       $_sReturn = $_sMePath.$sPath;

       return $_sReturn;
    }

    //----------------------------------------------------//
    // Translate relative path to url                     //
    // './some_path/' -> 'http://domain.com/some_path/'   //
    //----------------------------------------------------//
    Function RelativeToUrl( $sPath )
    {
        global $CNK;
        static $_sReturn  = '';
        static $_sBaseUrl = '';

        $sPath = preg_replace( "#^\.#", "", $sPath );

        if ( preg_match( "#/$#", $CNK->__aCfg["URL_BASE"] ) )
        {
            $_sBaseUrl = preg_replace( "#/$#", "", $CNK->__aCfg["URL_BASE"] );
        }
        else
        {
            $_sBaseUrl = $CNK->__aCfg["URL_BASE"];
        }

        $_sReturn = $_sBaseUrl.$sPath;

        return $_sReturn;
    }

    //-------------------------------------------------//
    // Shows dumped given variable                     //
    // ex: ShowDump( $foo, '$foo', 1 );            //
    //-------------------------------------------------//
    Function ShowDump( $sVarName, $sVarPrintName, $iMethod=1 )
    {
       if ( $iMethod == 1 )
       {
          echo "<pre>";
          echo "{$sVarPrintName}:<br>";
          var_dump( $sVarName );
          echo "</pre>";
       }
       elseif ( $iMethod == 2 )
       {
          echo "<pre>";
          echo "{$sVarPrintName}:<br>";
          print_r( $sVarName );
          echo "</pre>";
       }
       return;
    }

    //----------------------------------------------------//
    // Show message when system offline                   //
    //----------------------------------------------------//
    Function OfflineMsg( $sMsg )
    {
        global $aCFG;
        static $_sOutPut = "";


        $_sOutPut = "<html>
                <head>
                <title>System Offline</title>
                <style>
                    P,BODY{ font-family:arial,sans-serif; font-size:11px; }
                </style>
                </head>
                <body>
                &nbsp;<br><br>
                <blockquote>
                <h2>There appears to be the system is offline!</h2>
                {$msg}
                <br><br>
                <b><i>
                We apologise for any inconvenience&nbsp;[
                <a href='mailto:{$aCFG['MAILER_ADMIN']}?subject=System+Offline'>system administrator</a>
                ]</i></b>
                </blockquote>
                </body>
                </html>
               ";


        echo($_sOutPut);
        die("");

    }

}

?>
