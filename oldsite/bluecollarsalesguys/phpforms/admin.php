<?php

/*
+--------------------------------------------------------------------------
|
|   ADMIN AREA
|   phpForms, form processing script
|   by Igor M. Belobrov,
|   Conkurent, LLC, Web Programming Department
|   Email: igor@conkurent.com
|   ICQ #: 276745076
|   =========================================
|   Powered by Conkurent Engine,
|   written by Cyrill Polikarpov
|   Conkurent, LLC, Web Programming Department
|   Email: clio@conkurent.com
|   ICQ #: 99885395
|   =========================================
|   (c) 2004-2007 Conkurent, LLC
|   Visit: http://conkurent.com
|   Email: company@conkurent.com
|
+--------------------------------------------------------------------------
*/


// Set primary error_reporting
error_reporting ( E_ERROR );
set_magic_quotes_runtime( 0 );

define ( 'CTIMER'   ,   microtime()          );
define ( 'TOP'      ,   './'                 );
define ( 'IMG'      ,   './cnk-img/' );
define ( 'INC'      ,   './cnk-inc/'         );
define ( 'CONF'     ,   './cnk-conf/'        );
define ( 'DRIVERS'  ,   './cnk-inc/drivers/' );
define ( 'LIBS'     ,   './cnk-inc/libs/'    );
define ( 'I18N'     ,   './cnk-i18n/'        );
define ( 'MODULES'  ,   './cnk-admin/'       );
define ( 'MODULES_1',   './cnk-mods/'        );
define ( 'FONTS_DIR',   './cnk-fonts/'       );

//----------------------------------------------------//
// Load list of errors                                //
//----------------------------------------------------//
@require_once( TOP . 'cnk-sys_errors.php' );

//----------------------------------------------------//
// Main class constructor                             //
//----------------------------------------------------//
class CNK
{
    var $__oDb, $__oStd, $__oDebug, $__oDisplay, 
        $__oLs, $__oI18n, $__aIn, $__aCfg, 
        $__aWords, $__aModules, $__sWorkingModule, $__sBaseUrl;
    
    Function CNK()
    {
        global $oDB, $oSTD, $oDEBUG, $oDISPLAY, $oLS, $oI18N,
               $aCFG, $aMODULES, $oVARS;
        
        $this->__oDb        = &$oDB;
        $this->__oStd       = &$oSTD;
        $this->__oDebug     = &$oDEBUG;
        $this->__oDisplay   = &$oDISPLAY;
        $this->__oLs        = &$oLS;
        $this->__oI18n      = &$oI18N;
        $this->__aSys       = &$oVARS->__aSYS;
        
        $this->__aCfg       = &$aCFG;
        $this->__aModules   = &$aMODULES;
    }

    Function LoadClass( $class_name, $main=False )
    {
        if ( $main )
        {
            return new $class_name();
        }
        else
        {
            return new $class_name;
        }
    }

}

//----------------------------------------------------//
// Initialize debugger                                //
//----------------------------------------------------//
@require_once( LIBS . 'debug.lib.php' );
$oDEBUG = new DEBUG();

$oDEBUG->StartTimer( CTIMER );

define ( 'PHP_EXT', '.php' );

//----------------------------------------------------//
// Load utils                                         //
//----------------------------------------------------//
require_once( INC . 'utils.inc' . PHP_EXT );

//----------------------------------------------------//
// Load configuration                                 //
//----------------------------------------------------//
$aCFG = _LoadConfig( CONF );
define ( 'TPL_EXT', $aCFG["TPL_EXT"] );


if ( ! @file_exists( $aCFG['PATH_BASE'].'install.lock' ) )
{
    _ShowError( __LINE__.': '.ERROR_NO_INSTALLATION_FOUND );
}

//----------------------------------------------------//
// Load classes                                       //
//----------------------------------------------------//
@require_once( INC . 'vars.class' . PHP_EXT );
$oVARS = new VARS();

@require_once( INC . 'func.class' . PHP_EXT );
$oSTD = new FUNC();

//----------------------------------------------------//
// Load libraries                                     //
//----------------------------------------------------//
@require_once( LIBS . 'ls.lib' . PHP_EXT );
$oLS = new LS();

//----------------------------------------------------//
// Create database object                             //
//----------------------------------------------------//
$_aSqlCfg = _LoadSqlConfig();

@require_once( INC . 'db.class' . PHP_EXT );

$oDB =& DB::Loader( $_aSqlCfg["DATABASE_TYPE"] );
$oDB->Connect( $_aSqlCfg );

