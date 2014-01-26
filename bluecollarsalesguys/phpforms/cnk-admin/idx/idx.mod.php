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
    var $__aTplData   = array();
    var $__sHtml;
    var $__aModVars   = array();
    var $__aCfgBackUp = array();

    Function IDX()
    {
        global $CNK;

        /* {{{ Run autoconfigurator }}} */
        $this->_ThisAutoConfig();

        /* {{{ Prepare base url for template }}} */
        $this->__aTplData["base_url"] = $CNK->__sBaseUrl;
        $this->__aTplData["clean_url"] = preg_replace( "/(.*)\?.*/", "$1", $this->__aTplData["base_url"] );
        $this->__aTplData["img_path"] = $this->__aModVars["my_mod_imgs"];

        /* {{{ Load language support and prepare for template }}} */
        $CNK->__oI18n->LoadSection();
        $this->__aTplData = array_merge( (array)$this->__aTplData, (array)$CNK->__aWords );
        $this->__aTplData['onload_section'] = '';
        $this->__aTplData['SITE_NAME'] =  $CNK->__aCfg['SITE_NAME'];

        /// File download without authorization (!)
        if ( $CNK->__aIn['code']=='07' || isset($CNK->__aIn['file_id']) )
        {
           $this->_DownloadFile( $CNK->__aIn['file_id'] );
           die;
        }

        if ( !$this->_isLoggedIn() )
        {
           header("Location: ". $this->__aTplData["clean_url"]. "?act=login" );
           exit; // Authorization required
        }

        // Local config
        $this->__aVarNames["ITEMS_GROUP"] = 'it_group37145';
        $this->__aVarNames["ACTION"]      = 'action';
        $this->__aVarNames["PG_ACTION"]   = 'pg_act';
        $this->__aVarNames["FLD_ACTION"]  = 'fld_act';

        $this->__aVarNames["IT_ACTION"]   = 'it_act';
        $this->__aVarNames["EIT_ACTION"]  = 'eit_act';
        $this->__aVarNames["ADV_ACTION"]  = 'adv_act';
        $this->__aVarNames["PRE_ACTION"]  = 'pre_act';

        $this->__aVarNames["MIN_YEARS"]     = -100;  // 2004 - (-100) = 2104  -from year
        $this->__aVarNames["MAX_YEARS"]     = 100;  // 2004 - 100   = 1904    -to year

        $this->__aVarNames["DMY_ORDER"]     = 'DMY';

        $this->__aVarNames["MAX_FILE_SIZE"] = '2000000';

        $this->__aVarNames["ADD_PRE_ITEMS"] = 20;

        $this->__aVarNames["ADV_MAXLEN"]  = 'e_maxlen';
        $this->__aVarNames["ADV_SIZE"]    = 'e_size';
        $this->__aVarNames["ADV_ROWS"]    = 'm_rows';
        $this->__aVarNames["ADV_FSIZE"]   = 'f_size';

        $this->__aVarNames["LAY_HIDDEN"]  = 'layout';

        $this->__aActions["SAVE_PAGE"]    = 'save_page';
        $this->__aActions["SAVE_FIELD"]   = 'save_field';
        $this->__aActions["SAVE_ITEMS"]   = 'save_items';
        $this->__aActions["SAVE_ADV"]     = 'save_adv';
        $this->__aActions["CHANGE_TYPE"]  = 'change_type';
        $this->__aActions["ADD_ITEM"]     = 'add_item';
        $this->__aActions["ADD_PRE"]      = 'add_pre';
        $this->__aActions["IT_DEL_MULT"]  = 'del_mult';

        $this->__aTmp = array(); // for temporary local data transfering

        switch($CNK->__aIn['code'])
        {
           // Add/Edit form
           case '01':
                      $sFormID = SetDefault( $CNK->__aIn['fid'] );
                      $this->_AddEditForm( $sFormID );
                      break;

           // Delete form
           case '01d':
                      $sFormId = SetDefault( $CNK->__aIn['fid'] );
                      $this->_DeleteForm( $sFormId );
                      break;

           // Add/Edit form submit
           case '02':
                      $sFormID = SetDefault( $CNK->__aIn['fid'] );
                      $this->_AddEditFormSubmit( $sFormID );
                      break;

           // Form pages (with fields) list
           case '03':
                      $sFormID  = SetDefault( $CNK->__aIn['fid'] );
                      $sPageID  = SetDefault( $CNK->__aIn['page_id'] );
                      $sFieldId = SetDefault( $CNK->__aIn['fld_id'] );
                      if ( $sFieldId=='' ) $sFieldId = SetDefault( $CNK->__aIn['pf_id'] );

                      $this->_PageList( $sFormID, $sPageID, $sFieldId );
                      break;

           // Manage predefined
           case '04':
                      $sListId = SetDefault( $CNK->__aIn['list_id'] );
                      $sAct    = SetDefault( $CNK->__aIn['pre_act'] );
                      if ( $sAct=='' ) $sAct = SetDefault( $CNK->__aIn['prv_act'] );

                      $this->_PreList( $sListId, $sAct );
                      break;

           // Manage mail templates
           case '05':
                      $sMTplId = SetDefault( $CNK->__aIn['mtpl_id'] );
                      $sAct    = SetDefault( $CNK->__aIn['mtpl_act'] );
                      $this->_MTplList( $sMTplId, $sAct );
                      break;

           // View user submits
           case '06':
                      $sSubId   = SetDefault( $CNK->__aIn['sub_rid'] );
                      $sAct     = SetDefault( $CNK->__aIn['sub_act'] );
                      $sSortBy  = SetDefault( $CNK->__aIn['sortby'] );
                      $sOrder   = strtolower( SetDefault( $CNK->__aIn['order'] ) );

                      $this->_SubmitList( $sSubId, $sAct, $sSortBy, $sOrder );
                      break;

           // Get code
           case '08':
                      $sFormId = SetDefault( $CNK->__aIn['fid'] );
                      $this->_GetCode( $sFormId );
                      die;
                      break;

           // Edit settings
           case '09':
                      $sAction = SetDefault( $CNK->__aIn['settings_action'] );
                      $this->_EditSettings( $sAction );
                      break;

           default:
                   $this->_FormList();
                   break;
        }

        /* {{{ Eval template }}} */
        $this->__aTplData['manage_forms_href'] = $this->__aTplData["clean_url"] . "?act=idx";
        $this->__aTplData['manage_pre_href']   = $this->__aTplData["clean_url"] . "?act=idx&code=04";
        $this->__aTplData['manage_mtpl_href']  = $this->__aTplData["clean_url"] . "?act=idx&code=05";
        $this->__aTplData['view_submits_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=06";
        $this->__aTplData['settings_href']     = $this->__aTplData["clean_url"] . "?act=idx&code=09";
        $this->__aTplData['logout_href']       = $this->__aTplData["clean_url"] . "?act=login&code=02";

        if ( isset($this->__aTmp['E_ACTION']) && $this->__aTmp['E_ACTION']=='MTPL_EDIT' )
          $this->__sHtml = $CNK->__oDisplay->EvalTpl( $this->__aTplData, 'idx_mtpl_edit_wrp', '', false );
        else
          $this->__sHtml = $CNK->__oDisplay->EvalTpl( $this->__aTplData, 'idx_wrapper', '', false );

        $this->_ThisModConf( 'recover' );

        /* {{{ Output html to browser }}} */
        echo $this->__sHtml;
    }

    function _isLoggedIn()
    {
       global $CNK;
       $sCookie = $CNK->__oStd->my_GetCookie( 'pwd' );
       list( $sPwd, $sId ) = explode( '_', $sCookie );

       $CNK->__oDb->ExecQuery( "SELECT * FROM pf_admins WHERE adm_password='{$sPwd}' AND adm_id='{$sId}'" );
       if ( $CNK->__oDb->NumRows()>0 ) return true;
       else return false;
    }


    function _MTplList( $sMTplId, $sAct )
    {
       global $CNK;

       if ( $sAct!='' )
       {
          $this->_PerformMTplAction( $sMTplId, $sAct );
          if ( $sAct=='edit' ) return 0;
       }

       $mRidAll  = $CNK->__oDisplay->LoadTpl('idx_all_pages');
       $mRidWrp  = $CNK->__oDisplay->LoadTpl('sections/mtpl/idx_mtpl_wrp');
       $mRidHFrm = $CNK->__oDisplay->LoadTpl('sections/mtpl/idx_mtpl_frm');
       $mRidHead = $CNK->__oDisplay->LoadTpl('sections/mtpl/idx_mtpl_header');
       $mRidRow  = $CNK->__oDisplay->LoadTpl('sections/mtpl/idx_mtpl_row');
       $mRidNoIt = $CNK->__oDisplay->LoadTpl('sections/mtpl/idx_mtpl_noitems');

       $aTplAll  = $CNK->__aWords;
       $aTplWrp  = array();
       $aTplHead = $CNK->__aWords;
       $aTplRow  = $CNK->__aWords;
       $aTplNoIt = $CNK->__aWords;

       $aTplAll['img_path']              = $this->__aModVars["my_mod_imgs"];
       $aTplAll['include_hidden_frm']    = $CNK->__oDisplay->EvalTpl( array(), '', $mRidHFrm );
       $aTplAll['display_sec_btn']       = 'none';
       $aTplAll['all_fields_header']     = $CNK->__aWords['L_head_mtpl_lists'];
       $sNewMTplId = ShortUniqueId();
       $aTplAll['create_new_field_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=05&mtpl_id={$sNewMTplId}&mtpl_act=new";
       $aTplAll['create_new_page_href']  = '';

       $aTplAll['L_btn_flds_new_field'] = $CNK->__aWords['L_btn_mtpl_add_new'];

       $aTplWrp['T_field_rows'] = '';
       $aTplRow['img_path'] = $this->__aModVars["my_mod_imgs"];
       $CNK->__oDb->ExecQuery( "SELECT mtpl_id, mtpl_name, mtpl_tpl, mtpl_plain FROM pf_mail_tpls ORDER BY mtpl_name ASC" );
       if ( $CNK->__oDb->NumRows()>0 )
       {
          $aTplWrp['TL_fields_header'] = $CNK->__oDisplay->EvalTpl( $aTplHead, '', $mRidHead );
          while ( $aRes = $CNK->__oDb->Fetch() )
          {
             $aTplRow['mtpl_name']      = $aRes['mtpl_name'];
             $aTplRow['mtpl_edit_js']   = "window.open('" . "?act=idx&code=05&mtpl_id={$aRes['mtpl_id']}&mtpl_act=edit" . "','page_edit', 'width=730,height=490,toolbar=0,scroll=auto'); return false;";
             $aTplRow['mtpl_del_href']  = $this->__aTplData["clean_url"] . "?act=idx&code=05&mtpl_id={$aRes['mtpl_id']}&mtpl_act=del_mtpl";
             $aTplRow['L_phr_are_u_sure'] = $CNK->__aWords['L_phr_are_u_sure'];
             $aTplWrp['T_field_rows']  .= $CNK->__oDisplay->EvalTpl( $aTplRow, '', $mRidRow );
          }
       }
       else
          $aTplWrp['TL_fields_header'] = $CNK->__oDisplay->EvalTpl( $aTplNoIt, '', $mRidNoIt );

       $aTplAll['T_idx_all_pages'] = $CNK->__oDisplay->EvalTpl( $aTplWrp, '', $mRidWrp );

       // Right section info
       if ( isset($this->__aTmp['mtpl_deleted']) )
         $aTplAll['TL_right_section'] = $this->_LoadInfo( 'L_phr_mtpl_deleted', array('name'=> $this->__aTmp['mtpl_deleted']) );
       else $aTplAll['TL_right_section'] = $this->_LoadNote( 'mail_tpl' );


       $this->__aTplData['L_page_title']          = $CNK->__aWords['L_page_mtpl_list'];
       $this->__aTplData['T_idx_wrapper_content'] = $CNK->__oDisplay->EvalTpl( $aTplAll, '', $mRidAll );
    } // _MTplList

    function _PerformMTplAction( $sMTplId, $sAct )
    {
       global $CNK;

       switch ( $sAct )
       {
          case 'new':
            $mQRid = $CNK->__oDb->ExecQuery(
                   "SELECT * FROM pf_mail_tpls WHERE mtpl_id='{$sMTplId}'" );
            if ( $CNK->__oDb->NumRows($mQRid)<1 && $sMTplId!='' )
            {
               $sMTplName = $CNK->__aWords['L_vars_mtpl_name'];
               $CNK->__oDb->ExecQuery( "INSERT INTO pf_mail_tpls (mtpl_id, mtpl_name, mtpl_tpl, mtpl_from, mtpl_plain)
                                        VALUES ( '{$sMTplId}', '{$sMTplName}', '', '', 0 )" );
            }
            break;

          case 'edit':
            // Prepare popup window'z data
            $this->__aTmp['E_ACTION']         = 'MTPL_EDIT'; // Popup mtpl edit flag
            $this->__aTplData = array_merge( (array)$this->__aTplData, (array) $CNK->__aWords );
            $this->__aTplData['L_page_title'] = $CNK->__aWords['L_page_mtpl_edit'];

            $CNK->__oDb->ExecQuery( "SELECT * FROM pf_mail_tpls WHERE mtpl_id='{$sMTplId}'" );
            if ( $aRes = $CNK->__oDb->Fetch() )
            {
               $this->__aTplData['mtpl_id']   = $sMTplId;
               $this->__aTplData['mtpl_name'] = $aRes['mtpl_name'];
               $this->__aTplData['mtpl_from'] = $aRes['mtpl_from'];
               $this->__aTplData['mtpl_subj'] = $aRes['mtpl_subj'];
               $this->__aTplData['mtpl_tpl']  = $CNK->__oStd->TxtSafeOutput( $aRes['mtpl_tpl'] );
            }

            // Build form select
            $aFormIds = $aFldData = $aFldText = array();

            $this->__aTplData['form_select'] = "<option></option>\n";
            $mQRid = $CNK->__oDb->ExecQuery( "SELECT frm_id, frm_name FROM pf_forms" );
            while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
            {
               $aFormIds[] = $aRes['frm_id'];
               $sFormName = CropText( $aRes['frm_name'], 30 );
               $this->__aTplData['form_select'] .=
                "<option value='{$aRes['frm_id']}' title='{$aRes['frm_name']}'>{$sFormName}</option>\n";
               $aPFNum = array();
               $aFldName = array();

               $aPages = GetPagesIds($aRes['frm_id']);

               for ( $i=0; $i<count($aPages); $i++ )
               {
                  $j=0;
                  $mQRid1 = $CNK->__oDb->ExecQuery( "SELECT fld_id, fld_name FROM pf_fields WHERE fld_pg_id='{$aPages[$i]}' ORDER BY fld_num" );
                  while ( $aRes1 = $CNK->__oDb->Fetch($mQRid1) )
                  {
                     $j++; $sPF = ($i+1) . ".$j";
                     $sFldName = $CNK->__oStd->TxtUnSafeOutput( $aRes1['fld_name'] );
                     $aFldName[] = $sPF.'-'. addslashes(CropText( $sFldName, 25 ));
                     $aPFNum[]   = $aRes['frm_id'] .'.'. $aRes1['fld_id'];
                  }
               }

               if ( count($aPFNum)>0 ) $aFldData[] = "Array('". implode( "','", $aPFNum ) ."')";
                 else $aFldData[] = "Array()";

               if ( count($aFldName)>0 ) $aFldText[] = "Array('". implode( "','", $aFldName ) ."')";
                 else $aFldText[] = "Array()";
            }

            if ( count($aFormIds)>0 ) $this->__aTplData['all_form_ids'] = '\''. implode("','", $aFormIds) .'\'';
             else $this->__aTplData['all_form_ids'] = '';

            if ( count($aFldData)>0 ) $this->__aTplData['all_pgfld_data'] = implode( ",\n", $aFldData );
             else $this->__aTplData['all_pgfld_data'] = '';

            if ( count($aFldText)>0 ) $this->__aTplData['all_fld_texts'] = implode( ",\n", $aFldText );
             else $this->__aTplData['all_fld_texts'] = '';

            break;

          case 'save':
            if ( $sMTplId!='' )
            {
               $sMtplName = SetDefault( $CNK->__aIn["mtpl_name"] );
               $sMtplFrom = SetDefault( $CNK->__aIn["mtpl_from"] );
               $sMtplSubj = SetDefault( $CNK->__aIn["mtpl_subj"] );
               $sMtplTpl  = $CNK->__oStd->TxtSafeInput( SetDefault( $CNK->__aIn["mtpl_tpl"] ) );

               $CNK->__oDb->ExecQuery( "SELECT mtpl_name FROM pf_mail_tpls WHERE mtpl_id='{$sMTplId}'" );
               if ( $CNK->__oDb->NumRows()>0 )
               {
                  $CNK->__oDb->ExecQuery( "UPDATE pf_mail_tpls SET
                                            mtpl_name = '{$sMtplName}',
                                            mtpl_from = '{$sMtplFrom}',
                                            mtpl_subj = '{$sMtplSubj}',
                                            mtpl_tpl  = '{$sMtplTpl}'
                                           WHERE mtpl_id='{$sMTplId}'" );
               }
            }
            break;

          case 'del_mtpl':
            $CNK->__oDb->ExecQuery( "SELECT mtpl_name FROM pf_mail_tpls WHERE mtpl_id='{$sMTplId}'" );
            if ( $aRes = $CNK->__oDb->Fetch() )
            {
               $this->__aTmp['mtpl_deleted'] = $aRes['mtpl_name'] . ' '; // Space required!
               $CNK->__oDb->ExecQuery( "DELETE FROM pf_mail_tpls WHERE mtpl_id='{$sMTplId}'" );
            }
            break;
       }
    } // _PerformMTplAction

    // View list of submissions
    function _SubmitList(  $sSubId, $sAct, $sSortBy, $sOrder )
    {
       global $CNK;

       if ( $sAct!='' ) $this->_PerformSubmitsAction( $sSubId, $sAct );

       $mRidAll  = $CNK->__oDisplay->LoadTpl('sections/submits/idx_sub_list');
       $mRidRow  = $CNK->__oDisplay->LoadTpl('sections/submits/idx_sub_row');
       $mRidWrp  = $CNK->__oDisplay->LoadTpl('sections/submits/idx_sub_wrp');
       $mRidHead = $CNK->__oDisplay->LoadTpl('sections/submits/idx_sub_header');
       $mRidNoIt = $CNK->__oDisplay->LoadTpl('sections/submits/idx_sub_noitems');

       $aTplAll  = $CNK->__aWords;
       $aTplRow  = $CNK->__aWords;
       $aTplHead = $CNK->__aWords;
       $aTplNoIt = $CNK->__aWords;
       $aTplWrp  = array();

       $aTplRow['img_path']  = $this->__aModVars["my_mod_imgs"];
       $aTplHead['img_path'] = $this->__aModVars["my_mod_imgs"];
       $aTplAll['T_idx_all_submits'] = '';


       //  Columns sorting
       $aColNames = array( '1'=>'sub_form_name_sort_img',
                           '2'=>'sub_date_time_sort_img'  );
       $aTplHead['sub_form_name_href'] = $this->__aTplData["clean_url"] .
              "?act=idx&code=06&sortby=1&order=asc";
       $aTplHead['sub_date_time_href'] = $this->__aTplData["clean_url"] .
              "?act=idx&code=06&sortby=2&order=asc";
       $aTplHead['sub_form_name_sort_img'] = '';
       $aTplHead['sub_date_time_sort_img'] = '';

       $sSortStr = '';
       $sSortSql = ' ORDER BY sub_date DESC';

       if ( $sSortBy=='1' || $sSortBy=='2' )  // by form name or date/time
       {
          if ( $sOrder=='asc' || $sOrder=='desc' )
          {
             if ( $sOrder=='asc' ) $aTplHead[$aColNames[$sSortBy]] = 'sort.down.gif';
             else $aTplHead[$aColNames[$sSortBy]] = 'sort.up.gif';

             $sSortStr .= "&sortby={$sSortBy}&order={$sOrder}";

             $sNewSort = ( $sOrder=='asc' ) ? 'desc': 'asc';
             if ( $sSortBy=='1' )
             {
                $sSortSql = ' ORDER BY sub_frm_name '. $sOrder;
                $aTplHead['sub_form_name_href'] = $this->__aTplData["clean_url"] .
                       "?act=idx&code=06&sortby=1&order={$sNewSort}";
                $aTplHead['sub_date_time_href'] = $this->__aTplData["clean_url"] .
                       "?act=idx&code=06&sortby=2&order=asc";
             }
             else
             {
                $sSortSql = ' ORDER BY sub_date '. $sOrder;
                $aTplHead['sub_form_name_href'] = $this->__aTplData["clean_url"] .
                       "?act=idx&code=06&sortby=1&order=desc";
                $aTplHead['sub_date_time_href'] = $this->__aTplData["clean_url"] .
                       "?act=idx&code=06&sortby=2&order={$sNewSort}";
             }
          }

          foreach ( $aColNames as $sColName )
          {
             if ( $aTplHead[$sColName]!='' )
               $aTplHead[$sColName] = "<img border='0' src='" .
                $this->__aModVars["my_mod_imgs"] . $aTplHead[$sColName] ."' />";
          }
       }

       $mQRid = $CNK->__oDb->ExecQuery(
        "SELECT DISTINCT sub_rec_id, sub_frm_name, sub_date
         FROM pf_submissions {$sSortSql}"
       );

       if ( $CNK->__oDb->NumRows()>0 )
       {
          $aTplWrp['TL_fields_header'] = $CNK->__oDisplay->EvalTpl( $aTplHead, '', $mRidHead );
          while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
          {
             $aTplRow['sub_name']       = $CNK->__oStd->TxtSafeOutput( $aRes['sub_frm_name'] );
             $aTplRow['sub_date_time']  = $aRes['sub_date'];

             $aTplRow['sub_view_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=06&sub_rid={$aRes['sub_rec_id']}&sub_act=view{$sSortStr}";
             $aTplRow['sub_del_href']  = $this->__aTplData["clean_url"] . "?act=idx&code=06&sub_rid={$aRes['sub_rec_id']}&sub_act=del{$sSortStr}";
             $aTplRow['L_phr_are_u_sure'] = $CNK->__aWords['L_phr_are_u_sure'];
             $aTplWrp['T_field_rows']  .= $CNK->__oDisplay->EvalTpl( $aTplRow, '', $mRidRow );
          }
       }
       else
          $aTplWrp['TL_fields_header'] = $CNK->__oDisplay->EvalTpl( $aTplNoIt, '', $mRidNoIt );

       if ( isset($this->__aTmp['sub_num_deleted']) )
           $aTplAll['TL_right_section'] = $this->_LoadInfo( 'L_phr_items_deleted', array('num'=> $this->__aTmp['sub_num_deleted']) );
       elseif ( $sSubId=='' )
       {
           if ( isset( $CNK->__aIn['exp_act'] ) && ($CNK->__aIn['exp_act']=='true') )
           {
             $aTplAll['TL_right_section'] = $this->_ViewSubmissionGroups($sSubId);
           }
           else
             $aTplAll['TL_right_section'] = $this->_LoadNote( 'user_submits' );
       }
       else
           $aTplAll['TL_right_section'] = $this->_ViewSubmit( $sSubId );


       // Finally
       if ( $aTplAll['TL_right_section']=='' )
          $aTplAll['TL_right_section'] = $this->_LoadNote( 'user_submits' );


       $aTplAll['img_path']          = $this->__aModVars["my_mod_imgs"];
       $aTplAll['all_fields_header'] = $CNK->__aWords['L_head_sub_list'];
       $aTplAll['delete_all_subs_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=06&sub_act=del_all";
       $aTplAll['export_all_subs_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=06&exp_act=true";

       $aTplAll['T_idx_all_pages']   = $CNK->__oDisplay->EvalTpl( $aTplWrp, '', $mRidWrp );

       $this->__aTplData['L_page_title']          = $CNK->__aWords['L_page_sub'];
       $this->__aTplData['T_idx_wrapper_content'] = $CNK->__oDisplay->EvalTpl( $aTplAll, '', $mRidAll );
    }

    function _PerformSubmitsAction( $sSubId, $sAct )
    {
       global $CNK;

       switch ( $sAct )
       {
         case 'del':
              $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_submissions WHERE sub_rec_id='{$sSubId}'" );
              if ( $CNK->__oDb->NumRows($mQRid)>0 )
              {
                 while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
                 {
                    if ( $aRes['sub_filename']!='' )
                      @unlink($aRes['sub_filename']);
                 }
                 $this->__aTmp['sub_num_deleted'] = '1';
                 $CNK->__oDb->ExecQuery( "DELETE FROM pf_submissions WHERE sub_rec_id='{$sSubId}'" );
              }
              break;

         case 'del_all':
              $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_submissions" );
              if ( $CNK->__oDb->NumRows($mQRid)>0 )
              {
                 $this->__aTmp['sub_num_deleted'] = 0;
                 $mQRid1 = $CNK->__oDb->ExecQuery( "SELECT DISTINCT sub_rec_id FROM pf_submissions" );
                 $this->__aTmp['sub_num_deleted'] = $CNK->__oDb->NumRows($mQRid1);

                 while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
                 {
                    if ( $aRes['sub_filename']!='' )
                      @unlink($aRes['sub_filename']);
                 }
                 $CNK->__oDb->ExecQuery( "DELETE FROM pf_submissions" );
              }

              // Clean up uploaded files, for sure
              $sFilesPath = preg_replace( "/\/$/", "", $CNK->__aCfg['UPLOADED_FILES_PATH'] );
              if ( $h = @opendir($sFilesPath) )
              {
                 while ( false !== ($file = readdir($h)) )
                 {
                    if ( $file != '.htaccess' )
                       @unlink( $sFilesPath.'/'.$file );
                 }
                 closedir($h);
              }
              break;

         case 'exp_all':
              $aFrmList = ( isset($CNK->__aIn['frm_ids']) ) ? $CNK->__aIn['frm_ids'] : array();
              $sFrmList = "'" . implode( "','", $aFrmList ) . "'";
              $sCSVData = '';
              $sFrmName = '';

              $hQRid = $CNK->__oDb->ExecQuery( "SELECT DISTINCT sub_frm_id, sub_frm_name FROM pf_submissions WHERE sub_frm_id IN ( {$sFrmList} )" );
              while ( $aFrm = $CNK->__oDb->Fetch($hQRid) )
              {
                  $sFrmId   = $aFrm['sub_frm_id'];
                  $sFrmName = $aFrm['sub_frm_name'];

                  // New form begun
                  $sCSVData .= Array2CSV( array($sFrmName), $CNK->__aCfg['CSV_FIELD_SEPARATOR'] ). "\r\n";

                  $aCols    = array();
                  $sRows    = '';

                  $bColTitlesSet = false;

                  $hQRid1 = $CNK->__oDb->ExecQuery( "SELECT DISTINCT sub_rec_id FROM pf_submissions WHERE sub_frm_id='{$sFrmId}' ORDER BY sub_date ASC " );
                  while ( $aRec = $CNK->__oDb->Fetch($hQRid1) )
                  {
                      $sCurRecId = $aRec['sub_rec_id'];
                      $aRow      = array();

                      // New submission begin
                      $hQRid2 = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_submissions WHERE sub_rec_id='{$sCurRecId}' ORDER BY sub_fld_num ASC" );
                      while ( $aSub = $CNK->__oDb->Fetch($hQRid2) )
                      {
                          if ( !$bColTitlesSet ) $aCols[] = $CNK->__oStd->TxtUnSafeOutput(  $aSub['sub_fld_name'] );
                          $aRow[]   =  $CNK->__oStd->TxtUnSafeOutput( $aSub['sub_fld_value'] );
                      }

                      if ( !$bColTitlesSet )
                      {
                          $sRows .= Array2CSV( $aCols, $CNK->__aCfg['CSV_FIELD_SEPARATOR'] ) . "\r\n";
                          $bColTitlesSet = true;
                      }

                      $sRows .= Array2CSV( $aRow, $CNK->__aCfg['CSV_FIELD_SEPARATOR'] ). "\r\n";

                  }
                  $sCSVData .= $sRows . "\r\n";
              }

              $sCSVData = str_replace( '<', '_', $sCSVData );
              $sCSVData = str_replace( '>', '_', $sCSVData );

              $sToFileName = 'data_'. ShortUniqueId( 4 ) .'.csv';
              if ( !$nError )
              {
                  header( "Cache-control: private" );
                  header( "Content-type: application/force-download" );
                  header( "Content-Length: " . strlen($sCSVData)  );
                  header( "Content-Disposition: attachment; filename={$sToFileName}" );
                  echo $sCSVData;
              }
              die;

       } // switch ( $sAct )
    }

    function _ViewSubmissionGroups()
    {
       global $CNK;
       $mRidExp = $CNK->__oDisplay->LoadTpl('sections/submits/idx_sub_export');

       $mQRid = $CNK->__oDb->ExecQuery(
        "SELECT DISTINCT sub_frm_id, sub_frm_name FROM pf_submissions
         ORDER BY sub_frm_name ASC"
       );

       $aTplExp = $CNK->__aWords;
       $sSelect = "\n<select name=\"frm_ids[]\" size=\"10\" multiple style=\"width:365px;\">\n";

       while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
       {
          $sSelect .= "<option value='{$aRes['sub_frm_id']}'>";
          $sSelect .= $CNK->__oStd->TxtSafeOutput( $aRes['sub_frm_name'] ) . "</option>\n";
       }

       $sSelect .= "\n</select>";
       $aTplExp['forms_select'] = $sSelect;

       return $CNK->__oDisplay->EvalTpl( $aTplExp, '', $mRidExp );
    }

    function _ViewSubmit( $sSubId )
    {
       global $CNK;
       $mRidView = $CNK->__oDisplay->LoadTpl('sections/submits/idx_sub_view');
       $mRidFld  = $CNK->__oDisplay->LoadTpl('sections/submits/idx_sub_fld_row');

       $mQRid = $CNK->__oDb->ExecQuery(
        "SELECT * FROM pf_submissions
        WHERE sub_rec_id='{$sSubId}' ORDER BY sub_fld_num"
       );

       if ( $CNK->__oDb->NumRows()<1 ) return '';

       $aTplView = array();
       $aTplFld  = array();

       $aTplView = $CNK->__aWords;
       $sFrmName = $sDateTime = '';
       $aTplView['sub_form_name'] = $aTplView['sub_date_time'] = '';

       while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
       {
          $aTplFld['sub_field_name'] = $CNK->__oStd->TxtSafeOutput( $aRes['sub_fld_name'] );

          if ( $aRes['sub_filename']!='' )
            $sFldData = "<a class='a_downld' href='{$this->__aTplData['clean_url']}?code=07&file_id={$aRes['sub_id']}'>" .
               $CNK->__oStd->TxtSafeOutput( $aRes['sub_fld_value'] ) . "</a>";
          else $sFldData = $CNK->__oStd->TxtSafeOutput( $aRes['sub_fld_value'] );
          $aTplFld['sub_field_data'] = str_replace( "\n", "<br>\n", $sFldData );

          $aTplView['sub_form_name'] = $aRes['sub_frm_name'];
          $aTplView['sub_date_time'] = $aRes['sub_date'];
          $aTplView['T_sub_fields_rows'] .= $CNK->__oDisplay->EvalTpl( $aTplFld, '', $mRidFld );
       }
       $aTplView['sub_form_name'] =
                     $CNK->__oStd->TxtSafeOutput( $aTplView['sub_form_name'] );

       return $CNK->__oDisplay->EvalTpl( $aTplView, '', $mRidView );
    }

    //////////////////////////////////////////////////////////////////////
    //
    // Download user uploaded file by ID.
    // File name must have format as follows: ID.file_name.~
    //
    // @$sFileId - string: file id (equal to submission id)
    //

    function _DownloadFile( $sFileId )
    {
       global $CNK;
       $nError = 1;
       $sToFileName = '';
       $sCache = '';
       $aRes   = array();

       $sFilesPath = preg_replace( "/\/$/", "", $CNK->__aCfg['UPLOADED_FILES_PATH'] );

       if ( $h = @opendir($sFilesPath) )
       {
          while ( false !== ($sFile = readdir($h)) )
          {
             if ( preg_match( "/^(.+?)\.(.*)\.~$/", $sFile, $aRes ) )
                if ( $sFileId == $aRes[1] )
                {
                   $sToFileName = $aRes[2];
                   $sPath  = $sFilesPath . "/" . $sFile;

                   if ( $mFile = @fopen( $sPath, "rb" ) )
                   {
                      $nError = 0;
                      $sCache = @fread( $mFile, filesize($sPath) );
                      @fclose($mFile);
                   }
                   break;
                }
          }
          closedir($h);
       }

       if ( !$nError )
       {
          header( "Cache-control: private" );
          header( "Content-type: application/force-download" );
          header( "Content-Length: " . filesize($sPath)  );
          header( "Content-Disposition: attachment; filename={$sToFileName}" );
          echo $sCache;
       }
       else
       {
          echo "<center>" . $CNK->__aWords['L_phr_file_not_found'] .
          "<br>\n<a href='javascript:window.history.back();'>" .
          $CNK->__aWords['L_global_go_back'] . "</a></center>";
       }
       die;
    }

    //------------------------------------------------------------------------

    function _GetCode( $sFormId )
    {
       global $CNK;
       $sDns = $sPath = $sContent = '';
       $mSFile  = '';
       $aMatches = $aVars = array();
       $mRidWrp = $CNK->__oDisplay->LoadTpl('idx_forms_getcode');
       $aTplWrp = $CNK->__aWords;
       $aTplWrp['img_path'] = $this->__aModVars["my_mod_imgs"];
       $aTplWrp["SITE_NAME"] = $CNK->__aCfg['SITE_NAME'];

       $aVars['link'] = $CNK->__aCfg['URL_BASE']. "index.php?fid={$sFormId}";
       $aTplWrp['L_gcode_ins_link'] =
         preg_replace( "/<#(.+?)#>/ies", "\$aVars['\\1']", $CNK->__aWords['L_gcode_ins_link'] );

       $aVars['fid']  = $sFormId;
       $aVars['path'] = $CNK->__aCfg['PATH_BASE'];
       $aTplWrp['L_gcode_ins_php'] =
         preg_replace( "/<#(.+?)#>/ies", "\$aVars['\\1']", $CNK->__aWords['L_gcode_ins_php'] );

       // Get html code
       ob_start();

       // Back up
       $sWM = $CNK->__aModules[ $CNK->__sWorkingModule ];

       // Tmp Settings
       $CNK->__aModules[ $CNK->__sWorkingModule ] = MODULES_1 . 'idx/';
       $CNK->__aIn['fid']   = $sFormId;
       $CNK->__aIn['embed'] = 1;

       define ( 'F_WORKING_MODULE_1',
                $CNK->__aModules[ $CNK->__sWorkingModule ]. 'idx.mod'. PHP_EXT );
       @require ( F_WORKING_MODULE_1 );

       // Restore backup
       $CNK->__aModules[ $CNK->__sWorkingModule ] = $sWM;
       $CNK->__aIn['fid']   = '';
       $CNK->__aIn['embed'] = '';

       $sBuffer = ob_get_contents();
       ob_end_clean();

       $aTplWrp['html_form'] = $CNK->__oStd->TxtGenOutput( $sBuffer );

       echo $CNK->__oDisplay->EvalTpl( $aTplWrp, '', $mRidWrp );
    }

    //------------------------------------------------------------------------

    function _PreList( $sListId, $sAct )
    {
       global $CNK;
       $mRidAll  = $CNK->__oDisplay->LoadTpl('idx_all_pages');
       $mRidWrp  = $CNK->__oDisplay->LoadTpl('sections/pre/idx_pre_wrp');
       $mRidHead = $CNK->__oDisplay->LoadTpl('sections/pre/idx_pre_header');
       $mRidRow  = $CNK->__oDisplay->LoadTpl('sections/pre/idx_pre_row');
       $mRidNoIt = $CNK->__oDisplay->LoadTpl('sections/pre/idx_pre_noitems');

       $aTplAll  = array();
       $aTplWrp  = array(); $aTplWrp['img_path'] = $this->__aModVars["my_mod_imgs"];
       $aTplHead = $CNK->__aWords;
       $aTplRow  = $CNK->__aWords;
           $aTplRow['img_path'] = $this->__aModVars["my_mod_imgs"];
       $aTplNoIt = array();
       $aTplNoIt['L_phr_no_items'] = $CNK->__aWords['L_phr_no_items'];

       // Do something with lists
       if ( $sAct!='' ) $this->_PerformPreAction( $sAct );

       // Build predefined lists
       $aTplWrp['T_field_rows'] = '';
       $CNK->__oDb->ExecQuery( "SELECT pre_id, pre_list_name FROM pf_predefined ORDER BY pre_list_name ASC" );
       if ( $CNK->__oDb->NumRows()>0 )
       {
          $aTplWrp['TL_fields_header'] = $CNK->__oDisplay->EvalTpl( $aTplHead, '', $mRidHead );
          while ( $aRes = $CNK->__oDb->Fetch() )
          {
             $aTplRow['list_name']      = $aRes['pre_list_name'];
             $aTplRow['list_edit_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=04&list_id={$aRes['pre_id']}&pre_act=edit";
             $aTplRow['list_del_href']  = $this->__aTplData["clean_url"] . "?act=idx&code=04&list_id={$aRes['pre_id']}&pre_act=del_list";
             $aTplRow['L_phr_are_u_sure'] = $CNK->__aWords['L_phr_are_u_sure'];
             $aTplWrp['T_field_rows']  .= $CNK->__oDisplay->EvalTpl( $aTplRow, '', $mRidRow );
          }
       }
       else
          $aTplWrp['TL_fields_header'] = $CNK->__oDisplay->EvalTpl( $aTplNoIt, '', $mRidNoIt );

       if ( $sListId=='' )
           $aTplAll['TL_right_section'] = $this->_LoadNote( 'predefined' );
       elseif ( isset($this->__aTmp['pre_list_del']) )
           $aTplAll['TL_right_section'] = $this->_LoadInfo( 'L_phr_list_deleted', array('name'=> $this->__aTmp['pre_list_del']) );
       else
           $aTplAll['TL_right_section'] = $this->_AddEditPre( $sListId );

       $aTplAll['img_path']              = $this->__aModVars["my_mod_imgs"];
       $aTplAll['display_sec_btn']       = 'none';
       $aTplAll['all_fields_header']     = $CNK->__aWords['L_head_pre_lists'];
       $aTplAll['L_btn_flds_new_field']  = $CNK->__aWords['L_btn_pre_new_list'];
       $aTplAll['T_idx_all_pages']       = $CNK->__oDisplay->EvalTpl( $aTplWrp, '', $mRidWrp );
       $sNewListId = ShortUniqueId();
       $aTplAll['create_new_field_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=04&list_id={$sNewListId}&pre_act=new";
       $aTplAll['create_new_page_href']  = '';

       $this->__aTplData['L_page_title']          = $CNK->__aWords['L_page_pre_lists'];
       $this->__aTplData['T_idx_wrapper_content'] = $CNK->__oDisplay->EvalTpl( $aTplAll, '', $mRidAll );
    } // _PreList

    function _AddEditPre( $sListId )
    {
       global $CNK;

       $mRidRait = $CNK->__oDisplay->LoadTpl('sections/idx_ext_opts');
       $mRidGen  = $CNK->__oDisplay->LoadTpl('sections/pre/idx_list_pre');
       $mRidRow  = $CNK->__oDisplay->LoadTpl('sections/pre/idx_list_row');

       $aTplRait = $CNK->__aWords;
       $aTplGen  = $CNK->__aWords;
           $aTplGen['img_path'] = $this->__aModVars["my_mod_imgs"];
       $aTplRow  = $CNK->__aWords;

       $aTplRait['code_num']        = '04';
       $aTplRait['ext_param']       = $this->__aVarNames['PRE_ACTION'];
       $aTplRait['ext_param_id']    = 'save';
       $aTplRait['ext_param1']      = 'list_id';
       $aTplRait['ext_param1_id']   = $sListId;
       $aTplGen['list_id']          = $sListId;
       $aTplGen['self_href']        = $this->__aTplData["clean_url"] . "?act=idx&code=04";
       $aTplGen['L_phr_are_u_sure'] = $CNK->__aWords['L_phr_are_u_sure'];
       $aTplGen['pr_del_mult']      = $this->__aActions['IT_DEL_MULT'];
       $aTplGen['L_idx_btn_add_N_items'] = $CNK->__aWords['L_idx_btn_add_N_items'];
       $aTplRait['L_ext_opts_header'] = $CNK->__aWords['L_header_pre_lists'];

       $aTplGen['items_num_select'] = "<option value=''></option>\n";
       for ($i=1; $i<=$this->__aVarNames['ADD_PRE_ITEMS']; $i++ )
       {
          $aTplGen['items_num_select'] .= "<option value='{$i}'>{$i}</option>\n";
       }

       // Get List Name
       $CNK->__oDb->ExecQuery( "SELECT pre_list_name FROM pf_predefined WHERE pre_id='{$sListId}'" );
       if ( $aRes = $CNK->__oDb->Fetch() ) $sCatName = $aRes['pre_list_name'];
         else $sCatName = '';

       // Get Items
       $aTplGen['T_items_rows']     = '';
       $aTplGen['L_phr_no_items']   = $CNK->__aWords['L_phr_no_items'];
       $aTplGen['display_no_items'] = 'none';
       $aTplGen['display_items']    = 'none';

       $aPreIds  = array();
       $CNK->__oDb->ExecQuery( "SELECT prv_id, prv_title, prv_value FROM pf_pre_values WHERE prv_list_id='{$sListId}' ORDER BY prv_num ASC" );

       if ( $CNK->__oDb->NumRows()>0 ) $aTplGen['display_items'] = '';
         else  $aTplGen['display_no_items'] = '';

       while ( $aRes = $CNK->__oDb->Fetch() )
       {
          if ( $sCatName=='' ) $sCatName = $aRes['pr_cat_name'];
          $aTplRow['pr_id']    = $aRes['prv_id'];
          $aPreIds[]           = $aRes['prv_id'];
          $aTplRow['pr_title'] = $aRes['prv_title'];
          $aTplRow['pr_value'] = $aRes['prv_value'];
          $aTplRow['img_path']       = $this->__aModVars["my_mod_imgs"];
          $aTplRow['item_del_href']  = $this->__aTplData["clean_url"]. "?act=idx&code=04&list_id={$sListId}&prv_id={$aRes['prv_id']}&prv_act=del";
          $aTplRow['item_up_href']   = $this->__aTplData["clean_url"]. "?act=idx&code=04&list_id={$sListId}&prv_id={$aRes['prv_id']}&prv_act=up";
          $aTplRow['item_down_href'] = $this->__aTplData["clean_url"]. "?act=idx&code=04&list_id={$sListId}&prv_id={$aRes['prv_id']}&prv_act=down";

          $aTplGen['T_items_rows'] .= $CNK->__oDisplay->EvalTpl( $aTplRow, '', $mRidRow );
       }

       $aTplGen['list_name'] = $sCatName;

       if ( count($aPreIds)>0 ) $aTplGen['cb_pr_ids'] = '\'cb'. implode("','cb", $aPreIds) . '\'';
        else $aTplGen['cb_pr_ids'] = '';

       $aTplRait['T_field_opt_sections'] = $CNK->__oDisplay->EvalTpl( $aTplGen, '', $mRidGen );

       return $CNK->__oDisplay->EvalTpl( $aTplRait, '', $mRidRait );
    } // _AddEditPre

    function _PerformPreAction( $sAct )
    {
       global $CNK;

       switch ( $sAct )
       {
          case 'new':
            $sListId   = SetDefault( $CNK->__aIn['list_id'] );
            $sListName = $CNK->__aWords['L_vars_list_name'];
            $CNK->__oDb->ExecQuery( "SELECT pre_list_name FROM pf_predefined WHERE pre_id='{$sListId}'" );
            if ( !$aRes = $CNK->__oDb->Fetch() )
            {
               $CNK->__oDb->ExecQuery( "INSERT INTO pf_predefined (pre_id, pre_list_name)
                                        VALUES ('{$sListId}', '{$sListName}' )" );
            }
            break;

          case 'add':
              $nNum   = SetDefault( $CNK->__aIn['num'], 0 );
              $sListId = SetDefault( $CNK->__aIn['list_id'] );
              $CNK->__oDb->ExecQuery( "SELECT pre_list_name FROM pf_predefined WHERE pre_id='{$sListId}'" );
              if ( $CNK->__oDb->NumRows()>0 )
              {
                 $CNK->__oDb->ExecQuery( "SELECT prv_num FROM pf_pre_values WHERE prv_list_id='{$sListId}' ORDER BY prv_num DESC" );
                 if ( $aRes = $CNK->__oDb->Fetch() ) $nPrvNum = 1 + (int)$aRes['prv_num'];
                   else $nPrvNum = 0;

                 for ($i=0; $i<$nNum; $i++)
                 {
                    $sPreId = ShortUniqueId();
                    $CNK->__oDb->ExecQuery( "REPLACE INTO pf_pre_values (prv_id, prv_list_id, prv_title, prv_value, prv_num)
                                             VALUES ( '{$sPreId}',
                                                      '{$sListId}',
                                                      '',
                                                      '',
                                                      '{$nPrvNum}' )"
                                          );
                    $nPrvNum++;
                 }
              }
            break;

          case 'del':
            $sPrvId = SetDefault( $CNK->__aIn['prv_id'] );
            $CNK->__oDb->ExecQuery( "DELETE FROM pf_pre_values WHERE prv_id='{$sPrvId}'" );
            break;

          case $this->__aActions['IT_DEL_MULT']:
            $aDelItems = SetDefault( $CNK->__aIn['it_delete'], array() );
            foreach ($aDelItems as $k=>$v)
              if ( $v!='' ) $CNK->__oDb->ExecQuery( "DELETE FROM pf_pre_values WHERE prv_id='{$k}'" );
            break;

          case 'del_list':
            $sListId = SetDefault( $CNK->__aIn['list_id'] );
            $CNK->__oDb->ExecQuery( "SELECT pre_list_name FROM pf_predefined WHERE pre_id='{$sListId}'" );
            if ( $aRes = $CNK->__oDb->Fetch() )
            {
               $this->__aTmp['pre_list_del'] = $aRes['pre_list_name'];
               $CNK->__oDb->ExecQuery( "DELETE FROM pf_pre_values WHERE prv_list_id='{$sListId}'" );
               $CNK->__oDb->ExecQuery( "DELETE FROM pf_predefined WHERE pre_id='{$sListId}'" );
            }
            break;

          case 'up':
            $sPrvId  = SetDefault( $CNK->__aIn['prv_id'] );
            $sListId = SetDefault( $CNK->__aIn['list_id'] );
            $CNK->__oDb->ExecQuery( "SELECT prv_num FROM pf_pre_values WHERE prv_id='{$sPrvId}'" );
            if ( $aRes0 = $CNK->__oDb->Fetch() )
            {
               $CNK->__oDb->ExecQuery( "SELECT prv_id, prv_num FROM pf_pre_values WHERE prv_num<{$aRes0['prv_num']} AND prv_list_id='{$sListId}' ORDER BY prv_num DESC" );
               if ( $aRes1 = $CNK->__oDb->Fetch() )
               {
                  $CNK->__oDb->ExecQuery("UPDATE pf_pre_values SET prv_num='{$aRes1['prv_num']}' WHERE prv_id='{$sPrvId}'");
                  $CNK->__oDb->ExecQuery("UPDATE pf_pre_values SET prv_num='{$aRes0['prv_num']}' WHERE prv_id='{$aRes1['prv_id']}'");
               }
            }
            break;

          case 'down':
            $sPrvId  = SetDefault( $CNK->__aIn['prv_id'] );
            $sListId = SetDefault( $CNK->__aIn['list_id'] );
            $CNK->__oDb->ExecQuery( "SELECT prv_num FROM pf_pre_values WHERE prv_id='{$sPrvId}'" );
            if ( $aRes0 = $CNK->__oDb->Fetch() )
            {
               $CNK->__oDb->ExecQuery( "SELECT prv_id, prv_num FROM pf_pre_values WHERE prv_num>{$aRes0['prv_num']} AND prv_list_id='{$sListId}' ORDER BY prv_num ASC" );
               if ( $aRes1 = $CNK->__oDb->Fetch() )
               {
                  $CNK->__oDb->ExecQuery("UPDATE pf_pre_values SET prv_num='{$aRes1['prv_num']}' WHERE prv_id='{$sPrvId}'");
                  $CNK->__oDb->ExecQuery("UPDATE pf_pre_values SET prv_num='{$aRes0['prv_num']}' WHERE prv_id='{$aRes1['prv_id']}'");
               }
            }
            break;

          case 'save':
            $sListId   = SetDefault( $CNK->__aIn['list_id'] );
            $sListName = SetDefault( $CNK->__aIn['list_name'] );

            $CNK->__oDb->ExecQuery( "REPLACE INTO pf_predefined (pre_id, pre_list_name)
                                     VALUES ( '{$sListId}','{$sListName}' )" );

            $aTitles = SetDefault( $CNK->__aIn['it_title'], array() );
            $aValues = SetDefault( $CNK->__aIn['it_value'], array() );
            if ( count($aTitles)==count($aValues) )
               foreach ($aTitles as $k=>$v) // k-Id, v-Value
               {
                  $sValue = addslashes( $aValues[$k] );

                  $CNK->__oDb->ExecQuery( "UPDATE pf_pre_values SET
                                             prv_title='{$v}',
                                             prv_value='{$sValue}'
                                           WHERE prv_id='{$k}'" );
               }
            break;

       } // switch ( $sAct )

    } // _PerformPreAction

    // Show page list with fields
    function _PageList( $sFormId, $sPageID, $sFieldId )
    {
       global $CNK;
       $mRidAll = $CNK->__oDisplay->LoadTpl('idx_all_pages');
       $sNewId  = ShortUniqueId();

       $aTplAll['img_path'] = $this->__aModVars["my_mod_imgs"];

       $aTplPgRow  = $CNK->__aWords;
       $mRidPgRow  = $CNK->__oDisplay->LoadTpl('idx_pages_row');
       $aTplFldHdr = array();
       $aTplFldHdr = array_merge((array) $aTplFldHdr, (array) $CNK->__aWords);
       $aTplFldHdr['img_path'] = $this->__aModVars["my_mod_imgs"];
       $mRidFldHdr = $CNK->__oDisplay->LoadTpl('idx_fields_header');

       $aTplNoItems['L_phr_no_items'] = $CNK->__aWords['L_phr_no_items'];
       $aTplNoItems['img_path'] = $this->__aModVars["my_mod_imgs"];

       $mRidNoItems = $CNK->__oDisplay->LoadTpl('idx_fields_noitems');

       $aTplPgRow['img_path'] = $this->__aModVars["my_mod_imgs"];

       $aTplPgRow['L_idx_header_field_type'] = $CNK->__aWords['L_idx_header_field_type'];
       $aTplPgRow['L_idx_header_field_name'] = $CNK->__aWords['L_idx_header_field_name'];

       $mRidRow = $CNK->__oDisplay->LoadTpl('idx_fields_row');
       $aTplRow = $CNK->__aWords;

       // To do something with page (pg_act has been set)
       if ( isset($CNK->__aIn['pg_act']) ) $this->_PerformPageAction();

       // Item Up/Down/Del
       if ( isset($CNK->__aIn['it_act']) ) $this->_PerformItemAction();

       // Other type items operations (RESERVED)
       if ( isset($CNK->__aIn[$this->__aVarNames["EIT_ACTION"]]) ) $this->_PerformOtherItemAction();

       // To do something with field (*fld_act* has been set)
       if ( isset($CNK->__aIn[$this->__aVarNames["FLD_ACTION"]]) )
          $this->_PerformFieldAction( $sFieldId, $CNK->__aIn[$this->__aVarNames['FLD_ACTION']] );

       // Save advanced options of the field
       if ( isset($CNK->__aIn['adv_act']) ) $this->_OnAdvSubmit();

       // Pages Loop
       $sPages = '';
       list( $aPages, $aPgTitles ) = GetPagesList( $sFormId );

       if ( isset($aPages[0]) ) $sFirstPageId = $aPages[0];
        else $sFirstPageId = ''; // Erroneous situation

       $aTplAll['create_new_field_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&pg_id={$sFirstPageId}&pg_act=addfld&fld_id={$sNewId}";
       $aTplAll['create_new_page_href']  = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&pg_act=edit";

       // Create page ids array for JavaScript
       $aTplAll['all_pages_ids'] = '\'pr' .implode("','pr", $aPages). '\'';
       $aTplAll['cur_form_id']   = $sFormId;
       $this->__aTplData['onload_section'] .= 'GetCookies();';

//       $sHref = ( isset($CNK->__aIn['fld_id']) ) ? "&fld_id={$CNK->__aIn['fld_id']}" : '';
       $sHref = '';

       $nPages = count($aPages);
       $nFields = 0;
       for ( $i=0; $i<$nPages; $i++ )  // Pages loop
       {
          $aTplPgRow['page_title']          = ($i+1) ." :: ". CropText( $aPgTitles[$i], 20 );
          $aTplPgRow['page_title_hint']     = $aPgTitles[$i];

          $aTplPgRow['page_show_hide_href'] = "javascript:ShowHidePage('pr{$aPages[$i]}');";
          $aTplPgRow['cur_page_id']         = $aPages[$i];

          $sNewId = ShortUniqueId();
          $aTplPgRow['page_add_field_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&pg_act=addfld&pg_id={$aPages[$i]}&fld_id={$sNewId}";
          $aTplPgRow['page_edit_href']      = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&pg_act=edit&pg_id={$aPages[$i]}";
          $aTplPgRow['page_del_href']       = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&pg_act=del&pg_id={$aPages[$i]}" . $sHref;
          $aTplPgRow['L_phr_are_u_sure']    = $CNK->__aWords['L_phr_are_u_sure'];
          $aTplPgRow['page_up_href']        = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&pg_act=up&pg_id={$aPages[$i]}" . $sHref;
          $aTplPgRow['page_down_href']      = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&pg_act=down&pg_id={$aPages[$i]}" . $sHref;

          $sFields = '';
          $CNK->__oDb->ExecQuery("SELECT fld_id, fld_name, fld_type_id, fld_num, type_name FROM pf_fields INNER JOIN pf_types T ON fld_type_id=type_id WHERE fld_pg_id='{$aPages[$i]}' ORDER BY fld_num ASC");

          // If no items, write "No Items"
          if ( $CNK->__oDb->NumRows()<1 ) $aTplPgRow['TL_fields_header'] = $CNK->__oDisplay->EvalTpl( $aTplNoItems, '', $mRidNoItems );
            else $aTplPgRow['TL_fields_header'] = $CNK->__oDisplay->EvalTpl( $aTplFldHdr, '', $mRidFldHdr );

          while ( $aRes = $CNK->__oDb->Fetch() ) // Fields loop
          {
             $nFields++;

             $aTplRow['field_name'] = $aRes['fld_name'];
             $aTplRow['field_type'] = $aRes['type_name'];

             $aTplRow['field_edit_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&fld_id={$aRes['fld_id']}";
             $aTplRow['field_del_href']  = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&fld_id={$aRes['fld_id']}&fld_act=del";
             $aTplRow['field_up_href']   = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&fld_id={$aRes['fld_id']}&fld_act=up";
             $aTplRow['field_down_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&fld_id={$aRes['fld_id']}&fld_act=down";

             $aTplRow['field_id'] = $aRes['fld_id'];
             $aTplRow['form_id']  = $sFormId;
             $aTplRow['img_path'] = $this->__aModVars["my_mod_imgs"];

             $sFields .= $CNK->__oDisplay->EvalTpl( $aTplRow, '', $mRidRow );
          } // Fields loop

          $aTplPgRow['T_field_rows'] = $sFields;

          $sPages .= $CNK->__oDisplay->EvalTpl( $aTplPgRow, '', $mRidPgRow );
       } // Pages Loop


       // Stub
       // If pages have disappeared from DB (must be at least 1!)
       if ( $sPages=='' )
       {
          $mRidNoPages = $CNK->__oDisplay->LoadTpl('idx_pages_none');
          $sPages = $CNK->__oDisplay->EvalTpl( array(), '', $mRidNoPages );
       } // Stub


       $aTplAll['T_idx_all_pages'] = $sPages;
       $sFormName = GetFormName( $sFormId );

       $aStrTpl = array();
       $aStrTpl['form'] = $sFormName;
       $aStrTpl['fn']   = $nFields;
       $aStrTpl['pn']   = $nPages;
       $aTplAll['all_fields_header'] =
           preg_replace( "/<#(.+?)#>/ies", "\$aStrTpl['\\1']", $CNK->__aWords['L_head_flds_on_x_pages'] );

       $aTplAll['L_btn_flds_new_field'] = $CNK->__aWords['L_btn_flds_new_field']; // not trivial=
       $aTplAll['L_btn_flds_new_page']  = $CNK->__aWords['L_btn_flds_new_page'];

       // A kind of action switch
       if ( isset($CNK->__aIn['pg_act']) )
       {
          if ( isset($this->__aTmp['E_ACTION']['PAGE_DEL_INFO']) )
             $aTplAll['TL_right_section'] = $this->__aTmp['E_ACTION']['PAGE_DEL_INFO'];
          else
             $aTplAll['TL_right_section'] = $this->_AddEditPage( $sFormId );
       }
       else if ( isset($CNK->__aIn['it_act']) )
       {
          $aTplAll['TL_right_section'] = $this->_AddEditFieldItems( $sFormId );
       }
       else if ( isset($CNK->__aIn['adv_act']) )
       {
          $aTplAll['TL_right_section'] = $this->_AddEditAdvanced( $sFormId );
       }
       else if ( $sFieldId !='' )
       {
          if ( isset($this->__aTmp['E_ACTION']['FIELD_DELETED']) )
             $aTplAll['TL_right_section'] = $this->_LoadInfo( 'L_phr_field_deleted', array('name'=> $this->__aTmp['field_name']) );
          else
          {
             $aTplAll['TL_right_section'] = $this->_AddEditField( $sFormId, $sFieldId );
             if ( isset($this->__aTmp['E_ACTION']['EMAIL_OPTS_NOTE']) )
               $aTplAll['notes'] = $this->__aTmp['E_ACTION']['EMAIL_OPTS_NOTE'];
          }
       }
       else
          $aTplAll['TL_right_section'] = $this->_LoadNote('fields_list');

       $this->__aTplData['L_page_title']          = $CNK->__aWords['L_page_flds_form_fields'];
       $this->__aTplData['T_idx_wrapper_content'] = $CNK->__oDisplay->EvalTpl( $aTplAll, '', $mRidAll );
    } // _PageList

    //========================================================================
    // Show all forms list
    function _FormList()
    {
       global $CNK;
       $aDstImg = array( "i_email.gif", "i_db.gif" );
       $aDstAlt = array( "L_alt_frm_to_email", "L_alt_frm_to_database" );

       $mRid     = $CNK->__oDisplay->LoadTpl('idx_all_forms');
       $mRidWrp  = $CNK->__oDisplay->LoadTpl('idx_forms_wrp');
       $mRidRow  = $CNK->__oDisplay->LoadTpl('idx_forms_row');
       $mRidDst  = $CNK->__oDisplay->LoadTpl('idx_all_forms_dest');

       $aTpl     = $CNK->__aWords;
       $aTplWrp  = $CNK->__aWords;
       $aTplDst  = array();
       $aTplDst['img_path'] = $this->__aModVars["my_mod_imgs"];

       $aTpl['img_path'] = $this->__aModVars["my_mod_imgs"];

       $aTplRow = $CNK->__aWords;
       $aTplRow['img_path'] = $this->__aModVars["my_mod_imgs"];

       $mQid   = $CNK->__oDb->ExecQuery( "SELECT frm_id, frm_name, frm_type, frm_dest FROM pf_forms" );

       if ( $CNK->__oDb->NumRows($mQid)<1 )
       {
          $mRidNoItems = $CNK->__oDisplay->LoadTpl('idx_forms_noitems');
          $aTplNoItems = $CNK->__aWords;
          $aTpl['T_idx_forms_wrp'] = $CNK->__oDisplay->EvalTpl( $aTplNoItems, '', $mRidNoItems );
       }
       else
       {
          $aTplWrp['T_idx_rows_all_forms'] = '';
          // Forms loop
          while ( $aRes = $CNK->__oDb->Fetch($mQid) )
          {
             $mQid1 = $CNK->__oDb->ExecQuery( "SELECT pg_id FROM pf_pages WHERE pg_frm_id='{$aRes['frm_id']}'" );

             $aPages = array();
             while ( $aRes1 = $CNK->__oDb->Fetch($mQid1) ) $aPages[] = $aRes1['pg_id'];

             $sPages = "'" . implode( "','", $aPages ) . "'";
             $mQid2 = $CNK->__oDb->ExecQuery( "SELECT fld_id FROM pf_fields WHERE fld_pg_id IN ({$sPages})" );

             $aTplRow['fields_count'] = $CNK->__oDb->NumRows($mQid2);
             $aTplRow['pages_count']  = count($aPages);
             $aTplRow['form_name']    = $aRes['frm_name'];

             if ( $aRes['frm_dest'] <= 1 )
             {
                $aTplDst['inf_img'] = $aDstImg[$aRes['frm_dest']];
                $aTplDst['img_alt'] = $CNK->__aWords[$aDstAlt[$aRes['frm_dest']]];
                $aTplRow['form_action'] = $CNK->__oDisplay->EvalTpl( $aTplDst, '', $mRidDst );
             }
             else if ( $aRes['frm_dest'] == 2 )
             {
                $aTplDst['inf_img'] = $aDstImg[0];
                $aTplDst['img_alt'] = $CNK->__aWords[$aDstAlt[0]];;
                $aTplRow['form_action']  = $CNK->__oDisplay->EvalTpl( $aTplDst, '', $mRidDst );
                $aTplDst['inf_img'] = $aDstImg[1];
                $aTplDst['img_alt'] = $CNK->__aWords[$aDstAlt[1]];
                $aTplRow['form_action'] .= $CNK->__oDisplay->EvalTpl( $aTplDst, '', $mRidDst );
             }

             $aTplRow['form_id'] = $aRes['frm_id'];
             $aTplRow['preview_url']  = "index.php?fid={$aRes['frm_id']}&demo=1";
             $aTplRow['get_code_url'] = "admin.php?act=idx&code=08&fid={$aRes['frm_id']}";

             $aTplWrp['T_idx_rows_all_forms'] .=
                         $CNK->__oDisplay->EvalTpl( $aTplRow, '', $mRidRow );
          } // Forms loop (END)

          $aTpl['T_idx_forms_wrp'] = $CNK->__oDisplay->EvalTpl( $aTplWrp, '', $mRidWrp );
       }
       //  Form List table rows (END)
       $aTpl['L_idx_forms_doc'] = $this->_LoadNote('form_list');

       $sAllForms = $CNK->__oDisplay->EvalTpl( $aTpl, '', $mRid );

       $this->__aTplData['L_page_title'] = $CNK->__aWords['L_page_frm_list'];
       $this->__aTplData['T_idx_wrapper_content'] = $sAllForms;
    } // _FormList()

    //========================================================================
    // Add or Edit form (if Form_ID is set)
    function _AddEditForm( $sFormId='' )
    {
       global $CNK;

       $mRid   = $CNK->__oDisplay->LoadTpl('idx_form_opts');
       $aTpl   = $CNK->__aWords;
       $sColId = $sType = $sDest = $sMTpl = '';

       if ( $sFormId != '' )
       {
          $CNK->__oDb->ExecQuery("SELECT * FROM pf_forms WHERE frm_id='$sFormId'");
          if (  $aRes = $CNK->__oDb->Fetch() )
          {
             $aTpl['form_name']     = $CNK->__oStd->TxtSafeOutput( $aRes['frm_name'] );
             $sColId                = $aRes['frm_color'];
             $sDest                 = $aRes['frm_dest'];
             $aTpl['form_email']    = $aRes['frm_email'];
             $sMTpl                 = $aRes['frm_mtpl_id'];
             $aTpl['form_width']    = $aRes['frm_width'];
             $aTpl['after_text']    = $CNK->__oStd->TxtSafeOutput( $aRes['frm_after_sub_txt'] );
             $aTpl['form_redirect'] = $aRes['frm_redirect'];

             $aTpl['btn_prev']      = $aRes['frm_btn_prev'];
             $aTpl['btn_next']      = $aRes['frm_btn_next'];
             $aTpl['btn_submit']    = $aRes['frm_btn_submit'];
          }
       }
       else
       {
          $sFormId = ShortUniqueId();
          $aTpl['form_name']  = $CNK->__aWords['L_vars_form_name'];
          $sDest              = '1';
          $aTpl['btn_prev']   = $CNK->__aWords['L_col_frmopts_default_prev'];
          $aTpl['btn_next']   = $CNK->__aWords['L_col_frmopts_default_next'];
          $aTpl['btn_submit'] = $CNK->__aWords['L_col_frmopts_default_submit'];
       }

       $aTpl['form_id']           = $sFormId;
       $aTpl['color_select']      = BuildColorsSelect( $sColId );
       $aTpl['form_dest_select']  = BuildDestSelect( $sDest );
       $aTpl['form_mtpl_select']  = BuildSQLSelect( 'pf_mail_tpls', 'mtpl_name', 'mtpl_id', $sMTpl, 40);

       $this->__aTplData['onload_section']        .= 'ChangeDemoColor();ChangeFormDest();';

       $aTpl['L_idx_form_opts_doc']                = $this->_LoadNote( 'form_opts' );
       $this->__aTplData['L_page_title']           = $CNK->__aWords['L_page_frmopts'];
       $this->__aTplData['T_idx_wrapper_content']  = $CNK->__oDisplay->EvalTpl( $aTpl, '', $mRid );

    } // _AddEditForm()

    //========================================================================
    // Add or Edit form (if Form_ID is set)
    function _AddEditFormSubmit( $sFrmId='' )
    {
       global $CNK;

       if ( $sFrmId != '' )
       {
          // REPLACE INTO
          $sColorId = substr( $CNK->__aIn['form_color'], 0, 3 );

          $aIn = $CNK->__aIn;
          $sAfterText = $CNK->__oStd->TxtSafeInput( $CNK->__aIn['after_text'] );

          $CNK->__oDb->ExecQuery("SELECT frm_maxfilesize FROM pf_forms WHERE frm_id='$sFrmId'");
          if ( $aRes = $CNK->__oDb->Fetch() ) $nMaxFileSize = $aRes['frm_maxfilesize'];
           else $nMaxFileSize = 0;


          $sql = "REPLACE INTO pf_forms (frm_id, frm_name, frm_dest, frm_email, frm_color, frm_maxfilesize, frm_width, frm_mtpl_id, frm_after_sub_txt, frm_redirect, frm_btn_prev, frm_btn_next, frm_btn_submit)
                  VALUES (
                            '$sFrmId',
                            '{$aIn['form_name']}',
                            '{$aIn['form_dest']}',
                            '{$aIn['form_email']}',
                            '{$sColorId}',
                            '{$nMaxFileSize}',
                            '{$aIn['form_width']}',
                            '{$aIn['form_mtpl_id']}',
                            '{$sAfterText}',
                            '{$aIn['form_redirect']}',
                            '{$aIn['btn_prev']}',
                            '{$aIn['btn_next']}',
                            '{$aIn['btn_submit']}'
                         )";
          $CNK->__oDb->ExecQuery($sql);
          $CNK->__oDb->ExecQuery("SELECT pg_id FROM pf_pages WHERE pg_frm_id='$sFrmId'");
          if ( $CNK->__oDb->NumRows()<1 )
          {
             $sPageId = ShortUniqueId();
             $CNK->__oDb->ExecQuery( "INSERT INTO pf_pages (pg_id, pg_title, pg_color, pg_top_text, pg_bottom_text, pg_width, pg_frm_id, pg_num) ".
                                     "VALUES ('$sPageId', '', '', '', '', '0', '$sFrmId', '0')" );
          }
       }

       $this->_FormList();
    } // _AddEditFormSubmit

    function _DeleteForm( $sFormId='' )
    {
       global $CNK;
       $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_forms WHERE frm_id='{$sFormId}'" );
       if ( $aRes = $CNK->__oDb->Fetch($mQRid) )
       {
          $mQRidP = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_pages WHERE pg_frm_id='{$sFormId}'" );
          while ( $aResP = $CNK->__oDb->Fetch($mQRidP) )
          {
             $this->_DeletePage( $aResP['pg_id'] , $sFormId, false );
          }
          $mQRid = $CNK->__oDb->ExecQuery( "DELETE FROM pf_forms WHERE frm_id='{$sFormId}'" );
       }
       $this->_FormList();
    }

    // Add Edit Field
    function _AddEditField( $sFormId='', $sFieldId='' )
    {
       global $CNK;
       $sResult = '';

       $sSQL = "SELECT pg_id, pg_title, fld_name ".
               "FROM pf_fields F INNER JOIN pf_pages P ON P.pg_id=F.fld_pg_id ".
               "WHERE fld_id='$sFieldId'";

       $CNK->__oDb->ExecQuery( $sSQL );

       $sPageId = '';
       if ( $aRes = $CNK->__oDb->Fetch() )
       {
          $sPageId = $aRes['pg_id'];
       }

       $mRidOpts   = $CNK->__oDisplay->LoadTpl('sections/idx_pgfld_opts');
       $mRidButAdv = $CNK->__oDisplay->LoadTpl('sections/idx_button_adv');
       $mRidFldGen = $CNK->__oDisplay->LoadTpl('sections/idx_field_opts_general');

       $aTplOpts   = $CNK->__aWords;
       $aTplButAdv = $CNK->__aWords;
           $aTplButAdv['adv_form_href'] = $this->__aTplData["clean_url"]."?act=idx&code=03&fid={$sFormId}&fld_id={$sFieldId}&adv_act=edit";
       $aTplFldGen = $CNK->__aWords;
       $aTplFldDef = array();
       $sSections  = '';
       $aTplOpts['button_advanced'] = '';
       $aTplFldGen['required_display'] = '';

       $CNK->__oDb->ExecQuery("SELECT fld_name, fld_color, fld_type_id, fld_required, fld_layout_id, fld_mtpl_id, type_props, type_name FROM pf_fields INNER JOIN pf_types ON fld_type_id=type_id WHERE fld_id='{$sFieldId}'");
       if ( $aRes = $CNK->__oDb->Fetch() )
       {
          $aTplOpts['form_id']    = $sFormId;
          $aTplOpts['field_id']   = $sFieldId;
          $aTplOpts['field_name'] = $aRes['fld_name'];

          $sFldTypeId = $aRes['fld_type_id'];
          $sFldProps    = $aRes['type_props'];
          $sFldTypeName = strtolower( $aRes['type_name'] );

          // Show/Hide 'required' and 'Advanced' button
          if ( preg_match( "/-/", $aRes['type_props'] ) ) $aTplFldGen['required_display'] = 'none';
           else $aTplOpts['button_advanced'] = $CNK->__oDisplay->EvalTpl( $aTplButAdv, '', $mRidButAdv );

          $aTplFldGen['field_name']  = $aRes['fld_name'];
          $aTplFldGen['field_required'] = ( $aRes['fld_required']!=0 ) ? 'checked' : '';
          $aTplFldGen['hidden_fld_type'] = $sFldTypeId;
          $aTplFldGen['page_select'] = GetSelect( GetPagesArray( $sPageId ), $sPageId, 35 );
          $aTplFldGen['type_select'] = GetSelect( GetTypesArray( $sFldTypeId ), $sFldTypeId );
          $sSections .= $CNK->__oDisplay->EvalTpl( $aTplFldGen, '', $mRidFldGen );

          $aTplSec = array();
          $sItems  = '';
          $sCheck = '';

          if ( preg_match("/D/", $sFldProps) )
             $sItems = $this->_GetDefaultSection( $sFieldId, $sFldTypeName );

          if ( preg_match("/G/", $sFldProps) )
             $sCheck = $this->_GetCheckSection( $sFieldId, 'text');

          if ( preg_match("/C/", $sFldProps) )
             $sCheck = $this->_GetCheckSection( $sFieldId, 'mult');

          if ( preg_match("/I/", $sFldProps) )
          {
             if ( preg_match("/R/", $sFldProps) )
               $sItems = $this->_GetItemsSection( $sFieldId, 'radio');
             else
               $sItems = $this->_GetItemsSection( $sFieldId, 'checkbox');
          }

          if ( preg_match("/d/", $sFldProps) )
             $sItems = $this->_GetDateDefaultSection( $sFormId, $sFieldId );


          $sMailSec = $this->_GetMailOptions( $sFieldId, $aRes['fld_mtpl_id'] );

          $sSections .= $sItems . $sCheck . $sMailSec;

          $aTplOpts['L_pgfld_opts_header'] = $CNK->__aWords['L_header_fldopts_fld_opts'];
          $aTplOpts['pf_id']               = $sFieldId;
          $aTplOpts['action_name']         = $this->__aVarNames['FLD_ACTION'];
          $aTplOpts['pf_action']           = $this->__aActions['SAVE_FIELD'];
          $aTplOpts['L_idx_btn_save']      = $CNK->__aWords['L_idx_btn_save'];

          $aTplOpts['T_field_opt_sections'] = $sSections;

          $sResult = $CNK->__oDisplay->EvalTpl( $aTplOpts, '', $mRidOpts );
       }

       return $sResult;
    } // _AddEditField


    // Edit or add new page (view form)
    function _AddEditPage()
    {
       global $CNK;

       //
       $mRidOpts  = $CNK->__oDisplay->LoadTpl('sections/idx_pgfld_opts');
       $mRidPgGen = $CNK->__oDisplay->LoadTpl('sections/idx_page_opts');

       $aTplOpts  = $CNK->__aWords;
       $aTplPgGen = $CNK->__aWords;

       // Init
       $sColId ='';

       $sPageId = SetDefault( $CNK->__aIn['pg_id'] );
       if ( $sPageId=='' ) $sPageId = SetDefault( $CNK->__aIn['pf_id'] );

       if ( $sPageId!='' )
       {
          $CNK->__oDb->ExecQuery( "SELECT pg_id, pg_title, pg_color, pg_top_text, pg_bottom_text, pg_width, pg_btn_prev, pg_btn_next FROM pf_pages WHERE pg_id='{$sPageId}'" );
          if ( $aRes = $CNK->__oDb->Fetch() )
          {
             $aTplPgGen['page_title']       = $aRes['pg_title'];
             $aTplPgGen['page_top_text']    = $CNK->__oStd->TxtSafeOutput( $aRes['pg_top_text'] );
             $aTplPgGen['page_bottom_text'] = $CNK->__oStd->TxtSafeOutput( $aRes['pg_bottom_text'] );
             $sColId                        = $aRes['pg_color'];
             $aTplPgGen['page_width']       = $aRes['pg_width'];
             $aTplPgGen['btn_prev']         = $aRes['pg_btn_prev'];
             $aTplPgGen['btn_next']         = $aRes['pg_btn_next'];
          }
       }

       if ( $sPageId=='' ) $sPageId = ShortUniqueId();

       $this->__aTplData['onload_section'] .= 'ChangeDemoColor();';
       $aTplPgGen['color_select'] = BuildColorsSelect( $sColId );

       $aTplOpts['form_id']     = $CNK->__aIn['fid'];
       $aTplOpts['pf_id']       = $sPageId;
       $aTplOpts['action_name'] = $this->__aVarNames['PG_ACTION'];
       $aTplOpts['pf_action']   = $this->__aActions['SAVE_PAGE'];

       $aTplOpts['T_field_opt_sections'] = $CNK->__oDisplay->EvalTpl( $aTplPgGen, '', $mRidPgGen );

       $aTplOpts['L_pgfld_opts_header'] = $CNK->__aWords['L_head_pgopts'];

       return $CNK->__oDisplay->EvalTpl( $aTplOpts, '', $mRidOpts );
    } // _AddEditPage

    // Perform actions with field items
    function _AddEditFieldItems( $sFormId )
    {
       global $CNK;

       $sFieldId    = SetDefault( $CNK->__aIn['fld_id'] );
       if ( $sFieldId=='' ) $sFieldId = SetDefault( $CNK->__aIn['pf_id'] );

       $mRidOpts    = $CNK->__oDisplay->LoadTpl('sections/idx_pgfld_opts');
       $mRidInfo    = $CNK->__oDisplay->LoadTpl('sections/idx_brief_pf_info');
       $mRidNewItem = $CNK->__oDisplay->LoadTpl('sections/fld_items/idx_item_new');
       $mRidItems   = $CNK->__oDisplay->LoadTpl('sections/fld_items/idx_items');
       $mRidItRow   = $CNK->__oDisplay->LoadTpl('sections/fld_items/idx_items_row');
       $mRidBtnRet  = $CNK->__oDisplay->LoadTpl('sections/idx_button_ret');

       $aTplOpts    = array(); $aTplOpts['T_field_opt_sections'] = '';
       $aTplInfo    = $CNK->__aWords;
       $aTplNewItem = $CNK->__aWords;
       $aTplItems   = $CNK->__aWords;
       $aTplItRow   = $CNK->__aWords;
            $aTplItRow['check_type'] = 'checkbox';
       $aTplBtnRet  = $CNK->__aWords;

       $CNK->__oDb->ExecQuery( "SELECT type_props FROM pf_fields INNER JOIN pf_types ON fld_type_id=type_id WHERE fld_id='{$sFieldId}'" );
       if ( $aRes = $CNK->__oDb->Fetch() )
       {
          $aTplItRow['check_type'] = ( preg_match("/R/", $aRes['type_props']) ) ? 'radio' : 'checkbox';
          $sGroupSuff              = ( preg_match("/R/", $aRes['type_props']) ) ? '' : '[]';
       }
       else
       {
          $aTplItRow['check_type'] = 'checkbox';  $sGroupSuff = '[]';
       }

       $aTplNewItem['new_item_id']     = ShortUniqueId();
       $aTplNewItem['add_item_action'] = $this->__aActions['ADD_ITEM'];
       $aTplNewItem['add_pre_items']   = $this->__aActions['ADD_PRE'];

       $CNK->__oDb->ExecQuery( "SELECT fld_name, fld_type_id, pg_title FROM pf_fields INNER JOIN pf_pages ON fld_pg_id=pg_id WHERE fld_id='{$sFieldId}'" );
       if ( $aRes = $CNK->__oDb->Fetch() )
       {
          $aTplInfo['field_name'] = $aRes['fld_name'];
          $aTplInfo['page_title'] = $aRes['pg_title'];
       }
       $aTplOpts['T_field_opt_sections'] .= $CNK->__oDisplay->EvalTpl( $aTplInfo, '', $mRidInfo );

       $aTplNewItem['pre_select'] = "<option></option>\n";
       $CNK->__oDb->ExecQuery( "SELECT pre_id, pre_list_name FROM pf_predefined ORDER BY pre_list_name ASC" );
       while ( $aRes = $CNK->__oDb->Fetch() )
       {
          $sName = CropText( $aRes['pre_list_name'], 33 );
          $aTplNewItem['pre_select'] .= "<option value='{$aRes['pre_id']}'>{$sName}</option>\n";
       }

       // Init
       $aTplOpts['form_id']   = $CNK->__aIn['fid'];
       $aTplOpts['pf_id']     = $sFieldId;

       $aTplOpts['action_name'] = $this->__aVarNames['IT_ACTION'];
       $aTplOpts['pf_action']   = $this->__aActions['SAVE_ITEMS'];

       $aTplOpts['T_field_opt_sections'] .= $CNK->__oDisplay->EvalTpl( $aTplNewItem, '', $mRidNewItem );

       $aTplItems['T_items_rows'] = '';

       // Display "No items" message or not
       $aTplItems['items_display']    = 'none';
       $aTplItems['no_items_display'] = 'none';

       $aItemsId = array();
       $CNK->__oDb->ExecQuery( "SELECT it_id, it_title, it_value, it_default FROM pf_items WHERE it_fld_id='$sFieldId' ORDER BY it_num ASC" );

       if ( $CNK->__oDb->NumRows()>0 ) $aTplItems['items_display'] = '';
         else $aTplItems['no_items_display'] = '';

       // Items loop
       while ( $aRes = $CNK->__oDb->Fetch() )
       {
          $aItemsId[]                = $aRes['it_id'];
          $aTplItRow['group_name']   = $this->__aVarNames['ITEMS_GROUP'].$sGroupSuff;
          $aTplItRow['it_id']        = $aRes['it_id'];
          $aTplItRow['item_title']   = $aRes['it_title'];
          $aTplItRow['item_value']   = $aRes['it_value'];
          $aTplItRow['item_checked'] = ( $aRes['it_default']=='1' ) ? 'checked' : '';

          $aTplItRow['img_path']   = $this->__aModVars["my_mod_imgs"];
          $aTplItRow['item_del_href']  = "?act=idx&code=03&fid={$CNK->__aIn['fid']}&fld_id={$sFieldId}&it_act=del&item_id={$aRes['it_id']}";
          $aTplItRow['item_up_href']   = "?act=idx&code=03&fid={$CNK->__aIn['fid']}&fld_id={$sFieldId}&it_act=up&item_id={$aRes['it_id']}";
          $aTplItRow['item_down_href'] = "?act=idx&code=03&fid={$CNK->__aIn['fid']}&fld_id={$sFieldId}&it_act=down&item_id={$aRes['it_id']}";

          $aTplItems['T_items_rows'] .= $CNK->__oDisplay->EvalTpl( $aTplItRow, '', $mRidItRow );
       } // Items loop

       $aTplItems['cb_item_ids']        = "'cb" . implode("','cb", $aItemsId) . "'";
       $aTplItems['it_del_mult']        = $this->__aActions['IT_DEL_MULT'];

       $aTplItems['img_path']           = $this->__aModVars["my_mod_imgs"];

       $aTplOpts['T_field_opt_sections'] .= $CNK->__oDisplay->EvalTpl( $aTplItems, '', $mRidItems );

       $sSelfUrl    = $this->__aTplData["clean_url"];
       $sUniqueId   = ShortUniqueId();
       $sAddItemUrl = $sSelfUrl . "?act=idx&code=03&fid={$CNK->__aIn['fid']}&fld_id={$sFieldId}&it_act=edit&item_id={$sUniqueId}";

       $aTplFldSec['add_item_href']  = $sAddItemUrl;

       $aTplOpts['L_pgfld_opts_header'] = $CNK->__aWords['L_head_fldit_header'];

       $aTplBtnRet['on_click']         = "javascript:location='" . $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&fld_id={$sFieldId}'";

       $aTplOpts['button_advanced'] = $CNK->__oDisplay->EvalTpl( $aTplBtnRet, '', $mRidBtnRet );

       $aTplOpts = array_merge((array) $aTplOpts, (array) $CNK->__aWords);
       $CNK->__aTplData['onload_section'] .= 'GetCookies();';
       return $CNK->__oDisplay->EvalTpl( $aTplOpts, '', $mRidOpts );
    } // _AddEditFieldItems


    // View "advanced" form
    function _AddEditAdvanced( $sFormId )
    {
       global $CNK;

       $sFieldId = SetDefault( $CNK->__aIn['fld_id'] );
         if ( $sFieldId=='' ) $sFieldId = SetDefault( $CNK->__aIn['pf_id'] );

       //
       $mRidOpts   = $CNK->__oDisplay->LoadTpl('sections/idx_pgfld_opts');
       $mRidInfo   = $CNK->__oDisplay->LoadTpl('sections/idx_brief_pf_info');
       $mRidLayout = $CNK->__oDisplay->LoadTpl('sections/advanced/idx_adv_layout');
       $mRidHtml   = $CNK->__oDisplay->LoadTpl('sections/advanced/idx_adv_html_opts');
       $mRidHRow   = $CNK->__oDisplay->LoadTpl('sections/advanced/idx_adv_html_row');
       $mRidBtnRet = $CNK->__oDisplay->LoadTpl('sections/idx_button_ret');

       $aTplOpts   = $CNK->__aWords;
       $aTplInfo   = $CNK->__aWords;
       $aTplLayout = $CNK->__aWords;
          $aTplLayout['img_path'] = $this->__aModVars["my_mod_imgs"];
       $aTplHtml   = $CNK->__aWords;
       $aTplHRow   = array();

       $aTplBtnRet = $CNK->__aWords;
            $aTplBtnRet['on_click'] = "javascript:location='" . $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&fld_id={$sFieldId}'";
       $aLayoutIds   = array();
       $aLayoutFiles = array();
       $sColId     = '';
       $aNumericFldIds   = array(); // numeric fields ids
       $aNumericFldNames = array(); // numeric fields names

       $CNK->__oDb->ExecQuery( "SELECT layout_id, layout_file_name FROM pf_layouts" );
       while ( $aRes = $CNK->__oDb->Fetch() )
       {
          $aLayoutIds[]   = $aRes['layout_id'];
          $aLayoutFiles[] = $aRes['layout_file_name'];
       }

       $aTplLayout['layout_hidden']     = $this->__aVarNames['LAY_HIDDEN'];

       // Get layout data
       $CNK->__oDb->ExecQuery( "SELECT fld_color, fld_caption_style, fld_control_style, fld_layout_id FROM pf_fields WHERE fld_id='{$sFieldId}'" );
       if ( $aRes = $CNK->__oDb->Fetch() )
       {
          $sColId = $aRes['fld_color'];
          $aTplLayout['layout_value'] = $aRes['fld_layout_id'];
          $aTplHtml['caption_style']  = stripslashes( $aRes['fld_caption_style'] );
          $aTplHtml['control_style']  = stripslashes( $aRes['fld_control_style'] );
       }
       if ( is_array($aLayoutIds) )
        if ( !in_array($aTplLayout['layout_value'], $aLayoutIds) )
          $aTplLayout['layout_value'] = $aLayoutIds[0];

       $aTplLayout['layout_imgs_dir']    = $CNK->__aCfg['URL_BASE'] . $CNK->__aCfg['LAYOUT_IMG_PATH'];
       $aTplLayout['default_img_layout'] = $aLayoutFiles[0];

       $aTplLayout['layout_extension']  = $CNK->__aCfg['LAYOUT_IMG_EXTENSION'];
       $aTplLayout['layout_ids_array']  = '\'' . implode("','", $aLayoutIds) . '\'';
       $aTplLayout['layout_imgs_array'] = '\'' . implode("','", $aLayoutFiles) . '\'';

       // Retrieve brief field info
       $CNK->__oDb->ExecQuery( "SELECT fld_name, pg_title FROM pf_fields INNER JOIN pf_pages ON fld_pg_id=pg_id WHERE fld_id='{$sFieldId}'" );
       if ( $aRes = $CNK->__oDb->Fetch() )
       {
          $aTplInfo['field_name'] = $aRes['fld_name'];
          $aTplInfo['page_title'] = $aRes['pg_title'];
       }

       $aTplOpts['T_field_opt_sections']    = $CNK->__oDisplay->EvalTpl( $aTplInfo, '', $mRidInfo );
       $aTplOpts['T_field_opt_sections']   .= $CNK->__oDisplay->EvalTpl( $aTplLayout, '', $mRidLayout );

       $aTplHtml['color_select']            = BuildColorsSelect( $sColId );

       // Show additional prop rows
       $aTplHtml['additional_rows']         = '';
       $mQRid = $CNK->__oDb->ExecQuery( "SELECT prop_id, prop_name FROM pf_fields INNER JOIN pf_properties ON fld_type_id=prop_type_id WHERE fld_id='{$sFieldId}' ORDER BY prop_name ASC" );
       while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
       {
          $aTplHRow['opt_value'] = '';

          $mQRid1 = $CNK->__oDb->ExecQuery( "SELECT val_value FROM pf_prop_values WHERE val_prop_id='{$aRes['prop_id']}' AND val_fld_id='{$sFieldId}'" );
          if ( $aRes1 = $CNK->__oDb->Fetch($mQRid1) )
          {
             $aTplHRow['opt_value'] = $aRes1['val_value'];
          }

          switch ( $aRes['prop_name'] )
          {
            // maxlength - edit,password
            case 'maxlength':
                 $aNumericFldIds[]   = $this->__aVarNames['ADV_MAXLEN'];
                 $aNumericFldNames[] = $CNK->__aWords['L_adv_html_opt_maxlength'];
                 $aTplHRow['L_adv_html_opt_name'] = $CNK->__aWords['L_adv_html_opt_maxlength'];
                 $aTplHRow['opt_name']            = $this->__aVarNames['ADV_MAXLEN'];
                 $aTplHtml['additional_rows'] .= $CNK->__oDisplay->EvalTpl( $aTplHRow, '', $mRidHRow );
              break;

            // size - edit,password
            case 'size':
                 $aNumericFldIds[]   = $this->__aVarNames['ADV_SIZE'];
                 $aNumericFldNames[] = $CNK->__aWords['L_adv_html_opt_size'];
                 $aTplHRow['L_adv_html_opt_name'] = $CNK->__aWords['L_adv_html_opt_size'];
                 $aTplHRow['opt_name']            = $this->__aVarNames['ADV_SIZE'];
                 $aTplHtml['additional_rows'] .= $CNK->__oDisplay->EvalTpl( $aTplHRow, '', $mRidHRow );
              break;

            // rows - mult.select, testarea
            case 'rows':
                 $aNumericFldIds[]   = $this->__aVarNames['ADV_ROWS'];
                 $aNumericFldNames[] = $CNK->__aWords['L_adv_html_opt_rows'];
                 $aTplHRow['L_adv_html_opt_name'] = $CNK->__aWords['L_adv_html_opt_rows'];
                 $aTplHRow['opt_name']            = $this->__aVarNames['ADV_ROWS'];
                 $aTplHtml['additional_rows'] .= $CNK->__oDisplay->EvalTpl( $aTplHRow, '', $mRidHRow );
              break;

            // MAX_FILE_SIZE - file upload
            case 'MAX_FILE_SIZE':
                 $mQRid2 = $CNK->__oDb->ExecQuery( "SELECT frm_maxfilesize FROM pf_forms WHERE frm_id='{$sFormId}'" );
                 if ( $aRes2 = $CNK->__oDb->Fetch($mQRid2) ) $aTplHRow['opt_value'] = $aRes2['frm_maxfilesize'];
                 $aNumericFldIds[]   = $this->__aVarNames['ADV_FSIZE'];
                 $aNumericFldNames[] = $CNK->__aWords['L_adv_html_opt_filesize'];
                 $aTplHRow['L_adv_html_opt_name'] = $CNK->__aWords['L_adv_html_opt_filesize'];
                 $aTplHRow['opt_name']            = $this->__aVarNames['ADV_FSIZE'];
                 $aTplHtml['additional_rows'] .= $CNK->__oDisplay->EvalTpl( $aTplHRow, '', $mRidHRow );
              break;
          }
       }

       $this->__aTplData['onload_section'] .= 'ChangeDemoColor();OnLoadSetLayout();';

       $aTplOpts['form_id']     = $sFormId;
       $aTplOpts['pf_id']       = $sFieldId;
       $aTplOpts['action_name'] = $this->__aVarNames['ADV_ACTION'];
       $aTplOpts['pf_action']   = $this->__aActions['SAVE_ADV'];

       $aTplOpts['T_field_opt_sections']   .= $CNK->__oDisplay->EvalTpl( $aTplHtml, '', $mRidHtml );

       $aTplOpts['numeric_fld_ids']     = ( count($aNumericFldIds)>0 ) ? '\''. implode( "','", $aNumericFldIds) . '\''     : '';
       $aTplOpts['numeric_fld_names']   = ( count($aNumericFldNames)>0 ) ? '\''. implode( "','", $aNumericFldNames) . '\'' : '';

       $aTplOpts['L_pgfld_opts_header'] = $CNK->__aWords['L_page_adv_advanced'];
       $aTplOpts['button_advanced']     = $CNK->__oDisplay->EvalTpl( $aTplBtnRet, '', $mRidBtnRet );

       return $CNK->__oDisplay->EvalTpl( $aTplOpts, '', $mRidOpts );
    } // _AddEditAdvanced

    function _SavePage()
    {
       global $CNK;

       $nPageNum = 0;
       $aIn = $CNK->__aIn;

       $aIn['top_text']    = $aIn['top_text'];
       $aIn['bottom_text'] = $aIn['bottom_text'];

       $CNK->__oDb->ExecQuery( "SELECT pg_num FROM pf_pages WHERE pg_id='{$aIn['pf_id']}'" );
       if ( $aRes = $CNK->__oDb->Fetch() ) $nPageNum = $aRes['pg_num'];
         else
         {
            $CNK->__oDb->ExecQuery( "SELECT pg_num FROM pf_pages WHERE pg_frm_id='{$aIn['fid']}' ORDER BY pg_num DESC" );
            if ( $aRes = $CNK->__oDb->Fetch() ) $nPageNum = 1 + (int)$aRes['pg_num'];
         }

       $sColorId = substr( $aIn['page_color'], 0, 3 );

       $sTopText = $CNK->__oStd->TxtSafeInput( $aIn['top_text'] );
       $sBotText = $CNK->__oStd->TxtSafeInput( $aIn['bottom_text'] );

       $CNK->__oDb->ExecQuery(
         "REPLACE INTO pf_pages (pg_id, pg_title, pg_color, pg_top_text, pg_bottom_text, pg_width, pg_frm_id, pg_num, pg_btn_prev, pg_btn_next )
                  VALUES (
                            '{$aIn['pf_id']}',
                            '{$aIn['page_title']}',
                            '{$sColorId}',
                            '{$sTopText}',
                            '{$sBotText}',
                            '{$aIn['page_width']}',
                            '{$aIn['fid']}',
                            '{$nPageNum}',
                            '{$aIn['btn_prev']}',
                            '{$aIn['btn_next']}'
                         )"
       );
    }

    // Add new field
    function _AddField( $sPageId='', $sFieldId='' )
    {
       global $CNK;

       // Clear data to show field edit form
       if ( isset($CNK->__aIn['pg_id'])  ) unset( $CNK->__aIn['pg_id']  );
       if ( isset($CNK->__aIn['pg_act']) ) unset( $CNK->__aIn['pg_act'] );

       $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_pages WHERE pg_id='{$sPageId}'" );
       if ( $aRes = $CNK->__oDb->Fetch($mQRid) ) // if PAGE exists
       {
          $mQRid1 = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_fields WHERE fld_id='{$sFieldId}'" );
          if ( !$aRes = $CNK->__oDb->Fetch($mQRid1) ) // if FIELD with FldId !exists
          {
             $mQRid2 = $CNK->__oDb->ExecQuery( "SELECT fld_num FROM pf_fields WHERE fld_pg_id='{$sPageId}' ORDER BY fld_num DESC" );
             if ( $aRes = $CNK->__oDb->Fetch($mQRid2) ) $nFieldNum = 1 + (int)$aRes['fld_num'];
               else $nFieldNum = 0;

             $CNK->__oDb->ExecQuery( "INSERT INTO pf_fields (fld_id, fld_pg_id, fld_name, fld_color, fld_caption_style, fld_control_style, fld_type_id, fld_num, fld_required, fld_layout_id, fld_mtpl_id)
                                          VALUES ( '{$sFieldId}', '$sPageId', '{$CNK->__aWords['L_vars_field_name']}', '', '', '', '00', {$nFieldNum}, 0, '', '' )" );
          }
       }
    }

    function _DeletePage( $sPageId , $sFormId, $bOnlyOne=true )
    {
       global $CNK;

       $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_pages WHERE pg_id='{$sPageId}' AND pg_frm_id='{$sFormId}'" );
       if ( $aRes = $CNK->__oDb->Fetch($mQRid) ) // if PAGEs exist
       {
          $sPageTitle = $aRes['pg_title'];
          $mQRid1 = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_pages WHERE pg_frm_id='{$sFormId}'" );
          if ( $CNK->__oDb->NumRows($mQRid1)>1 || !$bOnlyOne ) // if PAGES NUM >1
          {
             $this->__aTmp['E_ACTION']['PAGE_DEL_INFO'] = $this->_LoadInfo( 'L_phr_page_deleted', array('name'=> $sPageTitle) );

             $mQRid2 = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_fields WHERE fld_pg_id='{$sPageId}'" );
             while ( $aRes = $CNK->__oDb->Fetch($mQRid2) )
               $this->_DeleteField( $aRes['fld_id'] , false );

             $CNK->__oDb->ExecQuery( "DELETE FROM pf_pages WHERE pg_id='{$sPageId}'" );
          }
          else
          {
             $this->__aTmp['E_ACTION']['PAGE_DEL_INFO'] = $this->_LoadInfo( 'L_phr_page_cant_del', array() );
          }
       }
    }

    //////////////////////////////////////////////////////////////////////
    //
    // Auxilary functions
    //
    //////////////////////////////////////////////////////////////////////

    // Get field default section
    function _GetDefaultSection( $sFieldId, $sControl )
    {
       global $CNK;
       $mRidFldDef = $CNK->__oDisplay->LoadTpl('sections/idx_field_opts_default');
       $mRidCtrl   = $CNK->__oDisplay->LoadTpl( "sections/default/idx_default_" . $sControl );

       $aTplFldDef = $CNK->__aWords;
       $aTplCtrl   = array();

       $aTplCtrl['value'] = GetDefaultString( $sFieldId );
       $aTplFldDef['default_values'] = $CNK->__oDisplay->EvalTpl( $aTplCtrl , '', $mRidCtrl );

       return $CNK->__oDisplay->EvalTpl( $aTplFldDef, '', $mRidFldDef );
    }

    // Get time default section
    function _GetDateDefaultSection( $sFormId, $sFieldId )
    {
       global $CNK;
       $aLocWords = array(
        'D' => 'L_fldopts_date_day',
        'M' => 'L_fldopts_date_month',
        'Y' => 'L_fldopts_date_year'
       );
       $aDateFuncs = array( 'currentdate'=>'Current Date' );

       $aTplSel = $aTplItems = $aTplItRow = $CNK->__aWords;
       $aTplFldDef = $CNK->__aWords;
       $aTplPeriod = $CNK->__aWords;
       $sDefault   = '';
       $sOrder     = 'DMY';
       $nYFrom     = 0;
       $nYTo       = 100;

       $mRidItems  = $CNK->__oDisplay->LoadTpl( "sections/date/idx_date_items" );
       $mRidItRow  = $CNK->__oDisplay->LoadTpl( "sections/date/idx_date_items_row" );
       $mRidFldDef = $CNK->__oDisplay->LoadTpl( "sections/idx_field_opts_default");
       $mRidPeriod = $CNK->__oDisplay->LoadTpl( "sections/date/idx_date_period_row");
       $mRidSel    = $CNK->__oDisplay->LoadTpl( "sections/default/idx_default_select" );

       // Read Data field values
       $CNK->__oDb->ExecQuery( "SELECT it_id, it_title, it_value, it_num, it_default FROM pf_items WHERE it_fld_id='$sFieldId'" );
       if ( $CNK->__oDb->NumRows()>1 )
       {
          while ( $aRes = $CNK->__oDb->Fetch() )
          {
             switch ( $aRes['it_title'] )  // Special Keywords
             {
                case 'func':
                  $sDefault = strtolower($aRes['it_value']);
                  break;

                case 'order':
                  $sOrder   = $aRes['it_value'];
                  break;

                case 'interval':
                  list($nYFrom, $nYTo) = explode( ',', $aRes['it_value'] );
                  $nYFrom = (int)$nYFrom; $nYTo = (int)$nYTo;
                  break;
             }
          }
       }
       if ( $nYTo<$nYFrom ) $nYFrom = $nYTo;
       if ( $sOrder==''   ) $sOrder = 'DMY';

       $aTplItems['T_items_rows'] = '';
       $aTplItRow['img_path']     = $this->__aModVars["my_mod_imgs"];

       for ( $i=0; $i<strlen($sOrder); $i++ )
       {
          $sUpOrder = $sOrder;
          $sDownOrder = $sOrder;

          if ( $i!=0 )
          {
             $sTmp = $sUpOrder[$i]; $sUpOrder[$i] = $sUpOrder[$i-1]; $sUpOrder[$i-1] = $sTmp;
          }

          if ( $i!=strlen($sOrder) )
          {
             $sTmp = $sDownOrder[$i]; $sDownOrder[$i] = $sDownOrder[$i+1]; $sDownOrder[$i+1] = $sTmp;
          }

          $aTplItRow['item_title']    = $CNK->__aWords[$aLocWords[strtoupper($sOrder[$i])]];
          $aTplItRow['item_value']    = $CNK->__aWords[$aLocWords[strtoupper($sOrder[$i])]];
          $aTplItRow['item_name']     = strtoupper($sOrder[$i]);
          $aTplItRow['item_checked']  = ( strtoupper($sOrder[$i])==$sOrder[$i] ) ? 'checked': '';

          $aTplItRow['item_up_href']   = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&fld_id={$sFieldId}&eit_act={$sUpOrder}&type=date";
          $aTplItRow['item_down_href'] = $this->__aTplData["clean_url"] . "?act=idx&code=03&fid={$sFormId}&fld_id={$sFieldId}&eit_act={$sDownOrder}&type=date";

          $aTplItems['T_items_rows'] .= $CNK->__oDisplay->EvalTpl( $aTplItRow , '', $mRidItRow );
       }

       // Build functions select
       $aTplSel['default_select'] = "<option value=''></option>\n";
       foreach ( $aDateFuncs as $kFName=>$vFDisplay )
       {
          $sSelected     = ( $sDefault == $kFName ) ? ' selected': '';
          $aTplSel['default_select'] .= "<option value='{$kFName}'{$sSelected}>{$vFDisplay}</option>\n";
       }

       $aTplFldDef['default_values']  = $CNK->__oDisplay->EvalTpl( $aTplSel , '', $mRidSel );

       // Build year interval select
       $aTplPeriod['items_from_select'] = '';
       $aTplPeriod['items_to_select']   = '';
       for ( $i=$this->__aVarNames['MIN_YEARS']; $i<($this->__aVarNames['MAX_YEARS']+1); $i++ )
       {
          $sSelected = ( $i==$nYFrom ) ? ' selected': '';
           $aTplPeriod['items_from_select'] .= "<option value='{$i}'{$sSelected}>{$i}</option>\n";
          $sSelected = ( $i==$nYTo ) ? ' selected': '';
           $aTplPeriod['items_to_select']   .= "<option value='{$i}'{$sSelected}>{$i}</option>\n";
       }

       $aTplFldDef['additional_rows'] = $CNK->__oDisplay->EvalTpl( $aTplPeriod , '', $mRidPeriod );

       $aTplFldDef['L_idx_default_field_text'] = $CNK->__aWords['L_idx_default_field_text'];

       $sItems   = $CNK->__oDisplay->EvalTpl( $aTplItems,  '', $mRidItems );
       $sDefault = $CNK->__oDisplay->EvalTpl( $aTplFldDef, '', $mRidFldDef );

       return $sItems . $sDefault;
    }

    // Retrieve check according to type
    function _GetCheckSection( $sFieldId, $sCheckType )
    {
       global $CNK;

       $aCheckTypes  = array( 'text'=>'0', 'mult'=>'1' );
       $aCheckIntervalTxt = array( 'text'=>'chars', 'mult'=>'items' );

       $mRidChk = $CNK->__oDisplay->LoadTpl( "sections/idx_field_check" );
       $aTplChk  = $CNK->__aWords;
       $sCheckId = $sCheckValue = '';

       $CNK->__oDb->ExecQuery( "SELECT val_id, val_chk_id, val_fld_value FROM pf_checks_values WHERE val_fld_id='{$sFieldId}'" );

       if ( $aRes = $CNK->__oDb->Fetch() )
       {
          $sCheckId    = $aRes['val_chk_id'];
          $sCheckValue = $aRes['val_fld_value'];
       }

       // onLoad section
       $this->__aTplData['onload_section'] .= "ChangeRule({$aCheckTypes[$sCheckType]});";
       $aTplChk['check_type'] = $aCheckTypes[$sCheckType];

       // Get checks list from DB
       $CNK->__oDb->ExecQuery( "SELECT chk_id, chk_name, chk_rule FROM pf_checks WHERE chk_type_id='{$aCheckTypes[$sCheckType]}'" );
       $aTplChk['check_rule_select'] = "<option value=''></option>\n";
       while ( $aRes = $CNK->__oDb->Fetch() )
       {
          $sSelected = ($aRes['chk_id'] == $sCheckId) ? ' selected' : '';
          $sCheckName = htmlentities( $aRes['chk_name'] );
          $aTplChk['check_rule_select'] .= "<option value='{$aRes['chk_id']}'$sSelected>{$sCheckName}</option>\n";
       }

       $CNK->__oDb->ExecQuery("SELECT it_id FROM pf_items WHERE it_fld_id='{$sFieldId}'");
       $nMaxItems = $CNK->__oDb->NumRows();

       $sFrom = $sTo = '';
       list($sFrom, $sTo) = $this->_GetItemsSelect( $sFieldId, $aCheckTypes[$sCheckType], $sCheckValue );

       $aTplChk['items_from_select'] = $sFrom;
       $aTplChk['items_to_select']   = $sTo;

       $aTplChk['L_idx_check_interval'] =
        $CNK->__aWords[ 'L_idx_check_interval_' . $aCheckIntervalTxt[$sCheckType] ];

       return $CNK->__oDisplay->EvalTpl( $aTplChk, '', $mRidChk );
    }

    // Get field items section
    function _GetItemsSection( $sFieldId, $sControl)
    {
       global $CNK;

       $mRidFldSec = $CNK->__oDisplay->LoadTpl('sections/idx_field_items');
       $mRidSecRow = $CNK->__oDisplay->LoadTpl('sections/idx_field_items_row');

       $aTplFldSec = $CNK->__aWords;
       $aTplSecRow = array();
       $aTplFldSec['T_items_rows'] = '';

       // Items loop
       $CNK->__oDb->ExecQuery( "SELECT it_id, it_title, it_value, it_default FROM pf_items WHERE it_fld_id='$sFieldId' ORDER BY it_num ASC" );

       // Display "No items" message or not
       $aTplFldSec['items_display']    = 'none';
       $aTplFldSec['no_items_display'] = 'none';
       if ( $CNK->__oDb->NumRows()>0 ) $aTplFldSec['items_display'] = '';
         else $aTplFldSec['no_items_display'] = '';

       while ( $aRes = $CNK->__oDb->Fetch() )
       {
          $aTplSecRow['check_type'] = $sControl;
          $aTplSecRow['group_name'] = 'item_group_zx37145';

          $aTplSecRow['item_title']   = $aRes['it_title'];
          $aTplSecRow['item_value']   = $aRes['it_value'];
          $aTplSecRow['item_checked'] = ( $aRes['it_default']=='1' ) ? 'checked' : '';

          $aTplFldSec['T_items_rows'] .= $CNK->__oDisplay->EvalTpl( $aTplSecRow, '', $mRidSecRow );
       } // Items loop

       $sSelfUrl = $this->__aTplData["clean_url"];
       $sAddItemUrl  = $sSelfUrl . "?act=idx&code=03&fid={$CNK->__aIn['fid']}&fld_id={$sFieldId}&it_act=edit";

       $aTplFldSec['add_item_href']  = $sAddItemUrl;
       return $CNK->__oDisplay->EvalTpl( $aTplFldSec, '', $mRidFldSec );
    } // _GetItemsSection

    // Generates mail edit section
    function _GetMailOptions( $sFieldId, $sMailTplId )
    {
       global $CNK;
       $sResult = '';
       $bMtmplExists = 0;
       $aTplMail = $CNK->__aWords;
       $aTplNote = array();

       $CNK->__oDb->ExecQuery( "SELECT type_props, fld_mtpl_id  FROM pf_fields F INNER JOIN pf_types T ON F.fld_type_id=T.type_id WHERE F.fld_id='{$sFieldId}'" );
       if ( $aResF = $CNK->__oDb->Fetch() )
        if ( preg_match("/M/", $aResF['type_props']) )
        {
           $mRidMail = $CNK->__oDisplay->LoadTpl('sections/idx_field_mail_opts');
           $mRidNote = $CNK->__oDisplay->LoadTpl('idx_all_pages_notes');
           $mQidF = $CNK->__oDb->ExecQuery( "SELECT mtpl_id, mtpl_name, mtpl_tpl FROM pf_mail_tpls" );
           $aTplMail['mail_tpl_select'] = "<option></option>\n";
           while ( $aRes = $CNK->__oDb->Fetch($mQidF) )
           {
              $sSelected     = ( $aRes['mtpl_id']==$sMailTplId ) ? ' selected' : '';
              $aTplMail['mail_tpl_select'] .= "<option value='{$aRes['mtpl_id']}'{$sSelected}>{$aRes['mtpl_name']}</option>\n";
           }

           $aTplNote['note'] = $CNK->__oI18n->LoadDoc( 'email_template' );
           $this->__aTmp['E_ACTION']['EMAIL_OPTS_NOTE'] = $CNK->__oDisplay->EvalTpl( $aTplNote, '', $mRidNote );

           $sResult = $CNK->__oDisplay->EvalTpl( $aTplMail, '', $mRidMail );
        }
       return $sResult;
    }

    // Get items section
    function _GetItemsSelect( $sFieldId, $sCheckType='0', $sCheckValue='' )
    {
       global $CNK;

       $sDefFrom = $sDefTo = '0';

       if ( $sCheckType=='1' )
       {
          $CNK->__oDb->ExecQuery("SELECT it_id FROM pf_items WHERE it_fld_id='{$sFieldId}'");
          $nMaxItems = $CNK->__oDb->NumRows();
       }
       else $nMaxItems = 255;

       $aVals = explode( ',', $sCheckValue );
       if ( count($aVals)>1 )
       {
          $sDefFrom = $aVals[0]; $sDefTo = $aVals[1];
       }
       else if ( count($aVals)==1 )
       {
          $sDefFrom = $sDefTo = $aVals[0];
       }

       for ( $i=0; $i<=$nMaxItems; $i++ )
       {
          $sSelected = ( $i==$sDefFrom ) ? ' selected' : '';
          $sFrom .= "<option value='{$i}'{$sSelected}>{$i}</option>\n";
       }

       for ( $i=0; $i<=$nMaxItems; $i++ )
       {
          $sSelected = ( $i==$sDefTo ) ? ' selected' : '';
          $sTo .= "<option value='{$i}'{$sSelected}>{$i}</option>\n";
       }
       return array( $sFrom, $sTo );
    }

    // Do somethimg with item accordingly to 'itact' param
    function _PerformItemAction()
    {
       global $CNK;

       $sItemId  = SetDefault( $CNK->__aIn['item_id'] );

       $sFieldId = SetDefault( $CNK->__aIn['fld_id'] );
       if ( $sFieldId=='' ) $sFieldId = SetDefault( $CNK->__aIn['pf_id'] );

       switch ( $CNK->__aIn['it_act'] )
       {
          // Add item :)
          case $this->__aActions['ADD_ITEM']:

            $CNK->__oDb->ExecQuery( "SELECT it_id FROM pf_items WHERE it_id='{$sItemId}'" );
            if ( $CNK->__oDb->NumRows()<1 )
            {
               $QRid = $CNK->__oDb->ExecQuery( "SELECT it_num FROM pf_items WHERE it_fld_id='{$sFieldId}' ORDER BY it_num DESC" );
               if ( $aRes = $CNK->__oDb->Fetch($QRid) ) $nItNum = 1 + (int)$aRes['it_num'];
                 else $nItNum = 0;
               $CNK->__oDb->ExecQuery( "REPLACE INTO pf_items (it_id, it_fld_id, it_title, it_value, it_num, it_default )
                 VALUES ( '{$sItemId}', '{$sFieldId}', '{$CNK->__aIn['item_title']}', '{$CNK->__aIn['item_value']}',{$nItNum}, 0 )" );
            }
            break;

          // Add predefined
          case $this->__aActions['ADD_PRE']:
            $sPreId = $CNK->__aIn['pre_list'];

            // Start item number
            $nStartNum = 0;
            $CNK->__oDb->ExecQuery( "SELECT it_num FROM pf_items WHERE it_fld_id='{$sFieldId}' ORDER BY it_num DESC" );
            if ( $aRes = $CNK->__oDb->Fetch() ) $nStartNum = 1 + (int)$aRes['it_num'];

            $QRid = $CNK->__oDb->ExecQuery( "SELECT prv_title, prv_value FROM pf_pre_values WHERE prv_list_id='{$sPreId}' ORDER BY prv_num ASC" );
            while ( $aRes = $CNK->__oDb->Fetch($QRid) )
            {
               $sItemId = ShortUniqueId();
           $sTitle  = addslashes( $aRes['prv_title'] );
           $sValue  = $aRes['prv_value'];
               $CNK->__oDb->ExecQuery( "INSERT INTO pf_items (it_id, it_fld_id, it_title, it_value, it_num, it_default )
                 VALUES ( '{$sItemId}', '{$sFieldId}', '{$sTitle}', '{$sValue}',{$nStartNum}, 0 )" );
               $nStartNum++;
            }
            break;

          // Save all items
          case $this->__aActions['SAVE_ITEMS']:
            $sGrName  = $this->__aVarNames['ITEMS_GROUP'];
            $aDefault = ( is_array($CNK->__aIn["$sGrName"]) ) ? $CNK->__aIn["$sGrName"] : array($CNK->__aIn["$sGrName"]) ;

            foreach ( $CNK->__aIn['it_title'] as $k=>$v )
            {
               $nDefault = ( in_array( $k, $aDefault ) ) ? 1 : 0;
               $sValue   = $CNK->__aIn['it_value'][$k];

               $CNK->__oDb->ExecQuery( "UPDATE pf_items SET
                                           it_title='{$v}',
                                           it_value='{$sValue}',
                                           it_default={$nDefault}
                                        WHERE it_id='{$k}'" );
            }
            break;

          // Delete multiple items
          case $this->__aActions['IT_DEL_MULT']:
            $aDelItems = SetDefault( $CNK->__aIn['it_delete'], array() );
            foreach ($aDelItems as $k=>$v)
              if ( $v!='' ) $CNK->__oDb->ExecQuery( "DELETE FROM pf_items WHERE it_id='{$k}'" );
            break;

          // Delete item
          case 'del':
            $CNK->__oDb->ExecQuery( "DELETE FROM pf_items  WHERE it_id='{$sItemId}'" );
            break;

          // Move up
          case 'up':
            $CNK->__oDb->ExecQuery( "SELECT it_num FROM pf_items WHERE it_id='{$sItemId}'" );
            if ( $aRes0 = $CNK->__oDb->Fetch() )
            {
               $CNK->__oDb->ExecQuery( "SELECT it_id, it_num FROM pf_items WHERE it_num<{$aRes0['it_num']} AND it_fld_id='{$sFieldId}' ORDER BY it_num DESC" );
               if ( $aRes1 = $CNK->__oDb->Fetch() )
               {
                  $CNK->__oDb->ExecQuery("UPDATE pf_items SET it_num='{$aRes1['it_num']}' WHERE it_id='{$sItemId}'");
                  $CNK->__oDb->ExecQuery("UPDATE pf_items SET it_num='{$aRes0['it_num']}' WHERE it_id='{$aRes1['it_id']}'");
               }
            }
            break;

          // Move down
          case 'down':
            $CNK->__oDb->ExecQuery( "SELECT it_num FROM pf_items WHERE it_id='{$sItemId}'" );
            if ( $aRes0 = $CNK->__oDb->Fetch() )
            {
               $CNK->__oDb->ExecQuery( "SELECT it_id, it_num FROM pf_items WHERE it_num>{$aRes0['it_num']} AND it_fld_id='{$sFieldId}' ORDER BY it_num ASC" );
               if ( $aRes1 = $CNK->__oDb->Fetch() )
               {
                  $CNK->__oDb->ExecQuery("UPDATE pf_items SET it_num='{$aRes1['it_num']}' WHERE it_id='{$sItemId}'");
                  $CNK->__oDb->ExecQuery("UPDATE pf_items SET it_num='{$aRes0['it_num']}' WHERE it_id='{$aRes1['it_id']}'");
               }
            }
            break;
       }

       $mRidFldSec = $CNK->__oDisplay->LoadTpl('sections/idx_field_items');
       $mRidSecRow = $CNK->__oDisplay->LoadTpl('sections/idx_field_items_row');

       $aTplFldSec = array();
       $aTplSecRow = array();
       $aTplFldSec['T_items_rows'] = '';
    }

    // Other type items operations
    function _PerformOtherItemAction()
    {
       global $CNK;
       $sFieldId = $CNK->__aIn['fld_id'];
       $sFieldType = $CNK->__aIn['type'];
       $sEItAct = $CNK->__aIn['eit_act'];

       switch ( $sFieldType )
       {
         case 'date':
           $CNK->__oDb->ExecQuery( "UPDATE pf_items SET it_value='{$sEItAct}' WHERE it_fld_id='{$sFieldId}' AND it_title='order'" );
           break;
       }
    }

    // To do something with page
    function _PerformPageAction()
    {
       global $CNK;
       $sPageId = SetDefault( $CNK->__aIn['pg_id'] );
       if ( $sPageId=='' ) $sPageId = SetDefault( $CNK->__aIn['pf_id'] );

       $sFormId  = SetDefault( $CNK->__aIn['fid']    );
       $sFieldId = SetDefault( $CNK->__aIn['fld_id'] );

       if ( $sPageId!='' )
       {
          switch ( $CNK->__aIn[$this->__aVarNames['PG_ACTION']] )
          {
             // Move up
             case 'up':
               $CNK->__oDb->ExecQuery( "SELECT pg_num FROM pf_pages WHERE pg_id='{$sPageId}'" );
               if ( $aRes0 = $CNK->__oDb->Fetch() )
               {
                  $CNK->__oDb->ExecQuery( "SELECT pg_id, pg_num FROM pf_pages WHERE pg_num<{$aRes0['pg_num']} AND pg_frm_id='{$sFormId}' ORDER BY pg_num DESC" );
                  if ( $aRes1 = $CNK->__oDb->Fetch() )
                  {
                     $CNK->__oDb->ExecQuery("UPDATE pf_pages SET pg_num='{$aRes1['pg_num']}' WHERE pg_id='{$sPageId}'");
                     $CNK->__oDb->ExecQuery("UPDATE pf_pages SET pg_num='{$aRes0['pg_num']}' WHERE pg_id='{$aRes1['pg_id']}'");
                  }
               }
               break;

             // Move down
             case 'down':
               $CNK->__oDb->ExecQuery( "SELECT pg_num FROM pf_pages WHERE pg_id='{$sPageId}'" );
               if ( $aRes0 = $CNK->__oDb->Fetch() )
               {
                  $CNK->__oDb->ExecQuery( "SELECT pg_id, pg_num FROM pf_pages WHERE pg_num>{$aRes0['pg_num']} AND pg_frm_id='{$sFormId}' ORDER BY pg_num ASC" );
                  if ( $aRes1 = $CNK->__oDb->Fetch() )
                  {
                     $CNK->__oDb->ExecQuery("UPDATE pf_pages SET pg_num='{$aRes1['pg_num']}' WHERE pg_id='{$sPageId}'");
                     $CNK->__oDb->ExecQuery("UPDATE pf_pages SET pg_num='{$aRes0['pg_num']}' WHERE pg_id='{$aRes1['pg_id']}'");
                  }
               }
               break;

             case $this->__aActions['SAVE_PAGE']:
               $this->_SavePage();
               break;

             case 'addfld':
               $this->_AddField( $sPageId , $sFieldId );
               break;

             case 'del':
               $this->_DeletePage( $sPageId , $sFormId );
               break;
          }
       }
    }

    // To do something with field
    function _PerformFieldAction( $sFieldId, $sAct )
    {
       global $CNK;

       if ( $sFieldId!='' )
       {
          switch ( $sAct )
          {
             // Move up
             case 'up':
               $CNK->__oDb->ExecQuery( "SELECT fld_num, fld_pg_id, pg_num, pg_frm_id FROM pf_fields INNER JOIN pf_pages ON fld_pg_id=pg_id WHERE fld_id='{$sFieldId}'" );
               if ( $aRes0 = $CNK->__oDb->Fetch() )
               {
                  $CNK->__oDb->ExecQuery( "SELECT fld_id, fld_num FROM pf_fields WHERE fld_num<{$aRes0['fld_num']} AND fld_pg_id='{$aRes0['fld_pg_id']}' ORDER BY fld_num DESC" );
                  if ( $aRes1 = $CNK->__oDb->Fetch() )
                  {
                     $CNK->__oDb->ExecQuery("UPDATE pf_fields SET fld_num='{$aRes1['fld_num']}' WHERE fld_id='{$sFieldId}'");
                     $CNK->__oDb->ExecQuery("UPDATE pf_fields SET fld_num='{$aRes0['fld_num']}' WHERE fld_id='{$aRes1['fld_id']}'");
                  }
                  else
                  {
                     $CNK->__oDb->ExecQuery( "SELECT pg_id, fld_num FROM pf_fields RIGHT JOIN pf_pages ON fld_pg_id=pg_id WHERE pg_num<{$aRes0['pg_num']} AND pg_frm_id='{$aRes0['pg_frm_id']}' ORDER BY pg_num DESC, fld_num DESC" );
                     if ( $aRes2 = $CNK->__oDb->Fetch() )
                     {
                        $nNewFldNum = ( $aRes2['fld_num']=='' ) ? 0 : (int)$aRes2['fld_num']+1;
                        $CNK->__oDb->ExecQuery("UPDATE pf_fields SET fld_num='{$nNewFldNum}', fld_pg_id='{$aRes2['pg_id']}' WHERE fld_id='{$sFieldId}'");
                     }
                  }
               }
               break; // case 'up'

             // Move down
             case 'down':
               $CNK->__oDb->ExecQuery( "SELECT fld_num, fld_pg_id, pg_num, pg_frm_id FROM pf_fields INNER JOIN pf_pages ON fld_pg_id=pg_id WHERE fld_id='{$sFieldId}'" );
               if ( $aRes0 = $CNK->__oDb->Fetch() )
               {
                  $CNK->__oDb->ExecQuery( "SELECT fld_id, fld_num FROM pf_fields WHERE fld_num>{$aRes0['fld_num']} AND fld_pg_id='{$aRes0['fld_pg_id']}' ORDER BY fld_num ASC" );
                  if ( $aRes1 = $CNK->__oDb->Fetch() )
                  {
                     $CNK->__oDb->ExecQuery("UPDATE pf_fields SET fld_num='{$aRes1['fld_num']}' WHERE fld_id='{$sFieldId}'");
                     $CNK->__oDb->ExecQuery("UPDATE pf_fields SET fld_num='{$aRes0['fld_num']}' WHERE fld_id='{$aRes1['fld_id']}'");
                  }
                  else
                  {
                     $CNK->__oDb->ExecQuery( "SELECT pg_id, fld_num FROM pf_fields RIGHT JOIN pf_pages ON fld_pg_id=pg_id WHERE pg_num>{$aRes0['pg_num']} AND pg_frm_id='{$aRes0['pg_frm_id']}' ORDER BY pg_num ASC, fld_num ASC" );
                     if ( $aRes2 = $CNK->__oDb->Fetch() )
                     {
                        if ( $aRes2['fld_num']=='0' ) $CNK->__oDb->ExecQuery( "UPDATE pf_fields SET fld_num=fld_num+1 WHERE fld_pg_id='{$aRes2['pg_id']}' ");
                        $CNK->__oDb->ExecQuery("UPDATE pf_fields SET fld_num=0, fld_pg_id='{$aRes2['pg_id']}' WHERE fld_id='{$sFieldId}'");
                     }
                  }
               }
               break; // case 'down'

             // Save field data
             case $this->__aActions['SAVE_FIELD']:
               $this->_SaveField( $sFieldId );
               break; // case 'SAVE_FIELD'

             // Change type
             case $this->__aActions["CHANGE_TYPE"]:
               $this->_ChangeFieldType( $sFieldId );
               break; // case 'CHANGE_TYPE'

             // Delete field
             case 'del':
               $this->_DeleteField( $sFieldId );
               break; // case 'del'
          }
       }
    }

    // Save Field data
    function _SaveField( $sFieldId )
    {
       global $CNK;

       $mQRid = $CNK->__oDb->ExecQuery( "SELECT fld_pg_id, fld_num, type_id, type_props FROM pf_fields RIGHT JOIN pf_types ON fld_type_id=type_id WHERE fld_id='{$sFieldId}'" );
       if ( $aRes = $CNK->__oDb->Fetch($mQRid) )
       {
          $sTypeId    = $aRes['type_id'];
          $sTypeProps = $aRes['type_props'];

          $sFieldName = SetDefault( $CNK->__aIn['fld_name'] );
          $nFieldNum  = SetDefault( $CNK->__aIn['fld_num'], 0 );
          $nFieldReq  = ( SetDefault( $CNK->__aIn['fld_req'] )!='' ) ? 1 : 0;
          $sFieldPage = SetDefault( $CNK->__aIn['fld_page'] );
          $sDefault   = SetDefault( $CNK->__aIn['default_val'] );

          $sCheckRule = SetDefault( $CNK->__aIn['check_rule'] );
          $nItemsFrom = SetDefault( $CNK->__aIn['items_from'] );
          $nItemsTo   = SetDefault( $CNK->__aIn['items_to']   );

          $sMailTplId = SetDefault( $CNK->__aIn['mail_tpl']   );

//          $sFieldType = SetDefault( $CNK->__aIn['fld_type'] );

          if ( $aRes['fld_pg_id'] != $sFieldPage )
          {
             $mQRid1 = $CNK->__oDb->ExecQuery( "SELECT fld_num FROM pf_fields WHERE fld_pg_id='{$sFieldPage}' ORDER BY fld_num DESC" );
             if ( $aRes1 = $CNK->__oDb->Fetch($mQRid1) )
                if ( $aRes1['fld_num'] >= $nFieldNum )
                {
                   $nNewNum = $aRes1['fld_num']+1;
                   $CNK->__oDb->ExecQuery( "UPDATE pf_fields SET fld_num='{$nNewNum}' WHERE fld_id = '{$sFieldId}'" );
                }
          }

          $CNK->__oDb->ExecQuery( "UPDATE pf_fields SET
                                    fld_name     = '{$sFieldName}',
                                    fld_required = '{$nFieldReq}',
                                    fld_pg_id    = '{$sFieldPage}'
                                   WHERE fld_id = '{$sFieldId}'" );

          /////////////////////////////////////////////////////////////////////
          // Save default text
          if ( preg_match( "/D/", $sTypeProps ) )
          {
             $sNewId = ShortUniqueId();
             $mQrid2 = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_items WHERE it_fld_id = '{$sFieldId}'" );
             if ( $aRes = $CNK->__oDb->Fetch($mQrid2) )
             {
                while ( $aRes = $CNK->__oDb->Fetch($mQrid2) )
                  $CNK->__oDb->ExecQuery( "DELETE FROM pf_items WHERE it_id = '{$aRes['it_id']}'" );

                $CNK->__oDb->ExecQuery( "UPDATE pf_items SET
                                          it_title     = '{$sDefault}',
                                          it_value     = '' ,
                                          it_num       = '0',
                                          it_default   = '0'
                                         WHERE it_fld_id = '{$sFieldId}'" );
             }
             else $CNK->__oDb->ExecQuery( "INSERT INTO pf_items (it_id, it_fld_id, it_title, it_value, it_num, it_default)
                                                      VALUES ('{$sNewId}', '{$sFieldId}', '{$sDefault}', '', 0, 0)" );
          }

          /////////////////////////////////////////////////////////////////////
          // Save text field check  /G/
          // Save items field check /C/
          if ( preg_match( "/G/", $sTypeProps ) || preg_match( "/C/", $sTypeProps ) )
          {
             $sNewId = ShortUniqueId();
             if ( $sCheckRule=='' )
                $CNK->__oDb->ExecQuery( "DELETE FROM pf_checks_values WHERE val_fld_id = '{$sFieldId}'" );
             else
             {
                $sFieldVal = $nItemsFrom;
                if ( $nItemsFrom!='' ) $sFieldVal .= ',' . $nItemsTo;

                $mQRid2 = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_checks_values WHERE val_fld_id='{$sFieldId}'" );
                if ( $aRes2 = $CNK->__oDb->Fetch($mQRid2) )
                {
                   while ( $aRes = $CNK->__oDb->Fetch($mQrid2) )
                     $CNK->__oDb->ExecQuery( "DELETE FROM pf_items WHERE it_id = '{$aRes['it_id']}'" );

                   $CNK->__oDb->ExecQuery( "UPDATE pf_checks_values SET
                                             val_chk_id     = '{$sCheckRule}' ,
                                             val_fld_id     = '{$sFieldId}',
                                             val_fld_value  = '{$sFieldVal}'
                                            WHERE val_id = '{$aRes2['val_id']}'" );
                }
                else $CNK->__oDb->ExecQuery( "INSERT INTO pf_checks_values (val_id, val_chk_id, val_fld_id, val_fld_value)
                                                      VALUES ('{$sNewId}', '{$sCheckRule}', '{$sFieldId}', '{$sFieldVal}')" );
             }
          }

          /////////////////////////////////////////////////////////////////////
          // Save mail template  /M/
          if ( preg_match( "/M/", $sTypeProps ) )
          {
             $CNK->__oDb->ExecQuery( "UPDATE pf_fields SET
                                       fld_mtpl_id  = '{$sMailTplId}'
                                      WHERE fld_id = '{$sFieldId}'" );
          }

          /////////////////////////////////////////////////////////////////////
          // Save date /d/
          if ( preg_match( "/d/", $sTypeProps ) )
          {
             $sVis[0]   = ( SetDefault( $CNK->__aIn['D'] )!='' ) ? 'D' : 'd';
             $sVis[1]   = ( SetDefault( $CNK->__aIn['M'] )!='' ) ? 'M' : 'm';
             $sVis[2]   = ( SetDefault( $CNK->__aIn['Y'] )!='' ) ? 'Y' : 'y';

             $sFunc     = SetDefault( $CNK->__aIn['default_sel']  );
             $nIntFrom  = SetDefault( $CNK->__aIn['year_from'], 0 );
             $nIntTo    = SetDefault( $CNK->__aIn['year_to'], 100 );

             $sOrder = 'DMY';

             $mQRid3 = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_items WHERE it_fld_id='{$sFieldId}'" );
             while ( $aRes = $CNK->__oDb->Fetch($mQRid3) )
             {
                if ( $aRes['it_title']=='order' )
                {
                   $sOrder = ( strlen($aRes['it_value'])==3 ) ? $aRes['it_value'] : $sOrder;
                   for ( $i=0; $i<strlen($sVis); $i++ )
                     $sOrder = preg_replace( "/{$sVis[$i]}/i", "{$sVis[$i]}", $sOrder );
                }
             }

             $CNK->__oDb->ExecQuery( "UPDATE pf_items SET
                                       it_value    = '{$sOrder}'
                                      WHERE it_fld_id = '{$sFieldId}' AND it_title = 'order'" );

             $CNK->__oDb->ExecQuery( "UPDATE pf_items SET
                                       it_value    = '{$sFunc}'
                                      WHERE it_fld_id = '{$sFieldId}' AND it_title = 'func'" );

             $sInterv = $nIntFrom .','. $nIntTo;
             $CNK->__oDb->ExecQuery( "UPDATE pf_items SET
                                       it_value    = '{$sInterv}'
                                      WHERE it_fld_id = '{$sFieldId}' AND it_title = 'interval'" );
          }
       }
    }

    // Change field type
    function _ChangeFieldType( $sFieldId )
    {
       global $CNK;

       $sFieldName = SetDefault( $CNK->__aIn['fld_name'] );
       $nFieldReq  = ( SetDefault( $CNK->__aIn['fld_req'] )!='' ) ? 1 : 0;
       $sFieldPage = SetDefault( $CNK->__aIn['fld_page'] );
       $sNewType   = SetDefault( $CNK->__aIn['fld_type'] );

       $CNK->__oDb->ExecQuery( "SELECT * FROM pf_types WHERE type_id='{$sNewType}'" );
       if ( $aRes = $CNK->__oDb->Fetch() )
       {
          $CNK->__oDb->ExecQuery( "DELETE FROM pf_prop_values   WHERE val_fld_id='{$sFieldId}'" );
          $CNK->__oDb->ExecQuery( "DELETE FROM pf_checks_values WHERE val_fld_id='{$sFieldId}'" );
          $CNK->__oDb->ExecQuery( "DELETE FROM pf_items  WHERE it_fld_id='{$sFieldId}'" );

          if ( preg_match( '/d/', $aRes['type_props'] ) )
          {
             $sNewId = ShortUniqueId();
             $CNK->__oDb->ExecQuery( "INSERT INTO pf_items (it_id, it_fld_id, it_title, it_value, it_num, it_default)
                                           VALUES ( '{$sNewId}', '{$sFieldId}', 'func', '', 0, 0  ) ");

             $sNewId = ShortUniqueId();
             $CNK->__oDb->ExecQuery( "INSERT INTO pf_items (it_id, it_fld_id, it_title, it_value, it_num, it_default)
                                           VALUES ( '{$sNewId}', '{$sFieldId}', 'interval', '0,{$this->__aVarNames["MAX_YEARS"]}', 0, 0  ) ");

             $sNewId = ShortUniqueId();
             $CNK->__oDb->ExecQuery( "INSERT INTO pf_items (it_id, it_fld_id, it_title, it_value, it_num, it_default)
                                           VALUES ( '{$sNewId}', '{$sFieldId}', 'order', '{$this->__aVarNames["DMY_ORDER"]}', 0, 0  ) ");
          }

          // Adjust field number
          $mQRid = $CNK->__oDb->ExecQuery( "SELECT fld_pg_id, fld_num FROM pf_fields WHERE fld_id='{$sFieldId}'" );
          if ( $aRes = $CNK->__oDb->Fetch($mQRid) )
          {
             $nFieldNum = $aRes['fld_num'];
             if ( $aRes['fld_pg_id'] != $sFieldPage )
             {
                $mQRid1 = $CNK->__oDb->ExecQuery( "SELECT fld_num FROM pf_fields WHERE fld_pg_id='{$sFieldPage}' ORDER BY fld_num DESC" );
                if ( $aRes1 = $CNK->__oDb->Fetch($mQRid1) )
                   if ( $aRes1['fld_num'] >= $nFieldNum )
                   {
                      $nNewNum = $aRes1['fld_num']+1;
                      $CNK->__oDb->ExecQuery( "UPDATE pf_fields SET fld_num='{$nNewNum}' WHERE fld_id = '{$sFieldId}'" );
                   }
             }
          }

          // Save data
          $CNK->__oDb->ExecQuery( "UPDATE pf_fields SET
                                    fld_name     = '{$sFieldName}',
                                    fld_pg_id    = '{$sFieldPage}',
                                    fld_required = '{$nFieldReq}',
                                    fld_type_id  = '{$sNewType}'
                                   WHERE fld_id  = '{$sFieldId}'" );
       }
    }

    // Delete field
    function _DeleteField( $sFieldId, $bOnlyOne=true )
    {
       global $CNK;
       $CNK->__oDb->ExecQuery( "DELETE FROM pf_prop_values   WHERE val_fld_id='{$sFieldId}'" );
       $CNK->__oDb->ExecQuery( "DELETE FROM pf_checks_values WHERE val_fld_id='{$sFieldId}'" );
       $CNK->__oDb->ExecQuery( "DELETE FROM pf_items  WHERE it_fld_id='{$sFieldId}'" );

       $mQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_fields WHERE fld_id='{$sFieldId}'" );
       if ( $aRes = $CNK->__oDb->Fetch($mQRid) )
       {
          if ( $bOnlyOne ) // Single field to delete
          {
             $this->__aTmp['field_name'] = $aRes['fld_name'];
             $this->__aTmp['E_ACTION']['FIELD_DELETED'] = sprintf( "%s(%s)", $aRes['fld_name'], $aRes['fld_name'] );
          }
          $CNK->__oDb->ExecQuery( "DELETE FROM pf_fields WHERE fld_id='{$sFieldId}'" );
       }
    }

    // After someone clicks 'Save' on 'Advanced' page
    function _OnAdvSubmit()
    {
       global $CNK;

       $sFormId = SetDefault( $CNK->__aIn['fid'] );

       if ( isset( $CNK->__aIn[$this->__aVarNames['ADV_ACTION']] ) &&
            $CNK->__aIn[$this->__aVarNames['ADV_ACTION']]==$this->__aActions['SAVE_ADV'] )
       {
          $sFieldId  = SetDefault( $CNK->__aIn['pf_id'] );
          $sLayoutId = SetDefault( $CNK->__aIn['layout'] );
          $sFieldColorId = preg_replace( "/^(.*)\^.*$/", '$1', SetDefault( $CNK->__aIn['field_color'] ));
          $sCaptionStyle = SetDefault( $CNK->__aIn['caption_style'] );
          $sControlStyle = SetDefault( $CNK->__aIn['control_style'] );

          // Save field table changes
          $CNK->__oDb->ExecQuery( "UPDATE pf_fields SET
                                    fld_color='{$sFieldColorId}',
                                    fld_layout_id='{$sLayoutId}',
                                    fld_caption_style='{$sCaptionStyle}',
                                    fld_control_style='{$sControlStyle}'
                                   WHERE fld_id='{$sFieldId}'" );

          // Update HTML properties of champ
          $mQRid = $CNK->__oDb->ExecQuery( "SELECT prop_id, prop_name FROM pf_fields INNER JOIN pf_properties ON fld_type_id=prop_type_id WHERE fld_id='{$sFieldId}'" );
          while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
          {
             $sPropId = $aRes['prop_id'];

             switch ( $aRes['prop_name'] )
             {
               // maxlength - edit,password
               case 'maxlength':
                    $nMaxLength = SetDefault( $CNK->__aIn[$this->__aVarNames['ADV_MAXLEN']], 0 );
                    $nMaxLength = MakeInteger($nMaxLength);
                    $CNK->__oDb->ExecQuery( "REPLACE INTO pf_prop_values (val_prop_id, val_fld_id, val_value )
                                             VALUES ('{$sPropId}', '{$sFieldId}', '{$nMaxLength}' )" );
                 break;

               // size - edit,password
               case 'size':
                    $nSize = SetDefault( $CNK->__aIn[$this->__aVarNames['ADV_SIZE']], 0 );
                    $nSize = MakeInteger($nSize);
                    $CNK->__oDb->ExecQuery( "REPLACE INTO pf_prop_values (val_prop_id, val_fld_id, val_value )
                                             VALUES ('{$sPropId}', '{$sFieldId}', '{$nSize}' )" );
                 break;

               // rows - mult.select, testarea
               case 'rows':
                    $nRows = SetDefault( $CNK->__aIn[$this->__aVarNames['ADV_ROWS']], 0 );
                    $nRows = MakeInteger($nRows);
                    $CNK->__oDb->ExecQuery( "REPLACE INTO pf_prop_values (val_prop_id, val_fld_id, val_value )
                                             VALUES ('{$sPropId}', '{$sFieldId}', '{$nRows}' )" );
                 break;

               // MAX_FILE_SIZE - file upload
               case 'MAX_FILE_SIZE':
                    $nFileSize  = SetDefault( $CNK->__aIn[$this->__aVarNames['ADV_FSIZE']], 0 );
                    $nFileSize = MakeInteger($nFileSize);
                    $CNK->__oDb->ExecQuery( "UPDATE pf_forms SET
                                             frm_maxfilesize='{$nFileSize}'
                                             WHERE frm_id='{$sFormId}'" );
                 break;
             }
          }
       }
    }

    /////////////////////////////////////////////////////////////////////
    // Display "Settings" page and
    // password changing
    //
    function _EditSettings( $sAction = 'view' )
    {
       global $CNK;

       $mRidWrp  = $CNK->__oDisplay->LoadTpl('sections/settings/idx_setup');
       $mRidErr  = $CNK->__oDisplay->LoadTpl('sections/settings/idx_error_wrp');
       $mRidDone = $CNK->__oDisplay->LoadTpl('sections/settings/idx_done_wrp');

       $aTplWrp  = $CNK->__aWords;
       $aTplErr  = array();
       $aTplDone  = $CNK->__aWords;
       $sLogin   = '';

       // Get admin Id
       $sCookie = $CNK->__oStd->my_GetCookie( 'pwd' );
       list( $sPwd, $sId ) = explode( '_', $sCookie );

       if ( $sAction == 'update' )
       {
          $sPassword  = addslashes( $CNK->__aIn['adm_pass'] );
          $sPassword1 = addslashes( $CNK->__aIn['adm_pass1'] );

          // Check login
          $sLogin = addslashes( $CNK->__aIn['adm_login'] );
          $hQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_admins WHERE adm_id!='{$sId}' AND adm_login='{$sLogin}'" );
          if ( $aRes = $CNK->__oDb->NumRows()>0 ) // Such login already exists
          {
             $aTplErr['error_text']  = $CNK->__aWords['L_sett_err_login_exists'];
             $aTplWrp['message'] = $CNK->__oDisplay->EvalTpl( $aTplErr, '', $mRidErr );
          }
          else
          {
             $sOldPassword = addslashes( md5(  $CNK->__aIn['adm_old_pass'] ) );
             $hQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_admins WHERE adm_id='{$sId}' AND adm_password='{$sOldPassword}'" );
             if ( $aRes = $CNK->__oDb->NumRows()>0 ) // Ok, password is correct
             {
                $bLengthOk = ( strlen($CNK->__aIn['adm_pass'])>=5 ) ? true : false ;
                if ( $sPassword == $sPassword1 )
                {
                   $sNewPassMD5 = md5( $sPassword );
                   if ( $bLengthOk )
                   {
                      if ( trim($sLogin) == '' ) $sAdmLoginStr = '';
                      else $sAdmLoginStr = "adm_login    = '{$sLogin}',";

                      $hQRid = $CNK->__oDb->ExecQuery(
                        "UPDATE pf_admins SET
                          {$sAdmLoginStr} adm_password = '{$sNewPassMD5}'
                         WHERE  adm_id='{$sId}'"
                      );

                      // "Re-login"
                      $CNK->__oStd->my_SetCookie( 'pwd', $sNewPassMD5 .'_'. $sId, -1 );
                      $aTplWrp['message'] = $CNK->__oDisplay->EvalTpl( $aTplDone, '', $mRidDone );
                   }
                   else
                   {
                      // Password is too short
                      $aTplErr['error_text']  = $CNK->__aWords['L_sett_err_passw_length'];
                      $aTplWrp['message'] = $CNK->__oDisplay->EvalTpl( $aTplErr, '', $mRidErr );
                   }
                }
                else
                {
                   // Passwords do not match
                   $aTplErr['error_text']  = $CNK->__aWords['L_sett_err_passw_not_eq'];
                   $aTplWrp['message'] = $CNK->__oDisplay->EvalTpl( $aTplErr, '', $mRidErr );
                }

             }
             else
             {
               // Password is incorrect
               $aTplErr['error_text']  = $CNK->__aWords['L_sett_err_passw_wrong'];
               $aTplWrp['message'] = $CNK->__oDisplay->EvalTpl( $aTplErr, '', $mRidErr );
             }
          }

       } // if ( $sAction == 'update' )

       // view
       $hQRid = $CNK->__oDb->ExecQuery( "SELECT * FROM pf_admins WHERE adm_id='{$sId}'" );
       if ( $aRes = $CNK->__oDb->Fetch($hQRid) )
       {
          $sLogin = $aRes['adm_login'];
       }
       $aTplWrp['site_name']     = $CNK->__aCfg['SITE_NAME'];
       $aTplWrp['default_login'] = $sLogin;
       $aTplWrp['admin_email']   = $CNK->__aCfg['MAILER_ADMIN'];

       $this->__aTplData['L_page_title']          = $CNK->__aWords['L_page_sett'];
       $this->__aTplData['T_idx_wrapper_content'] = $CNK->__oDisplay->EvalTpl( $aTplWrp, '', $mRidWrp );
    }
    //------------------------------------------------------------------------


    function _LoadInfo( $sPhraseKey, $aVars )
    {
       global $CNK;
       $mRid = $CNK->__oDisplay->LoadTpl( 'idx_info' );
       $aTpl['L_info'] = $CNK->__aWords[$sPhraseKey];
       $aTpl['L_info'] = preg_replace( "/<#(.+?)#>/ies", "\$aVars['\\1']", $aTpl['L_info'] );
       return $CNK->__oDisplay->EvalTpl( $aTpl, '', $mRid );
    }

    function _LoadNote( $sDocName )
    {
       global $CNK;
       $mRid = $CNK->__oDisplay->LoadTpl( 'idx_note' );
       $aTpl['L_note'] = $CNK->__oI18n->LoadDoc( $sDocName );

       return $CNK->__oDisplay->EvalTpl( $aTpl, '', $mRid );
    }

    ////////////////////////////////////////////////////////////////////////////
    //                                                                        //
    //                    ADMIN AREA END                                      //
    //                                                                        //
    ////////////////////////////////////////////////////////////////////////////


    //----------------------------------------------------//
    // Autoconfiguration for module IDX                   //
    //----------------------------------------------------//
    Function _ThisAutoConfig()
    {
        global $CNK;
        $this->_ThisModStructure();
        $this->_ThisModConf( $sFlag='configure' );
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
    function _ThisModConf( $sFlag='configure' )
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
