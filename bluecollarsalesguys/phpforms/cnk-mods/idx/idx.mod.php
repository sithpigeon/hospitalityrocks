<?php

/*
+--------------------------------------------------------------------------
|
|   FORM GENERATION AND PROCESSING MODULE
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

$_OBJ1 = new IDX1();

class IDX1
{
    var $__sDisplayId;
    var $__aTplData = array();
    var $__sHtml;
    var $__aModVars = array();

    var $__sSubId = 0; // Submission record (group) id
    var $__sDate  = 0;

    Function IDX1()
    {
        global $CNK;

        /* {{{ Run autoconfigurator }}} */
        $this->_ThisAutoConfig();

        /* {{{ Prepare base url for template }}} */
        $this->__aTplData["base_url"] = $CNK->__sBaseUrl;
        $this->__aTplData["clean_url"] = preg_replace( "/(.*)\?.*/", "$1", $CNK->__sBaseUrl );
        $this->__aTplData["clean_url"] = preg_replace( "/admin\.php.*/", "index.php", $CNK->__sBaseUrl );
        $this->__aTplData["img_path"] = $this->__aModVars["my_mod_imgs"];
        $this->__aTplData["style_path"] = $this->__aModVars["my_mod_imgs"];
        $this->__aTplData["SITE_NAME"]   = $CNK->__aCfg["SITE_NAME"];

        /* {{{ Load language support and prepare for template }}} */
        $CNK->__oI18n->LoadSection();
        $this->__aTplData = array_merge( $this->__aTplData, $CNK->__aWords );

        // Local config
        $this->__aVars["FORM_WIDTH"] = 600;
        $this->__aVars["FORM_COLOR"] = 'efefef';
        $this->__aVars["FIELD_COLOR"] = 'cdcdcd';
        $this->__aVars["DEFAULT_LAYOUT"] = 'ordinary';
        $this->__aVars["DEFAULT_ROWS"]   = 5;
        $this->__aVars["DEFAULT_CTRL_STYLE"] = "width:100%;";
        $this->__aVars["EXTRA_CTRL_STYLE"]   = "";

        $this->__aVars["DEFAULT_YEARS"] = "100";
        $this->__aVars["MAX_FILE_NAME_LENGTH"] = "50"; // of uploaded file


        $this->__aTmp      = array();
        $this->__aChkFuncs = array();

        $sCode = SetDefault( $CNK->__aIn['code'] );
        $bIsDemo  = 0;
	$bIsEmbed = 0;

        switch( $sCode )
        {
           /* {{{ Form submit}}}*/
           case '01':
                      $aFormData = SetDefault( $CNK->__aIn, array() );
                      $bIsDemo = SetDefault( $CNK->__aIn['demo'], 0 );
                      $this->_PreProcessFormData( $aFormData, $bIsDemo );
                      break;

           // Show form
           default:
                      $sFormId  = SetDefault( $CNK->__aIn['fid'] );
                      $bIsDemo  = SetDefault( $CNK->__aIn['demo'], 0 );
                      $bIsEmbed = SetDefault( $CNK->__aIn['embed'], 0 );
                      $this->_ShowForm( $sFormId, $bIsDemo, $bIsEmbed );
                      break;
        }

        /* {{{ Eval template }}} */
        if ( $bIsDemo )
        {
           $this->__aTplData["SITE_NAME"]     = $CNK->__aCfg['SITE_NAME'];
           $this->__aTplData["L_page_title"]  = $CNK->__aWords['L_frmview_demo'];
           $this->__aTplData["form_note"]     = '<span style="font-size:12px">' . $CNK->__aWords['L_frmview_demo_note'] . '</span>';
           $this->__aTplData["body_js"]       = "onContextMenu='return false;'";
        }
        else
        {
           $this->__aTplData["L_page_title"]  = $CNK->__aWords['L_page_frmfill'];
           $this->__aTplData["form_note"]     = '';
        }

        if ( $this->__aTplData['form'] == '' )
           $this->__aTplData["L_page_title"] = $CNK->__aWords['L_page_no_frm'];

        // Check if the form is embedded (into client's page)
        if ( $bIsEmbed && $this->__aTplData['form'] != '' )
	  $this->__sHtml = $this->__aTplData['form'];
        else
          $this->__sHtml = $CNK->__oDisplay->EvalTpl( $this->__aTplData, 'wrapper', '', false );

        /* {{{ Reset system configuration to default }}} */
        $this->_ThisModConf( 'recover' );

        /* {{{ Output html to browser }}} */
        echo $this->__sHtml;
    }

    //////////////////////////////////////////////////////////////////////
    //                                                                  //
    //                 Form submissions processing BEGIN                //
    //                                                                  //
    //////////////////////////////////////////////////////////////////////

    // Form submission preprocessing
    function _PreProcessFormData( $aData, $bIsDemo )
    {
       global $CNK;

       $sFrmId = SetDefault( $aData['fid'] );
       $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_forms WHERE frm_id='{$sFrmId}'" );
       if ( $aRes = $CNK->__oDb->Fetch($mQRid) )
       {
          $this->__sDate  = date("Y-m-d H:i:s");
          $this->__sSubId = ShortUniqueId();
          $aRes1 = array();
          if ( $aRes['frm_mtpl_id']!='' )
          {
             $mQRid1 = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_mail_tpls WHERE mtpl_id='{$aRes['frm_mtpl_id']}'" );
             $aRes1 = $CNK->__oDb->Fetch($mQRid1);
          }

          $this->_DoFormAction( $bIsDemo, $aData, $aRes1, $aRes );
       }
       else
       {
          echo "<center> <font color='#FF0000'><br>" . $CNK->__aWords['L_phr_form_not_found'] .
               "</font> </center>";
       }
       die;
    }

    //////////////////////////////////////////////////////////////////////
    //
    // Save data into DeBe and/or Mail
    //
    // @$bIsDemo  - boolean: demo indicator
    // @$aData    - array: data submitted
    // @$aTplNfo  - array: mail template info
    // @$aFrmNfo  - array: form info
    //

    function _DoFormAction( $bIsDemo, $aData, $aMailTplNfo, $aFrmNfo )
    {
       global $CNK;
       $sRecId = ShortUniqueId();
       $sMtpl = '';

       $sFrmName = $CNK->__oStd->TxtUnSafeOutput( $aFrmNfo['frm_name'] );
       $sFrmId = $aData['fid'];

       // Mail templates array
       /* $aMailTpls    = array(
           '0' => array( 'id', 'tpl', 'from', array('to'), 'subj' ), ...  );*/
       $aMailTpls = array();
       $aDBFields = array();
       $aReady    = array();
       $aExtraNfo = array(); // Reserved. For new template tags info

       if ( !$bIsDemo )
       {
          $this->__sSubRecId = ShortUniqueId(); // Submission record (group) id

          $aMailTpls  = $this->_GetAdminEmails( $aMailTpls, $aMailTplNfo, $aFrmNfo );
          $aDBFields  = $this->_GetFormFieldsFromDB( $sFrmId );
          $aFrmFields = $this->_GetFormDataSubmitted( $aData );
          $aMailTpls  = $this->_GetUserFormEmails( $aMailTpls, $aFrmFields, $aDBFields );
          $aReady     = $this->_SaveUploadedFiles( $aReady, $aDBFields );
           // Custom field types processing
           // begin
                 $aReady = $this->_NormalizeDates( $aReady, $aDBFields, $aFrmFields );
           // end
          $aReady     = $this->_NormalizeSubmittedData( $aReady, $aDBFields, $aFrmFields );
          $aMailTpls  = $this->_InsertUserDataIntoEmails( $aMailTpls, $aReady, $aFrmNfo, $aExtraNfo );
          if ( $aFrmNfo['frm_dest']==1 || $aFrmNfo['frm_dest']==2 )
             $this->_SaveSubmitToDB( $aReady, $aFrmNfo );

          $this->_SendEmailsBulk( $aMailTpls );

       }

       // After submission alert and redirect ---
       $mRidAfter = $CNK->__oDisplay->LoadTpl( 'after_sub' );
       $aTplAfter = array();

       if ( $aFrmNfo['frm_redirect']!='' )
       {
          $sRedirectURL = trim($aFrmNfo['frm_redirect']);
          $sRedirectURL = str_replace( '&amp;', '&', $sRedirectURL );
          if ( !preg_match( "/^http:\/\//i", $sRedirectURL ) )
            if ( !preg_match( "/^https:\/\//i", $sRedirectURL ) )
              $sRedirectURL = 'http://' . $sRedirectURL;
          $aTplAfter['url_redirect'] = $sRedirectURL;
       }
       else $aTplAfter['url_redirect'] = $CNK->__aSys['SERVER']['HTTP_REFERER'];

       $aTplAfter['alert_text'] = '';

       $aFrmNfo['frm_after_sub_txt'] = addslashes($aFrmNfo['frm_after_sub_txt']);
       $aFrmNfo['frm_after_sub_txt'] = str_replace( "<br>", '\n', $aFrmNfo['frm_after_sub_txt'] );

       $aFrmNfo['frm_after_sub_txt'] = $CNK->__oStd->TxtUnSafeOutput( $aFrmNfo['frm_after_sub_txt'] );
       $aFrmNfo['frm_after_sub_txt'] = str_replace( "\r"  , ''  , $aFrmNfo['frm_after_sub_txt'] );
       $aFrmNfo['frm_after_sub_txt'] = str_replace( "\n"  , '\n', $aFrmNfo['frm_after_sub_txt'] );

       $aFrmNfo['frm_after_sub_txt'] = str_replace( "'"  , "\'"  , $aFrmNfo['frm_after_sub_txt'] );
       $aFrmNfo['frm_after_sub_txt'] = str_replace( '"'  , '\"', $aFrmNfo['frm_after_sub_txt'] );

       if ( $aFrmNfo['frm_after_sub_txt']!='' )
          $aTplAfter['alert_text'] = "alert('{$aFrmNfo['frm_after_sub_txt']}');";

       $CNK->__oDisplay->EvalTpl( $aTplAfter, '', $mRidAfter, 1 );
    }

    ////////////////////////////////////////////////////////////////////////
    //
    // Retrieve admin emails and template
    //
    function _GetAdminEmails( $aMailTpls, $aMailTplNfo, $aFrmNfo )
    {
       global $CNK;
       // Mail templates array
       /* $aMailTpls    = array(
          '0' => array( 'id', 'tpl', 'from', array('to'), 'subj' ), ...
       );*/

       // If form is mailing to admin
       if ( count($aMailTplNfo) > 0 && $aFrmNfo['frm_dest']!='1' )
       {
          $sMailTo   = $CNK->__oStd->TxtUnSafeOutput( $aFrmNfo['frm_email']     );
          $sMailFrom = $CNK->__oStd->TxtUnSafeOutput( $aMailTplNfo['mtpl_from'] );
          $sSubj     = $CNK->__oStd->TxtUnSafeOutput( $aMailTplNfo['mtpl_subj'] );
          $sMtpl     = doCorrectMtpl( $CNK->__oStd->TxtUnSafeOutput( $aMailTplNfo['mtpl_tpl']  ) );

          $aMailTo = explode( ',', $sMailTo );

          $aMailTpls[] = array( '', $sMtpl, $sMailFrom, $aMailTo, $sSubj );
       }
       return $aMailTpls;
    }

    //

    function _GetFormFieldsFromDB( $sFrmId )
    {
       global $CNK;

       $aQRes = array();

       // Get all fields of form with ID from db
       $mQRid = $CNK->__oDb->ExecQuery(
        "SELECT *
         FROM  pf_pages INNER JOIN
               pf_fields ON (pg_id=fld_pg_id) INNER JOIN
               pf_types ON (fld_type_id=type_id)
         WHERE pg_frm_id='{$sFrmId}'"
       );

       while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
         $aQRes[] = $aRes;

       return $aQRes;
    }

    ////////////////////////////////////////////////////////////////////////
    //
    // Just select form fields and cut fld_ prefix
    //

    function _GetFormDataSubmitted( $aData )
    {
       global $CNK;

       $aFrmFields = array();

       foreach ( $aData as $k=>$v )
         if ( preg_match( '/^fld_/', $k ) )
         {
            $sFldId = preg_replace( '/^fld_/', '', $k );
            $aFrmFields[$sFldId] = $v;
         }

       return $aFrmFields;
    }


    ////////////////////////////////////////////////////////////////////////
    //
    // "Email fields" processing
    //

    function _GetUserFormEmails( $aMailTpls, $aFrmFields, $aDBFields )
    {
       global $CNK;

       foreach ( $aDBFields as $aField )
       {
          $sFldId  = $aField['fld_id'];
          $sMtplId = $aField['fld_mtpl_id'];

          if ( $sMtplId!='' && isset($aFrmFields[$sFldId]) )
          {
             $mQRid = $CNK->__oDb->ExecQuery(
              "SELECT * FROM pf_mail_tpls WHERE mtpl_id='{$sMtplId}'" );

             if ( $CNK->__oDb->NumRows($mQRid) > 0 )
             {
                $aRes = $CNK->__oDb->Fetch($mQRid);
                $sFrom = $CNK->__oStd->TxtUnSafeOutput( $aRes['mtpl_from'] );
                $sSubj = $CNK->__oStd->TxtUnSafeOutput( $aRes['mtpl_subj'] );
                $sMtpl = doCorrectMtpl( $CNK->__oStd->TxtUnSafeOutput( $aRes['mtpl_tpl']  ) );
                $aMailTo = $this->_ExtractFieldEmails( $aFrmFields[$sFldId] );

                $aMailTpls[] = array( $sFldId, $sMtpl, $sFrom, $aMailTo, $sSubj );
             }
          }
       }

       return $aMailTpls;
    }

    ////////////////////////////////////////////////////////////////////////
    //
    // Get emails from field
    //

    function _ExtractFieldEmails( $mField )
    {
       $aResult = array();
       if ( is_array($mField) ) $aResult = $mField;
       else $aResult[] = $mField;

       return $aResult;
    }

    ////////////////////////////////////////////////////////////////////////
    //
    // Save uploaded files and return file names as "ready" data
    //

    function _SaveUploadedFiles( $aReady, $aDBFields )
    {
       global $CNK;

       $aRes = array();
       $aFNP = array();

       foreach ( $aDBFields as $aField )
        if ( preg_match( '/U/', $aField['type_props'] ) )
        {
           // walk files array
           foreach ( $CNK->__aSys["FILES"] as $k=>$vaFile )
           {
              $sFldId = preg_replace( '/^fld_/', '', $k );
              if ( $sFldId == $aField['fld_id'] )
              {
                 $sFileId    = ShortUniqueId();

                 $sFileName  = $vaFile['name'];

                 // Check tooo long file names
                 $aFNP = GetFileNameParts( $sFileName );
                 if ( strlen($aFNP['name'])>$this->__aVars["MAX_FILE_NAME_LENGTH"] )
                 {
                    $sFileName = substr($aFNP['name'], 0, $this->__aVars["MAX_FILE_NAME_LENGTH"] );
                    $sFileName .= ( $aFNP['ext']!='' ) ? ".{$aFNP['ext']}" : '';
                 }

                 $sNewFileName = $CNK->__aCfg['UPLOADED_FILES_PATH'] .
                                 $sFileId .'.'. $sFileName . '.~';
                 $sUrl = $CNK->__aCfg['URL_BASE'].
                         "admin.php?code=07&file_id=" . $sFileId;

                 if ( is_uploaded_file( $vaFile['tmp_name']) )
                 {
                    if ( !move_uploaded_file( $vaFile['tmp_name'], $sNewFileName ) )
                    {
                      $CNK->__oLs->CopyMoveFile( $vaFile['tmp_name'], $sNewFileName, "copy" );
                    }
                 }
                 else
                 {
                    $sUrl = '';
                 }

                 // new[id] = array( 'sub_fld_num', 'sub_fld_name', 'sub_fld_value', array('sub_filename','file_url') )
                 $nNum = 100000 * $aField['pg_num'] + $aField['fld_num'];

                 $aReady[$sFldId] = array(
                   $nNum, $aField['fld_name'], $sFileName,
                   array( $sNewFileName, $sUrl, $sFileId ), 2
                 );
              }
           }
        }

       return $aReady;
    }


    function _NormalizeDates( $aReady, $aDBFields, $aFrmFields )
    {
       global $CNK;

       $aDates = array();
       foreach ( $aDBFields as $aField )
        if ( preg_match( '/d/', $aField['type_props'] ) )
        {
           $sDateFmt = $this->_GetDateFormatString( $aField['fld_id'] );

           $aDate    = $this->_ExtractDate( $aFrmFields, $aField['fld_id'] );
           $sDate    = $this->_GetDateString( $sDateFmt, $aDate );

           // new[id] = array( 'sub_fld_num', 'sub_fld_name', 'sub_fld_value', array('sub_filename','file_url') )
           $nNum = 100000 * $aField['pg_num'] + $aField['fld_num'];

           $aReady[$aField['fld_id']] = array(
             $nNum, $aField['fld_name'], $sDate, array(), 0
           );
        }

       return $aReady;
    }

    //////////////////////////////////////////////////////////////////////
    //
    // Returns date string format: DmY, MdY...
    //
    // @$sFldId - string: field id
    //

    function _GetDateFormatString( $sFldId )
    {
       global $CNK;
       $sResult = '';

       $mQRid1 = $CNK->__oDb->ExecQuery( "SELECT it_value FROM pf_items WHERE it_fld_id='{$sFldId}' AND it_title='order'" );
       if ( $aRes = $CNK->__oDb->Fetch($mQRid1) )
       {
          for ( $i=0; $i<strlen($aRes['it_value']); $i++ )
          {
             $cUChar = strtoupper( $aRes['it_value'][$i] );
             if ( $cUChar == $aRes['it_value'][$i] )
             {
                $sSpace = ( $sResult=='' ) ? '' : ' ';
                $sResult .= $sSpace . $cUChar;
             }
          }
       }

       return $sResult;
    }


    //////////////////////////////////////////////////////////////////////
    //
    // Get d, m, y parts for field with certain id
    //
    // @$aFrmFields - array: submitted data
    // @$sFldId     - string: field id
    //

    function _ExtractDate( $aFrmFields, $sFldId )
    {
       $aResult = array();

       foreach ( $aFrmFields as $k=>$v )
          if ( preg_match( "/^[dmy]_/i", $k ) )
          {
             $sFldIdCut = preg_replace( "/^[dmy]_/i", '', $k );
             if ( $sFldIdCut == $sFldId)
             {
                $cDChar           = strtoupper( $k[0] );
                $aResult[$cDChar] = $v;
             }
          }

       return $aResult;
    }


    //////////////////////////////////////////////////////////////////////
    //
    // Formats date string according to format string
    //
    // @$sDateFmt  - string: date format
    // @$aDate     - string: date array
    //

    function _GetDateString( $sDateFmt, $aDate )
    {
       $aDate[' '] = ' ';
       $sResult = preg_replace( "/(.+?)/ies", "\$aDate['\\1']", $sDateFmt );
       return $sResult;
    }


    function _NormalizeSubmittedData( $aReady, $aDBFields, $aFrmFields )
    {
       foreach ( $aDBFields as $nIndex=>$aField )
       {
          $sTextVal = ''; $nFmt = 0;

          if (    $aField['fld_type_id'] != '07'   // File upload ( already set )
               && $aField['fld_type_id'] != '09'   // HTML ( no need )
               && $aField['fld_type_id'] != '10' ) // Date ( already set )
          {
             foreach ( $aFrmFields as $k=>$v )
               if ( $aField['fld_id']==$k )
               {
                  if ( is_array($v) )
                  {
                     $sTextVal = implode( "\n", $v ); $nFmt = 1;
                  }
                  else $sTextVal = $v;
               }

             // new[id] = array( 'sub_fld_num', 'sub_fld_name', 'sub_fld_value', array('sub_filename','file_url') )
             $nNum = 100000 * $aField['pg_num'] + $aField['fld_num'];

             $aReady[$aField['fld_id']] = array(
               $nNum, $aField['fld_name'], $sTextVal, array(), $nFmt
             );
          }
       }
       return $aReady;
    }

    function _FixEmailData( $aReady )
    {
       global $CNK;

       foreach ( $aReady as $k=>$v )
       {
          $v[2] = $CNK->__oStd->_FixEmailMsgBug( $v[2] );
          $aReady[$k] = $v;
       }
       return $aReady;
    }

    function _InsertUserDataIntoEmails( $aMailTpls, $aReady, $aFrmNfo, $aExtraNfo )
    {
       global $CNK;
       // $aExtraNfo - reserved
       $sFrmId    = $aFrmNfo['frm_id'];
       $aReady = $this->_FixEmailData( $aReady );
       $aFrmNfo['frm_name'] = $CNK->__oStd->_FixEmailMsgBug( $aFrmNfo['frm_name'] );
       $sFormData = $this->_CollectAllFormData( $aReady );
       $sFldVal   = '';

       for ( $i=0; $i<count($aMailTpls); $i++ )
       {
          $aMailTpls[$i][1] = str_replace( "[ip-address]", $CNK->__aSys['SERVER']['REMOTE_ADDR'], $aMailTpls[$i][1] );
          $aMailTpls[$i][1] = str_replace( "[user-agent]", $CNK->__aSys['SERVER']['HTTP_USER_AGENT'], $aMailTpls[$i][1] );
          $aMailTpls[$i][1] = str_replace( "[form-name]", $aFrmNfo['frm_name'], $aMailTpls[$i][1] );
          $aMailTpls[$i][1] = str_replace( '[form-data]', $sFormData, $aMailTpls[$i][1] );

          foreach ( $aReady as $k=>$v )
          {
             if ( $v[4] == 2 ) $sFldVal = $v[3][1]; // if file upload
             else $sFldVal = $v[2];

             // Message
             $aMailTpls[$i][1] = str_replace( "[field-name#{$sFrmId}.{$k}#]", $v[1]   , $aMailTpls[$i][1] );
             $aMailTpls[$i][1] = str_replace( "[field-data#{$sFrmId}.{$k}#]", $sFldVal, $aMailTpls[$i][1] );

             // From field
             $aMailTpls[$i][2] = str_replace( "[field-name#{$sFrmId}.{$k}#]", $v[1]   , $aMailTpls[$i][2] );
             $aMailTpls[$i][2] = str_replace( "[field-data#{$sFrmId}.{$k}#]", $sFldVal, $aMailTpls[$i][2] );

             // Subject field
             $aMailTpls[$i][4] = str_replace( "[field-name#{$sFrmId}.{$k}#]", $v[1]   , $aMailTpls[$i][4] );
             $aMailTpls[$i][4] = str_replace( "[field-data#{$sFrmId}.{$k}#]", $sFldVal, $aMailTpls[$i][4] );
          }
          // Clean unused
          $aMailTpls[$i][1] = preg_replace( "/(\[field-name#.*?#\])/", '', $aMailTpls[$i][1] );
          $aMailTpls[$i][1] = preg_replace( "/(\[field-data#.*?#\])/", '', $aMailTpls[$i][1] );
       }

       return $aMailTpls;
    }


    function _CollectAllFormData( $aReady )
    {
       global $CNK;
       $sResult = '';
       $aTemp   = array(); // sorted Ready array.
                           // 'name','data','file_data','fmt','id'

       foreach( $aReady as $k=>$v )
          $aTemp[$v[0]] = array( $v[1], $v[2], $v[3], $v[4] );
       ksort( $aTemp );

       foreach( $aTemp as $k=>$v )
       {
          switch ( $v[3] ) // format switch
          {
           // Plain text
           case 0:
                   $sResult .= $v[0] . " :: " . $v[1] . "\r\n";
                   break;

           // Array
           case 1:
                   $sResult .= $v[0] . " :: \r\n" . $v[1] . "\r\n";
                   break;

           // File
           case 2:
                   $sResult .= $v[0] . " :: " . $v[2][1] . "\r\n";
                   break;
          }
          $sResult .= "\r\n";
       }
       return $sResult;
    }

    //////////////////////////////////////////////////////////////////////
    //
    // Save submitted form data in DB submissions table
    //
    // @$aReady  - array: transformed form data
    // @$aFrmNfo - array: form information ( name, id and other )
    //

    function _SaveSubmitToDB( $aReady, $aFrmNfo )
    {
       global $CNK;

       $sFrmId   = $aFrmNfo['frm_id'];
       $sFrmName = addslashes( $CNK->__oStd->TxtUnSafeOutput( $aFrmNfo['frm_name'] ) );
       $sRecId   = $this->__sSubId;
       $sDate    = $this->__sDate;

       foreach( $aReady as $k=>$v )
       {
          $sSubId    = ShortUniqueId();
          $nFldNum   = $v[0];
          $sFldName  = addslashes( $v[1] );
          $sFldVal   = $v[2];
          if ( isset( $v[3][0]) )
          {
             $sFileName = $v[3][0]; $sSubId = $v[3][2];
          }
          else $sFileName = '';

          $CNK->__oDb->ExecQuery(
           "INSERT INTO pf_submissions ( sub_id,      sub_frm_id,  sub_frm_name,  sub_rec_id,  sub_fld_num,  sub_fld_name, sub_fld_value, sub_filename,      sub_date)
                                VALUES ( '{$sSubId}', '{$sFrmId}', '{$sFrmName}', '{$sRecId}', '{$nFldNum}', '{$sFldName}', '{$sFldVal}', '{$sFileName}', '{$sDate}' )" );
       }
    }

    //////////////////////////////////////////////////////////////////////
    //
    // Send emails bulk
    //
    // @$aMailTpls  - array: full emails info
    //

    function _SendEmailsBulk( $aMailTpls )
    {
       for ( $i=0; $i<count($aMailTpls); $i++ )
       {
          $sMtpl     = $aMailTpls[$i][1];
          $sMailFrom = $aMailTpls[$i][2];
          $sSubj     = $aMailTpls[$i][4];

          for ( $j=0; $j<count($aMailTpls[$i][3]); $j++ )
          {
             $sMailTo = trim( $aMailTpls[$i][3][$j] );
             if ( isCorrectEmail( $sMailTo ) )
               // Ok, let's spam :)
               xmail( $sMailTo, '', '', $sMailFrom, $sSubj, $sMtpl,
                      array(), array(), 0, array()                  );
          }
       }
    }

    //////////////////////////////////////////////////////////////////////
    //                                                                  //
    //                  Form submissions processing END                 //
    //------------------------------------------------------------------//
    //------------------------------------------------------------------//
    //                     Form rendering part BEGIN                    //
    //                                                                  //
    //////////////////////////////////////////////////////////////////////

    // Form rendering function
    function _ShowForm( $sFormId, $bIsDemo, $bIsEmbed )
    {
       global $CNK;

       $mRidForm   = $CNK->__oDisplay->LoadTpl( 'form' );
       $mRidPage   = $CNK->__oDisplay->LoadTpl( 'page' );
       $mRidPgTitl = $CNK->__oDisplay->LoadTpl( 'pg_title' );
       $mRidBtn    = $CNK->__oDisplay->LoadTpl( 'pg_button' );
       $mRidBotTxt = $CNK->__oDisplay->LoadTpl( 'pg_bottom_txt' );
       $mRidTopTxt = $CNK->__oDisplay->LoadTpl( 'pg_top_txt' );
       $mRidPgChk  = $CNK->__oDisplay->LoadTpl( 'page_check_js' );

       $sPF = '';
       $aTplForm   = array();
       $aTplPage   = array();
       $aTplPgTitl = array();
       $aTplFCtrl  = array();
       $aTplField  = array();
       $aTplTopTxt = array(); // for top text
       $aTplBotTxt = array(); // for bottom text
       $aTplPgChk  = array(); // fields checks for page
       $aPages     = array();

       // Get form data
       $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_forms WHERE frm_id='{$sFormId}'" );
       if ( $aRes = $CNK->__oDb->Fetch($mQRid) )
       {
          $aTplForm['form_name'] = "frm_" . $aRes['frm_id'];
          $aTplForm['form_url']  = $this->__aTplData["clean_url"];

          $nFormWidth = ( $aRes['frm_width']!=0 ) ? $aRes['frm_width'] : $this->__aVars["FORM_WIDTH"];

          $aTplForm['hidden_form_data'] =
             "<input type=\"hidden\" name=\"act\" value=\"idx\">\n" .
             "<input type=\"hidden\" name=\"code\" value=\"01\">\n" .
             "<input type=\"hidden\" name=\"fid\" value=\"{$sFormId}\">";
          if ( $bIsDemo!=0 ) $aTplForm['hidden_form_data'] .=
             "\n<input type=\"hidden\" name=\"demo\" value=\"1\">";


          if ( $aRes['frm_maxfilesize']<>0 )
             $aTplForm['max_file_size'] = "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"{$aRes['frm_maxfilesize']}\">\n";

          // Get pages
          $mQRid1 = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_pages WHERE pg_frm_id='{$sFormId}' ORDER BY pg_num ASC" );
          $nPagesNum = $CNK->__oDb->NumRows($mQRid1);
          $nCurPage = 0;
          $aTplPgChk['form_id'] = 'frm_'.$aRes['frm_id'];

          while ( $aRes1 = $CNK->__oDb->Fetch($mQRid1) )
          {
             $nCurPage++;
             $aPages[] = $aRes1['pg_id'];
             $aTplPage['page_id'] = 'pg_'.$aRes1['pg_id'];

             $aTplPgChk['checks']  = '';
             $aTplPgChk['page_id'] = $aRes1['pg_id'];
             $aTplPgChk['to_hide'] = $nCurPage-1;
             $aTplPgChk['to_show'] = $nCurPage;

             $aTplPgChk['last_page'] = ( $nPagesNum==$nCurPage ) ? 1 : 0;

///////             $aTplPage['check_page_js'] = '';

             if ( $nCurPage>1 ) $aTplPage['page_display'] = 'none';

             // Page title
             $aTplPgTitl['page_title'] = $aRes1['pg_title'];
             if ( $aTplPgTitl['page_title']!='' )
                $aTplPage['page_title'] = $CNK->__oDisplay->EvalTpl( $aTplPgTitl, '', $mRidPgTitl );

             // Top text
             $aTplTopTxt['top_text'] = $aRes1['pg_top_text'];
             if ( $aTplTopTxt['top_text']!='' )
                $aTplPage['top_text'] = $CNK->__oDisplay->EvalTpl( $aTplTopTxt, '', $mRidTopTxt );

             // Bottom text
             $aTplBotTxt['bottom_text'] = $aRes1['pg_bottom_text'];
             if ( $aTplBotTxt['bottom_text']!='' )
                $aTplPage['bottom_text'] = $CNK->__oDisplay->EvalTpl( $aTplBotTxt, '', $mRidBotTxt );

             $aTplPage['page_color']  = $this->_GetColor( $aRes1['pg_color'], $aRes['frm_color'], $this->__aVars["FORM_COLOR"] );

             $aTplPage['page_width'] = ( $aRes1['pg_width']!='0' ) ? $aRes1['pg_width'] : $nFormWidth;
             $aTplPage['buttons'] = $this->_GetPageButtons( $mRidBtn, $nCurPage, $nPagesNum, $aRes, $aRes1 );
             $aTplPage['page_fields'] = '';
             $sPageId = $aRes1['pg_id'];

             // Get fields query
             $mQRid2 = $CNK->__oDb->ExecQuery(
              "SELECT F.*, T.*
               FROM pf_fields F RIGHT JOIN pf_types T ON fld_type_id=type_id
               WHERE fld_pg_id='{$sPageId}' ORDER BY fld_num ASC" );

             // Get fields loop
             while ( $aRes2 = $CNK->__oDb->Fetch($mQRid2) )
             {
                $mQRid3 = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_layouts WHERE layout_id='{$aRes2['fld_layout_id']}'" );
                if ( $aRes3 = $CNK->__oDb->Fetch($mQRid3) )
                 $sLayoutFile = $aRes3['layout_file_name'];
                else $sLayoutFile = $this->__aVars["DEFAULT_LAYOUT"];

                // Size and MaxLength
                $aTplFCtrl['str_size']   = $this->_GetStrSize( $aRes2 );
                $aTplFCtrl['max_length'] = $this->_GetStrMaxLength( $aRes2 );

                $aTplFCtrl['name']  = 'fld_'.$aRes2['fld_id'];
                if ( !preg_match('/S/', $aRes2['type_props']) )
                {
                   if ( $aTplFCtrl['str_size'] == '' && $aRes2['fld_control_style']=='' )
                     $aTplFCtrl['style'] = $this->__aVars["DEFAULT_CTRL_STYLE"];
                   else $aTplFCtrl['style'] = $CNK->__oStd->TxtUnSafeOutput($aRes2['fld_control_style']);
                }
                else
                {
                   $aTplFCtrl['style'] = ( $aRes2['fld_control_style']=='' )
                                         ? $this->__aVars["EXTRA_CTRL_STYLE"]
                                         : $CNK->__oStd->TxtUnSafeOutput($aRes2['fld_control_style']);
                }

                // Rows
                if ( preg_match('/W/', $aRes2['type_props']) )
                  $aTplFCtrl['rows'] = $this->_GetRowsNumber( $aRes2 );

                $aTplFCtrl['default']        = $this->_GetDefaultSection( $aRes2 );
                $aTplFCtrl['options_select'] = $this->_GetSelectOptions( $aRes2 );

                $aTplField['field_color']    = $this->_GetColor( $aRes2['fld_color'], '', $this->__aVars["FIELD_COLOR"] );

                // Field Type Control
                $sTplPath = $CNK->__aCfg['PATH_BASE'] . $CNK->__aCfg['TYPES_TPL_PATH'] . $aRes2['type_id'];

                $aTplField['caption_style'] = $CNK->__oStd->TxtUnSafeOutput( $aRes2['fld_caption_style'] );
                $aTplField['field_caption'] = $aRes2['fld_name'];

                // Is the field "requred"
                $aTplField['required'] = ( $aRes2['fld_required']!=0 ) ? "&nbsp;<font color='red'>*</font>": '';

                // Check if Checkbox ou Radiobutton // Date
                if ( preg_match("/B/", $aRes2['type_props']) )
                  $aTplField['field_control'] = $this->_GetCheckRadio( $aRes2, $sTplPath );
                elseif ( preg_match("/d/", $aRes2['type_props']) )
                  $aTplField['field_control'] = $this->_GetDateCtrl( $aRes2, $sTplPath );
                else $aTplField['field_control'] = $CNK->__oDisplay->LoadEvalTplFile( $aTplFCtrl, $sTplPath );

                $aTplPgChk['checks'] .= $this->_FieldCheck( $aRes2 );

                // Field View
                if ( preg_match("/H/", $aRes2['type_props']) ) // HTML
                {
                   $aTplPage['page_fields'] .=
                     "<tr><td colspan=\"2\" bgcolor=\"{$aTplField['field_color']}\">\n" .
                       $aTplFCtrl['default'] .
                     "\n</td></tr>\n";
                }
                elseif ( !preg_match("/\^/", $aRes2['type_props']) ) // Other
                {
                   $sLayPath = $CNK->__aCfg['PATH_BASE'] . $CNK->__aCfg['LAYOUT_TPL_PATH'] . $sLayoutFile;
                   $aTplPage['page_fields'] .= $CNK->__oDisplay->LoadEvalTplFile( $aTplField, $sLayPath );
                }
                else // Do not show at all
                   $aTplPage['page_fields'] .= $aTplField['field_control'];
             }

             $aTplPage['check_page_js'] = $CNK->__oDisplay->EvalTpl( $aTplPgChk, '', $mRidPgChk );

             $sPF .= $CNK->__oDisplay->EvalTpl( $aTplPage, '', $mRidPage );
          } // Next page
          if ( count($aPages)>0 )
           $aTplForm['pages_list'] = '\'pg_'. implode( "','pg_", $aPages ) . '\'';

          $aTplForm['form_data'] = $sPF;

          $aTplForm['js_funcs'] = '';
          foreach ( $this->__aChkFuncs as $kFile=>$vFunc )
          {
             $aTplForm['js_funcs'] .= $vFunc;
          }

          $this->__aTplData['form'] = $CNK->__oDisplay->EvalTpl( $aTplForm, '', $mRidForm );
       }
       else $this->__aTplData['form'] = '';

    }


    function _GetPageButtons( $mRidBtn, $nCurPage, $nPagesNum, $aRes, $aRes1 )
    {
       global $CNK;
       $sResult = '';
       $aTplBtn = array();

       $sBtnPrev   = ( $aRes['frm_btn_prev']  !='' ) ? $aRes['frm_btn_prev']   : $CNK->__aWords['L_col_frmopts_default_prev'];
       $sBtnNext   = ( $aRes['frm_btn_next']  !='' ) ? $aRes['frm_btn_next']   : $CNK->__aWords['L_col_frmopts_default_next'];
       $sBtnSubmit = ( $aRes['frm_btn_submit']!='' ) ? $aRes['frm_btn_submit'] : $CNK->__aWords['L_col_frmopts_default_submit'];
       $aTplBtn['type'] = 'button';

       if ( $nCurPage!=1 )
       {
          $nPgToHide = $nCurPage  - 1; // ChangePage 'to_hide' arg
          $nPgToShow = $nPgToHide - 1; // ChangePage 'to_show' arg
          $aTplBtn['on_click'] = "onClick='ChangePage({$nPgToHide},{$nPgToShow})'";
          $aTplBtn['btn_caption'] = ( $aRes1['pg_btn_prev']!='' ) ? $aRes1['pg_btn_prev'] : $sBtnPrev;
          $sResult = $CNK->__oDisplay->EvalTpl( $aTplBtn, '', $mRidBtn );
       }

       if ( $nCurPage==$nPagesNum )
       {
          $aTplBtn['on_click'] = "onClick='CheckPage{$aRes1['pg_id']}();'";
          $aTplBtn['btn_caption'] = $sBtnSubmit;
          $sResult .= $CNK->__oDisplay->EvalTpl( $aTplBtn, '', $mRidBtn );
       }
       else
       {
          $aTplBtn['on_click'] = "onClick='CheckPage{$aRes1['pg_id']}();'";
          $aTplBtn['btn_caption'] = ( $aRes1['pg_btn_next']!='' ) ? $aRes1['pg_btn_next'] : $sBtnNext;
          $sResult .= $CNK->__oDisplay->EvalTpl( $aTplBtn, '', $mRidBtn );
       }

       return $sResult;
    }

    function _GetColor( $sThisColor, $sParentColor, $sDefaultColor )
    {
       global $CNK;
       $sResult = '';
       $sColId = ( $sThisColor!='' ) ? $sThisColor : $sParentColor;
       $CNK->__oDb->ExecQuery( "SELECT * FROM pf_colors WHERE color_id='{$sColId}'" );

       if ( $aRes = $CNK->__oDb->Fetch() ) $sResult = $aRes['color_rgb'];
         else $sResult = $sDefaultColor;

       return '#'.$sResult;
    }

//
    function _GetStrSize( $aRes2 )
    {
       global $CNK;
       $sResult = '';
       $sParamName = 'size';
       $mQRid = $CNK->__oDb->ExecQuery( "SELECT val_value FROM pf_properties RIGHT JOIN pf_prop_values ON prop_id=val_prop_id WHERE val_fld_id='{$aRes2['fld_id']}' AND prop_name='{$sParamName}'" );
       if ( $aRes=$CNK->__oDb->Fetch($mQRid) )
          if ( $aRes['val_value'] != '0' )
             $sResult = "{$sParamName}=\"{$aRes['val_value']}\"";
       return $sResult;
    }

    function _GetStrMaxLength( $aRes2 )
    {
       global $CNK;
       $sResult = '';
       $sParamName = 'maxlength';
       $mQRid = $CNK->__oDb->ExecQuery( "SELECT val_value FROM pf_properties RIGHT JOIN pf_prop_values ON prop_id=val_prop_id WHERE val_fld_id='{$aRes2['fld_id']}' AND prop_name='{$sParamName}'" );
       if ( $aRes=$CNK->__oDb->Fetch($mQRid) )
          if ( $aRes['val_value'] != '0' )
             $sResult = "{$sParamName}=\"{$aRes['val_value']}\"";
       return $sResult;
    }
//
    // Get 'rows' or 'size' parameter for multiple select and textarea
    function _GetRowsNumber( $aRes2 )
    {
       global $CNK;
       $sParamName = 'rows';
       $mQRid = $CNK->__oDb->ExecQuery( "SELECT val_value FROM pf_properties RIGHT JOIN pf_prop_values ON prop_id=val_prop_id WHERE val_fld_id='{$aRes2['fld_id']}' AND prop_name='{$sParamName}'" );
       if ( $aRes=$CNK->__oDb->Fetch($mQRid) )
         $nResult = $aRes['val_value'];
       else $nResult = $this->__aVars["DEFAULT_ROWS"];
       return $nResult;
    }

    // Default for edit, password, textarea, ...
    function _GetDefaultSection( $aRes2 )
    {
       global $CNK;
       $sResult = '';
       $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_items WHERE it_fld_id='{$aRes2['fld_id']}'" );
       if ( $aRes=$CNK->__oDb->Fetch($mQRid) )
       {
          if ( preg_match( '/H/', $aRes2['type_props'] ) )
          {
             $sResult = $CNK->__oStd->TxtUnSafeOutput( $aRes['it_title'] );
          }
          else
             $sResult = $CNK->__oStd->TxtSafeOutput( $aRes['it_title'] );
       }
       return $sResult;
    }

    // Get checkbox or radiobutton group
    function _GetCheckRadio( $aRes2, $sTplPath )
    {
       global $CNK;
       $sResult  = '';
       $aTpl = array();
       $aTpl['style'] = ( $aRes2['fld_control_style']=='' ) ? $this->__aVars["EXTRA_CTRL_STYLE"]: $aRes2['fld_control_style'];
       $sTplFile = $CNK->__oDisplay->LoadTplFile( $sTplPath );
       $mRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_items WHERE it_fld_id='{$aRes2['fld_id']}' ORDER BY it_num ASC" );
       while ( $aRes=$CNK->__oDb->Fetch($mRid) )
       {
          $sVal = ( $aRes['it_value']=='' ) ? $aRes['it_title'] : $aRes['it_value'];

          $aTpl['name']  = 'fld_'.$aRes2['fld_id'];
          $aTpl['id']    = $aRes['it_id'];

          $aTpl['value'] = $aRes['it_value']; //$aRes['it_id'];
          $aTpl['title'] = $aRes['it_title'];
          $aTpl['checked'] = ( $aRes['it_default']==1 ) ? ' checked ' : '';

          if ( $sResult!='' ) $sResult .="<br>\n";
          $sResult .= $CNK->__oDisplay->EvalTplFile( $aTpl, $sTplFile );
       }
       return $sResult;
    }

    // For select, multiple select only
    function _GetSelectOptions( $aFldType )
    {
       global $CNK;
       $sResult = '';

       if ( preg_match('/O/', $aFldType['type_props']) )
       {
          $mQRid3 = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_items WHERE it_fld_id='{$aFldType['fld_id']}' ORDER BY it_num" );
          while ( $aRes3 = $CNK->__oDb->Fetch($mQRid3) )
          {
             $sSelected = ( $aRes3['it_default']==1 ) ? ' selected' : '';
             $sVal      = ( $aRes3['it_value']=='' ) ? $aRes3['it_title']: $aRes3['it_value'];
             $sResult .= "<option value='{$sVal}'{$sSelected}>{$aRes3['it_title']}</option>\n";
          }
       }
       return $sResult;
    }

    // Get multiple select item's ids
    function _GetMultItems( $sFieldId )
    {
       global $CNK;
       $sResult = '';
       $mQRid = $CNK->__oDb->ExecQuery( "SELECT it_id FROM pf_items WHERE it_fld_id='{$sFieldId}' ORDER BY it_num" );
       while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
       {
          if ( $sResult!='' ) $sComma = ',';
          $sResult = $sResult . $sComma . "'{$aRes['it_id']}'";
       }
       return $sResult;
    }

    // For Date type
    function _GetDateCtrl( $aRes2, $sTplPath )
    {
       global $CNK;
       $sResult = '';
       $nToYear   = $this->__aVars["DEFAULT_YEARS"];
       $nFromYear = 0;
       $sOrder    = 'DMY';
       $sFunc     = '';

       $aDaysNum  = array( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );
       $nDay      = 1;
       $nMonth    = 1;
       $nYear     = date("Y");
       $bOnChangeCheck = 0; // on/off javascript month checks

       $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_items WHERE it_fld_id='{$aRes2['fld_id']}'" );
       while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
       {
          if ( $aRes['it_title']=='interval' )
             list($nFromYear, $nToYear) = explode( ',', $aRes['it_value'] );
          else if ( $aRes['it_title']=='order' ) $sOrder = $aRes['it_value'];
          else if ( $aRes['it_title']=='func' )  $sFunc  = $aRes['it_value'];
       }

       // Data checks
       if ( $nToYear < $nFromYear ) $nFromYear = $nToYear;
       if ( !preg_match('/D|M|Y/', $sOrder) ) $sOrder = 'DMY';
       if ( preg_match('/D/', $sOrder) && preg_match('/M/', $sOrder) && preg_match('/Y/', $sOrder) )
       {
           $sJsPath = $CNK->__aCfg['PATH_BASE'] . $CNK->__aCfg['CHECKS_JS_PATH'] . 'dates';
           $this->__aChkFuncs['dates'] = $CNK->__oDisplay->LoadTplFile( $sJsPath );
           $bOnChangeCheck = 1;
       }

       if ( $sFunc=='currentdate' )
       {
          list( $nDay, $nMonth ) = explode( '.', date("d.m") );
          $nDay   = (int)$nDay;
          $nMonth = (int)$nMonth;
       }

       // Correct February days number (if leap year)
       if ( $nMonth==2 )
       {
          if ( $nYear % 400==0 || ($nYear % 100!=0 && $nYear % 4==0) )
            $aDaysNum[$nMonth-1] = 29;
          else $aDaysNum[$nMonth-1] = 28;
       }

       $aTpl = array();
       $sTplFile = $CNK->__oDisplay->LoadTplFile( $sTplPath );
       for ( $i=0; $i<strlen($sOrder) && $i<3; $i++ )
       {
          //
          $aTpl['date_select'] = '';
          if ( $sOrder[$i]=='D' )
          {
             $aTpl['name'] = "fld_d_{$aRes2['fld_id']}";
             for ( $j=1; $j<=$aDaysNum[$nMonth-1]; $j++ )
             {
                $sSelected = ( $j==$nDay ) ? ' selected' : '';
                $aTpl['date_select'] .= "<option value='{$j}'{$sSelected}>{$j}</option>\n";
             }
             $sResult .= $CNK->__oDisplay->EvalTplFile( $aTpl, $sTplFile );
          }
          else if ( $sOrder[$i]=='M' )
          {
             if ( $bOnChangeCheck )
                $aTpl['onchange'] = "CheckLeapYear('fld_m_{$aRes2['fld_id']}')";
             $aTpl['name'] = "fld_m_{$aRes2['fld_id']}";
             for ( $j=1; $j<=12; $j++ )
             {
                $sSelected = ( $j==$nMonth ) ? ' selected' : '';
                $sMonthName = ucwords( $CNK->__aWords["L_global_s_month_{$j}"] );
                $aTpl['date_select'] .= "<option value='{$sMonthName}'{$sSelected}>{$sMonthName}</option>\n";
             }
             $sResult .= $CNK->__oDisplay->EvalTplFile( $aTpl, $sTplFile );
          }
          else if ( $sOrder[$i]=='Y' )
          {
             if ( $bOnChangeCheck )
                $aTpl['onchange'] = "CheckLeapYear('fld_y_{$aRes2['fld_id']}')";
             $aTpl['name'] = "fld_y_{$aRes2['fld_id']}";

             for ( $j=$nFromYear; $j<=$nToYear; $j++ )
             {
                $sYear = $nYear-$j;
                $sSelected = ( $j==0 ) ? ' selected' : '';
                $aTpl['date_select'] .= "<option value='{$sYear}'{$sSelected}>{$sYear}</option>\n";
             }
             $sResult .= $CNK->__oDisplay->EvalTplFile( $aTpl, $sTplFile );
          }
       }

       return $sResult;
    }

    // Prepare field check
    function _FieldCheck( $aRes2 )
    {
       global $CNK;
       $sResult = '';
       $aTpl = array();
       $aFileFunc = array( 'not_empty'      => array( 'NotEmpty',     0 ),
                           'numbers_only'   => array( 'NumbersOnly',  0 ),
                           'letters_only'   => array( 'LettersOnly',  0 ),
                           'email'          => array( 'Email',        0 ),
                           'from_to_chars'  => array( 'FromToChars',  2 ),
                           'less_than_opts' => array( 'LessThanOpts', 1 ),
                           'more_than_opts' => array( 'MoreThanOpts', 1 ),
                           'equal_opts'     => array( 'EqualOpts',    1 ),
                           'interval_opts'  => array( 'IntervalOpts', 2 )
                         );

       $sPath = $aRes2['type_check_path'];
       $sChkPath = $CNK->__aCfg['PATH_BASE'] . $CNK->__aCfg['CHECKS_JS_PATH'] . $sPath.'/';
       $sPrefix  = '';
       $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_checks_values RIGHT JOIN pf_checks ON val_chk_id=chk_id WHERE val_fld_id='{$aRes2['fld_id']}'" );
       if ( $aRes = $CNK->__oDb->Fetch($mQRid) )
       {
          if ( !isset($this->__aChkFuncs[$sPath.'/'.$aRes['chk_rule']]) )
          {
             if ( $sPath=='checkbox' ) $sLangPrefix = 'cbx_';
             elseif ($aRes['chk_type_id']=='1') $sLangPrefix = 'mul_';
             else $sLangPrefix = 'str_';
             $aTpl['alert_message'] = $CNK->__aWords['L_js_chk_'.$sLangPrefix.$aRes['chk_rule']];

             $sFileCache = $CNK->__oDisplay->LoadTplFile( $sChkPath.$aRes['chk_rule'] );

             $this->__aChkFuncs[$sPath.'/'.$aRes['chk_rule']] = $CNK->__oDisplay->EvalTplFile( $aTpl, $sFileCache );
          }

          $aNums = explode( ',', $aRes['val_fld_value'] );
          if ( $sPath=='checkbox' ) $sPrefix = 'Ch'; // Checkbox functions prefix

          $sFldName = addslashes( $CNK->__oStd->TxtUnSafeOutput( $aRes2['fld_name'] ) );

          $sResult  = '   nResult += ';
          $sResult .= $sPrefix.$aFileFunc[$aRes['chk_rule']][0] . "( nResult, 'fld_{$aRes2['fld_id']}','{$sFldName}'";
          for ( $i=0; $i<$aFileFunc[$aRes['chk_rule']][1]; $i++ )
          {
             $sResult .= ',' . $aNums[$i];
          }
          // For checkbox group, ugly indeed! :(
          if ( $sPath=='checkbox' ) $sResult .= ', Array('. $this->_GetMultItems( $aRes2['fld_id'] ) .')';

          $sResult .= " );\n";
       }
       return $sResult;
    }
    //////////////////////////////////////////////////////////////////////
    //                                                                  //
    //                     Form rendering part end                      //
    //                                                                  //
    //////////////////////////////////////////////////////////////////////


    //----------------------------------------------------//
    // Autoconfiguration for the module                   //
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
                            $CNK->__aCfg,
                            _LoadConfig($this->__aModVars["my_mod_conf"])
                          );
        }
    }

}

?>