//----------------------------------------------------//
// Load display                                       //
//----------------------------------------------------//
@require_once( INC . 'display.class' . PHP_EXT );
$oDISPLAY = new DISPLAY( false );

//----------------------------------------------------//
// Init main class CNK                                //
//----------------------------------------------------//
$CNK = new CNK();

//----------------------------------------------------//
// Get modules list                                   //
//----------------------------------------------------//
$CNK->__aModules = _GetModulesList();

//----------------------------------------------------//
// Grab incoming data                                 //
//----------------------------------------------------//
$CNK->__aIn = $oVARS->GrabInput();

//----------------------------------------------------//
// Load multilingual support                          //
//----------------------------------------------------//
@require_once( INC . 'i18n.class' . PHP_EXT );
$oI18N = new LANG( $aCFG["LANG_DEFAULT"] );

//----------------------------------------------------//
// Define BASE_URL                                    //
//----------------------------------------------------//
    $CNK->__sBaseUrl = 
      $CNK->__aCfg["URL_BASE"] . 'admin' . PHP_EXT . '?sid=0';

//----------------------------------------------------//
// Define module to load                              //
//----------------------------------------------------//

$CNK->__sWorkingModule = 
   ( isset( $CNK->__aIn['act'] ) ) ? $CNK->__aIn['act'] : 'idx';

if ( !isset( $CNK->__aModules[ $CNK->__sWorkingModule ] ) )
{
    $CNK->__sWorkingModule = 'idx';
}

//----------------------------------------------------//
// Load selected module                               //
//----------------------------------------------------//
define ( 'F_WORKING_MODULE',
         $CNK->__aModules[ $CNK->__sWorkingModule ].$CNK->__sWorkingModule.'.mod'.PHP_EXT
       );
       
@require ( F_WORKING_MODULE );

//----------------------------------------------------//
// Final :) execution time                            //
//----------------------------------------------------//
//echo "<br>Script execution time: ".$oDEBUG->StopTimer();

$oDB->CloseConnection();

exit;

// ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ //
//                     THE END                        //
//++++++++++++++++++++++++++++++++++++++++++++++++++++//

//-------------------------------------------------//
// Convert given relative path to real path        //
//-------------------------------------------------//
Function _RelativeToReal( $sPath )
{
    global $_SERVER;
    static $_sReturn = '';
    static $_sMePath = '';
    static $_aMePath = array();
    
    $_aMePath = pathinfo( __FILE__ );
    $_sMePath = $_aMePath["dirname"];

    $sPath = preg_replace( "#^\.#", "", $sPath );
    
    $_sReturn = $_sMePath.$sPath;
    
    return $_sReturn;
}

//-------------------------------------------------//
// Shows dumped given variable                     //
// ex: _ShowDump( $foo, '$foo', 1 );               //
//-------------------------------------------------//
Function _ShowDump( $sVarName, $sVarPrintName, $iMethod=1 )
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

//-------------------------------------------------//
// Independent FileSystemOperations class          //
//-------------------------------------------------//
class FSO
{
    var $__aFiles       = Array();
    var $__aDirs        = Array();

    Function _GetFiles( $sDir, $bReturn = false )
    {
        static $_fStream;
        static $_sPointer;      
        
        $sDir = preg_replace( "#^\.#", "", $sDir );
        $sDir = preg_replace( "#/$#",  "", $sDir );

        if ( file_exists($sDir) ) 
        {
            if ( is_dir($sDir) ) 
            {
                $_fStream = opendir($sDir);
                while (($_sPointer = readdir($_fStream)) !== false) 
                {
                    if (($_sPointer != ".") && ($_sPointer != "..")) 
                    {
                        if ( !is_dir($sDir."/".$_sPointer)) 
                        {
                            $this->__aFiles[] = $sDir."/".$_sPointer;
                        }
                    }
                }
                closedir($_fStream);
                
                if ( (bool)$bReturn )
                {
                    return $this->__aFiles;
                }
                
            } 
            else 
            {
                return FALSE;
            }
        } 
        else 
        {
            return FALSE;
        }

    }
    
