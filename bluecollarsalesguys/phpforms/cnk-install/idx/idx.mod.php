<?php
/*
+--------------------------------------------------------------------------
|
|   INSTALLATION MODULE
|   phpForms, form processing script
|   =========================================
|   Module written by Igor M. Belobrov
|   Conkurent, LLC, Web Programming Department
|   Email: igor@conkurent.com
|   ICQ #: 276745076
|   =========================================
|   Powered by Conkurent Engine v2.0,
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

$_OBJ = new IDX();

class IDX
{
    var $__sDisplayId;
    var $__aTplData = array();
    var $__sHtml;
    var $__aModVars = array();
    
    Function IDX()
    {
        global $CNK;

        /* {{{ Run autoconfigurator }}} */
        $this->_ThisAutoConfig();

        $mRidWrapper = $CNK->__oDisplay->LoadTpl( 'wrapper' );

        /* {{{ Prepare base url for template }}} */
        $this->__aTplData["base_url"] = $CNK->__sBaseUrl;
        $this->__aTplData["img_path"] = $this->__aModVars["my_mod_imgs"];
        $this->__aTplData["APP_NAME"] = 'phpForms installation';

        $this->__aTplData['L_main_action'] = '..:: phpForms installation ::..';

        // Check if installtion is locked
        if ( file_exists( TOP . 'install.lock' ) )
        {
           _ShowError( __LINE__.': '.ERROR_001 );
        }
        
        if ( ( !empty($CNK->__aIn['code']) ) && 
             ( $CNK->__aIn['code'] != '00' ) &&
             ( $CNK->__aIn['code'] != '01' ) &&
             ( $CNK->__aIn['code'] != '02' )  )
        {
           $CNK->__aCfg = array_merge( 
                            $CNK->__aCfg, 
                            _LoadConfig( CONF )
                          );
        }

        if ( !isset($CNK->__aIn['code']) ) $CNK->__aIn['code'] = ' ';

        switch ( $CNK->__aIn['code'] )
        {
            case '01':
                      $this->_ShowGeneralAndDbConf();
                      break;

            case '02':
                      $this->_SaveConfig();
                      break;

            case '03':
                      $this->_CreateAdmin();
                      break;

            case 'u00':
                      $this->_PromptUninstall();
                      break;

            case 'u01':
                      $this->_Uninstall();
                      break;


            case '00':
            default:
                      $this->_ShowTerms();
        }       

        /* {{{ Output html to browser }}} */
        $this->__sHtml = $CNK->__oDisplay->EvalTpl( $this->__aTplData, '', $mRidWrapper );
        echo $this->__sHtml;
    } 
    
    //----------------------------------------------------//
    // Module IDX stuff                                   //
    //----------------------------------------------------//

    function _PromptUninstall()
    {
       global $CNK;
       $mRidUn = $CNK->__oDisplay->LoadTpl( 'uninstall0' );
       $this->__aTplData['APP_NAME']      = 'phpForms uninstallation';
       $this->__aTplData['L_main_action'] = '..:: phpForms uninstallation ::..';
       $this->__aTplData["L_page_title"]  = "Remove items";
       $this->__aTplData["info_header"]   = "Select items you want to remove";
       
       $this->__aTplData["T_wrapper_content"] = 
         $CNK->__oDisplay->EvalTpl( array(), '', $mRidUn );
    }

    function _Uninstall()
    {
       global $CNK;

       $CNK->__aCfg = array_merge( $CNK->__aCfg, 
                               _LoadConfig($this->__aModVars["my_mod_conf"]) );
       $CNK->__aCfg = array_merge( $CNK->__aCfg, _LoadSqlConfig() );

       $mRidUn = $CNK->__oDisplay->LoadTpl( 'uninstall1' );
       $aTplUn = array();
       $this->__aTplData['APP_NAME']      = 'phpForms uninstallation';
       $this->__aTplData['L_main_action'] = '..:: phpForms uninstallation ::..';
       $this->__aTplData["L_page_title"]  = "Remove items";
       $this->__aTplData["info_header"]   = "Result";
       $nItems = 0;

       // Drop databases
       if ( isset($CNK->__aIn['dbt'])   && $CNK->__aIn['dbt']!='' &&
            isset($CNK->__aCfg) && isset($CNK->__aCfg["DATABASE_TYPE"]) )
       {
          define( 'PHP_EXT', $CNK->__aCfg['PHP_EXT'] );

          // Try to connect to database
          require_once( INC . 'db.class' . PHP_EXT );
          $CNK->__oDb =& DB::Loader( $CNK->__aCfg["DATABASE_TYPE"] );

          $_bConnectionStatus = $CNK->__oDb->Connect( $CNK->__aCfg, false, true );

          $aSql = array();
          if ( (bool)$_bConnectionStatus )
          {
             $aSql[] = "DROP TABLE IF EXISTS pf_admins;";
             $aSql[] = "DROP TABLE IF EXISTS pf_checks;";
             $aSql[] = "DROP TABLE IF EXISTS pf_checks_values;";
             $aSql[] = "DROP TABLE IF EXISTS pf_colors;";
             $aSql[] = "DROP TABLE IF EXISTS pf_fields;";
             $aSql[] = "DROP TABLE IF EXISTS pf_forms;";
             $aSql[] = "DROP TABLE IF EXISTS pf_items;";
             $aSql[] = "DROP TABLE IF EXISTS pf_layouts;";
             $aSql[] = "DROP TABLE IF EXISTS pf_mail_tpls;";
             $aSql[] = "DROP TABLE IF EXISTS pf_pages;";
             $aSql[] = "DROP TABLE IF EXISTS pf_pre_values;";
             $aSql[] = "DROP TABLE IF EXISTS pf_predefined;";
             $aSql[] = "DROP TABLE IF EXISTS pf_prop_values;";
             $aSql[] = "DROP TABLE IF EXISTS pf_properties;";
             $aSql[] = "DROP TABLE IF EXISTS pf_submissions;";
             $aSql[] = "DROP TABLE IF EXISTS pf_types;";
             for ( $i=0; $i < count($aSql); $i++ )
             {
               $CNK->__oDb->ExecQuery( $aSql[ $i ] );
             }

             $nItems++;
          }
       }

       // Remove phpForms source file
       if ( isset($CNK->__aIn['src'])   && $CNK->__aIn['src']!='' )
       {
          $sDirPath = getcwd() . DS;
          // Dirs
          RemoveDir( $sDirPath . 'cnk-admin'   );
          RemoveDir( $sDirPath . 'cnk-conf'    );
          RemoveDir( $sDirPath . 'cnk-dev'     );
          RemoveDir( $sDirPath . 'cnk-i18n'    );
          RemoveDir( $sDirPath . 'cnk-img'     );
          RemoveDir( $sDirPath . 'cnk-inc'     );
          RemoveDir( $sDirPath . 'cnk-mods'    );
          RemoveDir( $sDirPath . 'cnk-install' );
          RemoveDir( $sDirPath . 'checks'      );
          RemoveDir( $sDirPath . 'layouts'     );
          RemoveDir( $sDirPath . 'types'       );
          RemoveDir( $sDirPath . 'cnk-tmp'     );
          RemoveDir( $sDirPath . 'cnk-setup'   );
          // Files
          @unlink( $sDirPath . 'install.php'        );
          @unlink( $sDirPath . 'phpforms.php'       );
          @unlink( $sDirPath . 'cnk-sys_errors.php' );
          @unlink( $sDirPath . 'index.php'          );
          @unlink( $sDirPath . 'admin.php'          );
          $nItems++;
       }

       // Remove user uploaded files directory
       if ( isset($CNK->__aIn['files']) && $CNK->__aIn['files']!='' )
       {
          if ( isset($CNK->__aCfg['UPLOADED_FILES_PATH']) && 
                     $CNK->__aCfg['UPLOADED_FILES_PATH']!='' )
          {
             $sUplFilesPath = preg_replace( "/\/$/", '', $CNK->__aCfg['UPLOADED_FILES_PATH'] );
             if ( RemoveDir( $sUplFilesPath ) ) $nItems++;
          }
       }

       $aTplUn['items'] = $nItems;

       $this->__aTplData["T_wrapper_content"] = 
         $CNK->__oDisplay->EvalTpl( $aTplUn, '', $mRidUn );
    }

    // 1.
    /* {{{ Show terms and conditions }}}*/
    function _ShowTerms()
    {
       global $CNK;

       $mRidTerms = $CNK->__oDisplay->LoadTpl( 'terms' );
       $aTplTerms = array();
       $this->__aTplData["L_page_title"] = "Terms and conditions";
       $this->__aTplData["info_header"] = 
           "1. Please read our terms and conditions before proceed...";
 
       // Read terms and conditions text file
       $mRidFile = @fopen( $this->__aModVars["my_mod_path"].'terms.dat', 'r' );
       $aTplTerms["T_terms_conditions"] = @fread( $mRidFile, filesize($this->__aModVars["my_mod_path"].'terms.dat') );
       @fclose( $mRidFile );
       
       $this->__aTplData["T_wrapper_content"] = 
            $CNK->__oDisplay->EvalTpl( $aTplTerms, '', $mRidTerms );
    }

    // 2.
    /* {{{ Show general and db configuration }}} */
    function _ShowGeneralAndDbConf()
    {
       global $CNK;

       $mRidGenDb = $CNK->__oDisplay->LoadTpl( 'gen_db' );
       $aTplGenDb = array();

       $this->__aTplData["L_page_title"] = "General settings and database configuration";
       $this->__aTplData["info_header"] = 
           "2. Please check if all autodetected parameters are correct and complete the blanks...";

       $aTplGenDb['SITE_NAME'] = 'phpForms';

       $aTplGenDb['PATH_BASE']           = getcwd() . DS;
       $aTplGenDb['UPLOADED_FILES_PATH'] = $aTplGenDb['PATH_BASE'] . 'files' . DS;


       $sPhpSelf = '';
       if ( isset( $PHP_SELF ) ) $sPhpSelf = $PHP_SELF;
       else $sPhpSelf = $_SERVER['PHP_SELF'];

       $sPhpSelfDir = preg_replace( "/\/.*?$/", '/', $sPhpSelf );
       $sPhpSelfDir = preg_replace( "/\/[^\/]*$/", "/", $sPhpSelf );
       $aTplGenDb['URL_BASE'] = 'http://' . $_SERVER['HTTP_HOST'] . $sPhpSelfDir;

       $aTplGenDb['DATABASE_HOST']       = "localhost";
       $aTplGenDb['DATABASE_PORT']       = '';
       $aTplGenDb['DATABASE_NAME']       = 'phpforms';
       $aTplGenDb['DATABASE_USERNAME']   = 'root';
       $aTplGenDb['DATABASE_PASSWORD']   = '';
       $aTplGenDb['DATABASE_TPRX']       = 'pf_';

       $this->__aTplData["T_wrapper_content"] = 
            $CNK->__oDisplay->EvalTpl( $aTplGenDb, '', $mRidGenDb );
    }

    // 3.
    /* {{{ Save config }}} */
    function _SaveConfig()
    {
       global $CNK;

       $aCfg = $this->_PrepareConfiguration();

       /**
       * Check for write permissions 
       * for our root path.
       **/
       @clearstatcache();
       if ( @function_exists('is_writeable') ) 
       {
          // primary check
          if ( ! @is_writeable( $aCfg['main']['PATH_BASE'] ) )
          {
             // forced check
             if ( !_ForceWriteableCheck( $aCfg['main']['PATH_BASE'] ) )
             {
                _ShowError( __LINE__.': '.ERROR_004 );  
             }
          }
       }
       else
       {
          // forced check
          if ( !_ForceWriteableCheck( $aCfg['main']['PATH_BASE'] ) )
          {
             _ShowError( __LINE__.': '.ERROR_004 ); 
          }
       }
       @clearstatcache();

       $this->_CreateCfgFiles( $aCfg );
       $this->_CreateUploadDir( $aCfg['main']['UPLOADED_FILES_PATH'] );
       $this->_UpdateIncludeFile( $aCfg['main']['URL_BASE'] );
       $this->_CreateTables();
       $this->_ShowCreateAdmin();
    }

    function _PrepareConfiguration()
    {
       global $CNK;
       $aCfg = array();

       // App
       $aCfg['app']['APP_NAME']            = "phpForms";
       $aCfg['app']['APP_BRANCH']          = "CNK_PHPFORMS";
       $aCfg['app']['APP_VERSION']         = "1.0.7";
       $aCfg['app']['APP_VERSION_FIX']     = "1.0.7.2";
       $aCfg['app']['APP_PUBLISHER']       = "Conkurent, LLC; http://www.conkurent.com; mailto:company@conkurent.com";
       $aCfg['app']['APP_COPYRIGHT']       = "2004-2007, <a class=\"extlnk\" href='http://conkurent.com'>Conkurent, LLC</a>";
       $aCfg['app']['APP_SUPPORT']         = "support@conkurent.com";
       $aCfg['app']['APP_BUGTRACK_PATH']   = "http://bugs.conkurent.com";
       $aCfg['app']['APP_BUGTRACK_ID']     = "NULL";

       // Cookies
       $aCfg['cookies']['COOKIE_ID']         = "cnk_";
       $aCfg['cookies']['COOKIE_DOMAIN']     = "";
       $aCfg['cookies']['COOKIE_PATH']       = "/";

       // Database
       $aCfg['database']['DATABASE_MODE']    = 'single';
       $aCfg['database']['DATABASE_SUPP']    = 'mysql';

       // MySQL
       $aCfg['mysql']['DATABASE_TYPE']    = "mysql";
       $aCfg['mysql']['DATABASE_SAFE']    = "false";
       $aCfg['mysql']['DATABASE_HOST']    = $CNK->__aIn['db_host'];
       $aCfg['mysql']['DATABASE_PORT']    = $CNK->__aIn['db_port'];
       $aCfg['mysql']['DATABASE_NAME']    = $CNK->__aIn['db_name'];
       $aCfg['mysql']['DATABASE_USER']    = $CNK->__aIn['db_user'];
       $aCfg['mysql']['DATABASE_PASS']    = $CNK->__aIn['db_pass'];
       $aCfg['mysql']['DATABASE_TPRX']    = 'pf_';

       // i18n - languages configuration
       $aCfg['i18n']['LANG_SUPPORTED']    = "en";
       $aCfg['i18n']['LANG_DEFAULT']      = "en";
       $aCfg['i18n']['LANG_DOCS_DIR']     = "docs";
       $aCfg['i18n']['LANG_DOCS_EXT']     = ".htm";

       // mailer
       $aCfg['mailer']['MAILER_METHOD']   = "native";
       $aCfg['mailer']['MAILER_THREADS']  = "1";
       $aCfg['mailer']['MAILER_ADMIN']    = "";

       // Main
       $aCfg['main']['SITE_NAME']           = $CNK->__aIn['site_name'];
       $aCfg['main']['PATH_BASE']           = $CNK->__aIn['path_base'];
       $aCfg['main']['PATH_IMAGES']         = $aCfg['main']['PATH_BASE'] . 'cnk-img' . DS;
       $aCfg['main']['UPLOADED_FILES_PATH'] = $CNK->__aIn['path_uploaded'];
       $aCfg['main']['URL_BASE']            = $CNK->__aIn['url_base'];
       $aCfg['main']['SYSTEM_STATUS']       = "online";
       $aCfg['main']['SYSTEM_OFFLINE']      = "Sorry, system is offline now. Visit us later, please.";

       // phpforms - application conf
       $aCfg['phpforms']['PHP_EXT']              = ".php";
       $aCfg['phpforms']['TPL_EXT']              = ".htm";
       $aCfg['phpforms']['LAYOUT_IMG_EXTENSION'] = ".gif";
       $aCfg['phpforms']['LAYOUT_IMG_PATH']      = "/layouts/img/";
       $aCfg['phpforms']['LAYOUT_TPL_PATH']      = "layouts/html/";
       $aCfg['phpforms']['TYPES_TPL_PATH']       = "types/";
       $aCfg['phpforms']['CHECKS_JS_PATH']       = "checks/";

       // Session
       $aCfg['session']['SESSION_ALLOW']         = 'false';
       $aCfg['session']['SESSION_STORAGE']       = 'fs';
       $aCfg['session']['SESSION_IDENTID']       = 'sid';
       $aCfg['session']['SESSION_EXPIRATION']    = '3600';

       return $aCfg;
    }

    function _CreateCfgFiles( $aCfg )
    {
       global $CNK;

       IsDirWritable( CONF );
       IsDirWritable( CONF.'/databases' );

       // Write .htaccess
       $sOutput = "Options -All -Multiviews\nOrder deny,allow\nDeny from all\n";
       $mFile   = @fopen( CONF.'/.htaccess', 'wb' );
       @fwrite( $mFile, $sOutput );
       @fclose( $mFile );

       // app.cfg
       $CNK->__oStd->WriteConfig( 'app',       $aCfg['app']      );
       // cookies.cfg
       $CNK->__oStd->WriteConfig( 'cookies',   $aCfg['cookies']  );
       // database.cfg
       $CNK->__oStd->WriteConfig( 'database',  $aCfg['database'] );
       // databases/default.sql.cfg
       $CNK->__oStd->WriteConfig( "databases/default.sql", $aCfg['mysql'] );
       // i18n.cfg
       $CNK->__oStd->WriteConfig( 'i18n',      $aCfg['i18n']     );
       // mailer.cfg
       $CNK->__oStd->WriteConfig( 'mailer',    $aCfg['mailer']   );
       // main.cfg
       $CNK->__oStd->WriteConfig( 'main',      $aCfg['main']     );
       // member.cfg
       $CNK->__oStd->WriteConfig( 'phpforms',  $aCfg['phpforms'] );
       // session.cfg
       $CNK->__oStd->WriteConfig( 'session',   $aCfg['session']  );

       return true;
    }

    //   /w/devel/phpforms/filez  - for example
    function _CreateUploadDir( $sPath )
    {
       global $CNK;
       @clearstatcache();

       $sPath = preg_replace( "/\/$/", '', $sPath );
       $sPath = preg_replace( "/\\\\$/", '', $sPath );

       if ( !is_dir($sPath) ) @mkdir( $sPath, 777 );
       @chmod( $sPath, 0777 );

       if ( @function_exists('is_writeable') ) 
       {
          // primary check
          if ( ! @is_writeable( $sPath ) )
          {
             // forced check
             if ( !_ForceWriteableCheck( $sPath . DS ) )
             {
                $sError = preg_replace( "/<#DIR#>/", $sPath, ERROR_007);
                _ShowError( __LINE__ . ': '. $sError );
             }
          }
       }

       // Write .htaccess
       $sOutput = "Options -All -Multiviews\nOrder deny,allow\nDeny from all\n";
       if ( $mFile = @fopen( "$sPath/.htaccess", 'wb' ) )
       { 
          @fwrite( $mFile, $sOutput );
          @fclose( $mFile );
       }
    }

    function _UpdateIncludeFile( $sUrlBase ) // phpforms.php
    {
       global $CNK;

       @clearstatcache();
       if ( @function_exists('is_writeable') ) 
       {
          // primary check
          if ( ! @is_writeable( './phpforms.php' ) )
          {
             _ShowError( __LINE__.': '. preg_replace( "/<#FILE#>/", 'phpforms.php', ERROR_006) );
          }
       }

       $mFile   = @fopen( './phpforms.php', 'r' );
       $sOutput = @fread( $mFile, filesize('./phpforms.php') );
       @fclose( $mFile );

       $sOutput = preg_replace( "/(\\\$sUrl\s*=\s*\").*?\"/s", "\$1{$sUrlBase}\"", $sOutput );
       $mFile   = @fopen( './phpforms.php', 'wb' );
       @fwrite( $mFile, $sOutput );
       @fclose( $mFile );

       return true;
    }


    function _CreateTables()
    {
       global $CNK;

       $CNK->__aCfg = array_merge( $CNK->__aCfg, _LoadSqlConfig() );

       define( 'PHP_EXT', $CNK->__aCfg['PHP_EXT'] );

       // Try to connect to database, using new params ...
       require_once( INC . 'db.class' . PHP_EXT );

       $CNK->__oDb =& DB::Loader( $CNK->__aCfg["DATABASE_TYPE"] );

       $_bConnectionStatus = $CNK->__oDb->Connect( $CNK->__aCfg );

       if ( (bool)$_bConnectionStatus )
       {
          // we can create the database structure
          @require_once( $this->__aModVars["my_mod_path"].'mysql.dump.php' );

          for ( $i=0; $i < count($aSql); $i++ )
          {
             $CNK->__oDb->ExecQuery( $aSql[ $i ] );
          }
       }
    }

    function _ShowCreateAdmin()
    {
       global $CNK;

       $mRidAdm = $CNK->__oDisplay->LoadTpl( 'cr_admin' );
       $aTplAdm = array();

       $this->__aTplData["L_page_title"] = "Admin account";
       $this->__aTplData["info_header"] = 
       "3. Please create your administrator account...";

       $this->__aTplData["T_wrapper_content"] = 
       $CNK->__oDisplay->EvalTpl( $aTplAdm, '', $mRidAdm );
    }

    // 4. 
    // Create default admin
    function _CreateAdmin()
    {
       global $CNK;
       $CNK->__aCfg = _LoadConfig( CONF );

       $CNK->__aCfg = array_merge( $CNK->__aCfg, _LoadSqlConfig() );

       define( 'PHP_EXT', $CNK->__aCfg['PHP_EXT'] );
       require_once( INC . 'db.class' . PHP_EXT );
       $CNK->__oDb =& DB::Loader( $CNK->__aCfg["DATABASE_TYPE"] );

       $_bConnectionStatus = $CNK->__oDb->Connect( $CNK->__aCfg );
       if ( (bool)$_bConnectionStatus )
       {
           $sId    = ShortUniqueId();
           $sLogin = $CNK->__aIn["adm_login"];
           $sPass  = md5($CNK->__aIn["adm_pass"]);
           $sEmail = $CNK->__aIn["adm_email"];

           $CNK->__oDb->ExecQuery(
               "INSERT INTO pf_admins( adm_id, adm_login, adm_password ) 
                VALUES ( '{$sId}', '{$sLogin}', '{$sPass}' )"
           );

           // Read mailer.cfg config
           $aMailerCfg = array();
           $aData   = array();
           $sLine = '';
           $mFile = @fopen( CONF.'mailer.cfg', 'r' );
           while ( !@feof( $mFile ) )
           {
              $sLine = trim( @fgets( $mFile, 0xFFF ) );
              if ( $sLine != '' )
              {
                 $aData = explode( '=', $sLine, 2 );
                 $aMailerCfg[ $aData[0] ] = $aData[1];
              }
           }
           @fclose( $mFile );
           $aMailerCfg["MAILER_ADMIN"] = $sEmail;

           // Write new mailer.cfg
           $CNK->__oStd->WriteConfig( 'mailer', $aMailerCfg );
       }

       // Send notification email to the admin
       $sFile = "";
       $mFile = @fopen( $this->__aModVars["my_mod_mail"] . "inst_complete.mtpl", 'r' );
       if ( $mFile )
       {
          while ( !feof($mFile) ) 
            $sFile .= @fread( $mFile, 4096 );
       }

       $aMTpl = array();
       $aMTpl['a_login']     = $CNK->__aIn["adm_login"];
       $aMTpl['a_passwd']    = $CNK->__aIn["adm_pass"];
       $aMTpl['login_link']  = $CNK->__aCfg["URL_BASE"] . "admin.php";
                               
       $aMTpl['uninst_link'] = $CNK->__aCfg["URL_BASE"] . "install.php";
       $sEmail = $CNK->__aIn["adm_email"];

       $sFile = preg_replace( "/<#(.+?)#>/ies", "\$aMTpl['\\1']", $sFile );
       xmail( $sEmail, '', '', 'install@phpforms.net', 
              'phpForms script installation', $sFile, array(), array(), 0, '' );

       // Place install.lock - to finish installation
       // and to avoid error message 'Installation not found'
       $mFile = @fopen( $CNK->__aCfg["PATH_BASE"] . 'install.lock', 'w' );
       @fwrite( $mFile, time() );
       @fclose( $mFile );
       @chmod( $CNK->__aCfg["PATH_BASE"] . 'install.lock', 0666 );

       // Show congratulations
       $mRidAdm = $CNK->__oDisplay->LoadTpl( 'done' );
       $aTplAdm = array();

       $this->__aTplData["L_page_title"] = "Installation complete";
       $this->__aTplData["info_header"]  = "4. Installation complete";

       $this->__aTplData["T_wrapper_content"] = 
       $CNK->__oDisplay->EvalTpl( $aTplAdm, '', $mRidAdm );

    }

    //----------------------------------------------------//


    //----------------------------------------------------//
    // Autoconfiguration for module IDX                   //
    //----------------------------------------------------//
    Function _ThisAutoConfig()
    {
        $this->_ThisModStructure();
//        IDX::_ThisModConf('configure');   
    }

    //----------------------------------------------------//
    // Define this module structure                       //
    //----------------------------------------------------//    
    Function _ThisModStructure()
    {
        global $CNK;
        
        $this->__aModVars["my_mod_path"] = $CNK->__aModules[ $CNK->__sWorkingModule ];
        $this->__aModVars["my_mod_conf"] = $CNK->__aModules[ $CNK->__sWorkingModule ] . 'conf/';
        $this->__aModVars["my_mod_mail"] = $CNK->__aModules[ $CNK->__sWorkingModule ] . 'mail/';
        $this->__aModVars["my_mod_tpls"] = $CNK->__aModules[ $CNK->__sWorkingModule ] . 'display/';
        $this->__aModVars["my_mod_imgs"] = MODULES . $CNK->__sWorkingModule . '/display/i/';
    } 
      
}

    //----------------------------------------------------//
    // Recursively deletes a directory
    //
    function RemoveDir( $sFromDir )
    {
       $bRes    = false;
       $mHandle = 0;
       if ( !is_dir($sFromDir) ) return;
       if ( $mHandle = @opendir( $sFromDir ) )
       {
          while (false !== ($sFile = readdir($mHandle)) )
          {
             if ( $sFile=='.' || $sFile=='..' ) continue;
             if ( is_dir($sFromDir.'/'.$sFile) )
                RemoveDir( $sFromDir.'/'.$sFile );
             else // if not dir
                @unlink( $sFromDir.'/'.$sFile );
          }
          closedir( $mHandle );
       }

       if ( is_dir($sFromDir) )
       {
          $bRes = @rmdir( $sFromDir );
          if ( !$bRes )
          {
             @chmod( $sFromDir, 0777 );
             if ( !@rmdir( $sFromDir ) )
               _ShowError( __LINE__.": ". preg_replace( "/<#DIR#>/", $sFromDir, ERROR_DIR_PERMISSION_DENIED)  );
          }
          return true;
       } else return false;
    }

    /**
     * Checks for directory write permissions without 
     * native php functions
     */
    function _ForceWriteableCheck( $sPath )
    {
       $sFile = 'fwtest5432.dat';
       $hFile = '';

       if ( $hFile = @fopen( $sPath.$sFile, 'a' ) )
       {
          @fwrite( $hFile, time() );
          @fclose( $hFile );
       }

       @clearstatcache();

       if ( !@file_exists( $sPath.$sFile ) )
          return false;
       else
       {
          @unlink( $sPath.$sFile );
          return true;  
       }
    }   

    
    /**
     * Check for write permissions for directory
     */
    function IsDirWritable( $sDirPath, $bSilent=false )
    {
       $bRes = true;

       $sDirPath = realpath($sDirPath);

       @clearstatcache();
       if ( @function_exists('is_writeable') )
       {
          if ( !@is_writeable( $sDirPath ) )
          {
             if ( !_ForceWriteableCheck( $sDirPath.'/' ) )
             {
                if ( !$bSilent ) _ShowError( __LINE__.': '.preg_replace( "/<#DIR#>/", $sDirPath, ERROR_007) );
                else $bRes = false;
             }
          }
       }
       else
       {
          if ( !_ForceWriteableCheck( $sDirPath.'/' ) )
          {
             if ( !$bSilent ) _ShowError( __LINE__.': '.preg_replace( "/<#DIR#>/", $sDirPath, ERROR_007) );
             else $bRes = false;
          }
       }
       @clearstatcache();
       return $bRes;
    }


?>