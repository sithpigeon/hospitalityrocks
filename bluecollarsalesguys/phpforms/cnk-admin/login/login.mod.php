<?php
/*
+--------------------------------------------------------------------------
|
|   LOGIN MODULE
|   phpForms, form processing script
|   by Igor M. Belobrov,
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

$_OBJ = new LOGIN();

class LOGIN
{

    var $__sDisplayId;
    var $__aTplData = array();
    var $__sHtml;
    var $__aModVars = array();
    var $__aCfgBackUp = array();

    Function LOGIN()
    {
        global $CNK;

        /* {{{ Run autoconfigurator }}} */
        $this->_ThisAutoConfig();

        /* {{{ Prepare base url for template }}} */
        $this->__aTplData["base_url"] = $CNK->__sBaseUrl;
        $this->__aTplData["clean_url"] = preg_replace( "/(.*)\?.*/", "$1", $this->__aTplData["base_url"] );
        $this->__aTplData["img_path"] = $this->__aModVars["my_mod_imgs"];
        $this->__aTplData["SITE_NAME"] = $CNK->__aCfg["SITE_NAME"];

        /* {{{ Load language support and prepare for template }}} */
        $CNK->__oI18n->LoadSection();
        $this->__aTplData = array_merge( (array)$this->__aTplData, (array)$CNK->__aWords );

        switch ( $CNK->__aIn["code"] )
        {
            case '00':
                      $this->_LoginForm();
                      break;

            case '01':
                      $this->_doLogin();
                      break;

            case '02':
                      $this->_doLogOut();
                      break;
            default:
                      $this->_LoginForm();
        }

        /* {{{ Reset system configuration to default }}} */
        $this->_ThisModConf( 'recover' );
        /* {{{ Output html to browser }}} */
        echo $this->__sHtml;
    }

    //----------------------------------------------------//
    // Module LOGIN stuff                                 //
    //----------------------------------------------------//

    /* {{{ Show login form to user }}} */
    Function _LoginForm()
    {
        global $CNK;

        /* {{{ Eval css stylesheet template }}} */
        $this->__aTplData["cnk_style_sheet"] = $CNK->__oDisplay->EvalTpl( $this->__aTplData, 'style', '', false );

        $this->__aTplData['APP_NAME']        = $CNK->__aCfg['APP_NAME'];
        $this->__aTplData['APP_VERSION']     = $CNK->__aCfg['APP_VERSION'];
        $this->__aTplData['APP_VERSION_FIX'] = $CNK->__aCfg['APP_VERSION_FIX'];
        $this->__aTplData['APP_COPYRIGHT']   = $CNK->__aCfg['APP_COPYRIGHT'];

        /* {{{ Eval login form template }}} */
        $this->__aTplData["cnk_content"] = $CNK->__oDisplay->EvalTpl( $this->__aTplData, 'login_form', '', false );
        /* {{{ Eval wrapper }}} */
        $this->__sHtml = $CNK->__oDisplay->EvalTpl( $this->__aTplData, 'wrapper', '', false );

        return true;
    }

    /* {{{ Do login process }}} */
    Function _doLogin()
    {
        global $CNK;
        static $_sUrl = '';

        $sLogin = SetDefault( $CNK->__aIn['input_login'] );
        $sPass  = SetDefault( $CNK->__aIn['input_pass'] );

        $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_admins WHERE adm_login='{$sLogin}'" );
        if ( $aRes = $CNK->__oDb->Fetch($mQRid) )
        {
           if ( $aRes['adm_password'] == md5($sPass) )
           {
              $sCookie = $aRes['adm_password'] .'_'. $aRes['adm_id'];
              $CNK->__oStd->my_SetCookie( 'pwd', $sCookie, -1 );

              $_sUrl = $CNK->__sBaseUrl . '?act=idx';
              $this->_Redirect( $_sUrl, $CNK->__aWords['L_trans_u_signed_in'] );
           }
        }

        $mRidError = $CNK->__oDisplay->LoadTpl( 'error_login' );
        $aTplError = $CNK->__aWords;

        $this->__aTplData["default_login"] = $sLogin;
        $this->__aTplData["error_login"] =
                 $CNK->__oDisplay->EvalTpl( $aTplError, '', $mRidError );
        $this->_LoginForm();
        return true;
    }

    /* {{{ Do logout process }}} */
    Function _doLogOut()
    {
        global $CNK;
        $CNK->__oStd->my_SetCookie( 'pwd', '', -1 );

        $_sUrl = $CNK->__sBaseUrl . '?act=login';
        $this->_Redirect( $_sUrl, $CNK->__aWords['L_trans_u_signed_out'] );
        $this->_LoginForm();
        return true;
    }

    /* {{{ Redirect page }}} */
    Function _Redirect( $sUrl, $sText )
    {
        global $CNK;

        /* {{{ Reset system configuration to default }}} */
        $this->_ThisModConf( 'recover' );

        @flush();
        $this->__aTplData["redirect_url"] = $sUrl;
        /* {{{ Eval css stylesheet template }}} */
        $this->__aTplData["cnk_style_sheet"] = $CNK->__oDisplay->EvalTpl( $this->__aTplData, 'style', '', false );

        /* {{{ Replace URL in 'redirect ..' phrase from lang pack }}} */
        $aTmp = array();
        $aTmp['url'] = $sUrl;
        $sRedir = preg_replace( "/<#(.+?)#>/ies", "\$aTmp['\\1']", $CNK->__aWords['L_trans_auto_redir'] );

        /* {{{ Set cnk_content param }}} */
        $this->__aTplData["cnk_content"] = $sText . $sRedir;
        $CNK->__oDisplay->EvalTpl( $this->__aTplData, 'redirect', '', true );
        exit;
    }

    //----------------------------------------------------//

    //----------------------------------------------------//
    // Autoconfiguration for module IDX                   //
    //----------------------------------------------------//
    Function _ThisAutoConfig()
    {
        $this->_ThisModStructure();
        $this->_ThisModConf('configure');
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

    //----------------------------------------------------//
    // Define this module structure                       //
    // @param 'configure' for set params                  //
    // @param 'recover' for recover original sys-config   //
    //----------------------------------------------------//
    Function _ThisModConf( $sFlag='configure' )
    {
        global $CNK;

        if ( $sFlag == 'recover' )
        {
           $CNK->__aCfg = $this->__aCfgBackUp;
           $CNK->__aCfgBackUp = array();
        }
        else
        {
           $this->__aCfgBackUp = $CNK->__aCfg;
           $CNK->__aCfg = array_merge(
                            (array) $CNK->__aCfg,
                            (array) _LoadConfig($this->__aModVars["my_mod_conf"])
                          );
        }
    }

}

?>