    Function _GetDirs( $sDir, $bReturn = false ) 
    {
        static $_fStream;
        static $_sPointer;

        $sDir = preg_replace( "#^\.#", "", $sDir );
        $sDir = preg_replace( "#/$#", "", $sDir );

        if ( file_exists($sDir) ) 
        {
            if ( is_dir($sDir) ) 
            {

                $_fStream = opendir($sDir);
                while (($_sPointer = readdir($_fStream)) !== false) 
                {
                    if ( ($_sPointer != ".") && ($_sPointer != "..") ) 
                    {
                        if ( is_dir($sDir.'/'.$_sPointer) ) 
                        {
                           $this->__aDirs[] = $sDir.'/'.$_sPointer;
                        } 
                        elseif ( is_file($sDir.'/'.$_sPointer) ) 
                        {
                           next;
                        }
                    }
                }
                closedir($_fStream);
                
                if ( (bool)$bReturn )
                {
                    return $this->__aDirs;
                }
            } 
            else 
            {
                _ShowError( ERROR_FS_TARGET_IS_NOT_A_DIR );
            }
        } 
        else 
        {
            _ShowError( ERROR_FS_TARGET_DOESNT_EXIST );
        }
    }
}

//----------------------------------------------------------------------------//
// Load SQL Configuration                                                     //
//----------------------------------------------------------------------------//
Function _LoadSqlConfig()
{
       global $aCFG;
       static $_oFso;
       static $_fStream;
       static $_sLine;
       static $_aData = array();
    
       $_oFso = new FSO;
       $_aConfigFiles = $_oFso->_GetFiles( _RelativeToReal( CONF.'databases/' ) ,true);   
    
       
       if ( $aCFG["DATABASE_MODE"] == 'multiple' )
       {
            for ( $c = 0; $c<sizeof($_aConfigFiles); $c++ )
            {
                $_fStream = @fopen( $_aConfigFiles[$c], 'r' );
                while ( !feof( $_fStream ) )
                {
                    $_sLine = trim( @fgets( $_fStream, 0xFFF ) );
                    if ( !empty($_sLine) )
                    {
                        $_aData[] = explode( '=', $_sLine, 2 );    
                    }
                }
                @fclose( $_fStream );        
            }   
       }
       else
       {
            $_fStream = @fopen( CONF.'databases/default.sql.cfg', 'r' );
            while ( !feof( $_fStream ) )
            {
               $_sLine = trim( @fgets( $_fStream, 0xFFF ) );
               if ( !empty($_sLine) )
               {
                   $_aData[] = explode( '=', $_sLine, 2 );    
               }
            }
            @fclose( $_fStream );
       }

       for ( $c = 0; $c<sizeof( $_aData ); $c++ )
       {
          $_aCFG[ $_aData[$c][0] ] = $_aData[$c][1];   
       }
       
       return $_aCFG;
       
}

//----------------------------------------------------------------------------//
// Local modules routine                                                      //
//----------------------------------------------------------------------------//
Function _GetModulesList()
{   
    static $_oFso;
    static $_aModules    = array();
    static $_aMods       = array();
    static $_aPathParts  = array();
   
    $_oFso = new FSO;
    $_aModules = $_oFso->_GetDirs( _RelativeToReal( MODULES ) ,true);
    
    for ( $c = 0; $c<count($_aModules); $c++ )
    {
        $_aPathParts =  pathinfo($_aModules[$c]);       
        $_aMods[ $_aPathParts["basename"] ] = $_aModules[$c].'/';
    }
    return $_aMods;
}
    

//----------------------------------------------------------------------------//
// Local Error function                                                       //
//----------------------------------------------------------------------------//
Function _ShowError( $sErrMessage )
{
    global $_SERVER;
    
    print  "<html>
            <head>
            <title>System Error</title>
            <style>
                P,BODY,TD { font-family:arial,sans-serif; font-size:11px; }
            </style>
            </head>
            
            <body>
                &nbsp;<br><br>
                <blockquote>
                <h2 style='color: red;'>There appears to be a system error.</h2>
                You can try to refresh the page by clicking 
                <a href=\"javascript:window.location=window.location;\">here</a>.
                <br> 
                If this does't fix the error, please, contact the site 
                <a href='mailto:{$_SERVER['SERVER_ADMIN']}?subject=System+Error'>
                administrator</a>
                <br><br><b>Error returned</b><br><br>
                <table width='300' cellSpacing='0' cellPadding='0' border='0'>
                <tr>
                   <td style='border: 1px dotted #dddddd; background-color: #f7f7f7; padding: 10px;'>
                   <br>
                   {$sErrMessage}
                   <br><br>
                   <td>
                </tr></table>
                <br><br><b><i>We apologise for any inconvenience</i></b>
                </blockquote>
            </body>
            </html>
            ";    
    exit;

}


?>
