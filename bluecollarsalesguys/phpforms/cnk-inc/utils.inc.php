<?php

    // E-mail format checking function
    function isCorrectEmail( $sEmailString )
    {
       $bResult = 0;
       $sPat = "/^[a-z0-9_\.\-]+@([a-z0-9][a-z0-9-]+\.)+[a-z]{2,4}$/i";
                                                                                
       if ( preg_match( $sPat, $sEmailString ) ) $bResult = 1;
       else $bResult = 0;
                                                                                
       return $bResult;
    }

    // Replaces \n with \r\n
    function doCorrectMtpl( $sMtpl )
    {
       $sResult = '';
       $sResult = str_replace( "\n", "\r\n", $sMtpl );
       return $sResult;
    }

    // Get file name and extension
    function GetFileNameParts( $sFileName )
    {
       $aRes = array();

       $sName = preg_replace( "/(.*)\..*$/", "\\1", $sFileName );

       $aRes  = pathinfo( $sFileName );
       if ( isset($aRes['extension']) ) $sExt = $aRes['extension'];
       else $sExt = '';

       return array( 'name'=>$sName, 'ext'=>$sExt );
    }


    function xmail( $email_address, $email_cc, $email_bcc, $email_from, 
                    $subject, $msg, $attach_filepath, $attach_types, 
                    $want_attach, $attach_realname )
    {
       $b = 0;
       $mail_attached = "";
       $boundary = "000XMAIL000";
       if (count($attach_filepath)>0 && $want_attach)
       {
          for ($a=0;$a<count($attach_filepath);$a++)
          {
             if ($fp=fopen($attach_filepath[$a],"rb"))
             {
                $file_name   = basename( $attach_filepath[$a] );
                $content[$b] = fread( $fp, filesize($attach_filepath[$a]) );
//              $mail_attached.="--".$boundary."\n"."Content-Type: $attach_types[$a]; name=\"$file_name\"\n"."Content-Transfer-Encoding: base64\n"."Content-Disposition: inline; filename=\"$file_name\"\n\n".chunk_split(base64_encode($content[$b]))."\n";
                $mail_attached .="--".$boundary."\n"."Content-Type: $attach_types[$a]; name=\"$file_name\"\n"."Content-Transfer-Encoding: base64\n"."Content-Disposition: inline; filename=\"$attach_realname[$a]\"\n\n".chunk_split(base64_encode($content[$b]))."\n";
                $b++;
                fclose($fp);
             }
             else
             {
                echo "Can't send an attachment.";
             }  
          }
          $mail_attached .= "\n";
//        $mail_attached .= "--".$boundary."\n";
          $add_header ="MIME-Version: 1.0\n"."Content-Type: multipart/mixed; boundary=\"$boundary\"; Message-ID: <".md5($email_from)."@domain.net>";
          $mail_content="--".$boundary."\n"."Content-Type: text/plain; charset=\"iso-8859-1\"\n"."Content-Transfer-Encoding: 8bit\n\n".$msg."\n\n".$mail_attached;
          return mail( $email_address,$subject,$mail_content,"From: ".$email_from."\nCC: ".$email_cc."\nBCC: ".$email_bcc."\nErrors-To: ".$email_from."\n".$add_header );
       }
       else
       {
          return mail( $email_address,$subject,$msg,"From: ". $email_from. "\nErrors-To: ".$email_from );
       }
    }


    function _GetDirCfgFiles( $sDir )
    {
       $aFiles = array();
       $hDir   = '';
       $sFile  = '';
       $aMatches = array();

       @clearstatcache();
       if ( is_dir($sDir) )
       {
          if ( $hDir = @opendir( $sDir ) )
          {
             while ( false !== ( $sEntry = readdir( $hDir ) ) )
             {
                $sFile = "{$sDir}/{$sEntry}";
                if ( is_file($sFile) && $sEntry!='.' && $sEntry!='..' )
                  if ( preg_match( "/\.cfg$/i", $sFile, $aMatches ) )
                     $aFiles[] = $sFile;
             }
          }
          @closedir( $hDir );
       }

       return $aFiles;
    }


    // Reads all config files (*.cfg) in specified dir
    // Uses _GetDirCfgFiles() function
    // v1.0.7
    function _LoadConfig( $sDir )
    {
       $aCfg  = $aTmp = array();
       $aCfgFiles = array();
       $aCfgFiles = _GetDirCfgFiles( $sDir );

       $hFile = '';

       for ( $i=0; $i<count($aCfgFiles); $i++ )
       {
          if ( $hFile = fopen( $aCfgFiles[$i], 'r' ) )
          {
             while( !feof( $hFile ) )
             {
                $sLine = trim( fgets( $hFile, 0xFFF ) );
                if ( $sLine != '' )
                  if ( substr($sLine, 0, 1)!=';' && substr($sLine, 0, 1)!='#' )
                  {
                     $aTmp    = explode( '=', $sLine, 2 );
                     $aTmp[0] = trim($aTmp[0]);
                     if ( !isset($aTmp[1]) ) $aTmp[1] = '';
                     $aTmp[1] = trim($aTmp[1]);
                     $aCfg[$aTmp[0]] = $aTmp[1];
                  }
             }

             fclose( $hFile );
          }
       }

       return $aCfg;
    }

/*
    function _LoadDbConfig()
    {


    }
*/

    function BuildColorsSelect( $color_id = '' )
    {
        global $CNK;

        $sResult = '';
        $sql = "SELECT color_id, color_name, color_rgb FROM pf_colors ORDER BY color_name";
        $mRid = $CNK->__oDb->ExecQuery( $sql );
        while ( $aRes = $CNK->__oDb->Fetch( $mRid ) )
        {
            if ( $aRes['color_id'] == $color_id) $sSelected=' selected';
             else $sSelected='';
            $sResult .= "<option value='{$aRes['color_id']}^{$aRes['color_rgb']}'$sSelected>{$aRes['color_name']}</option>\n";
        }

        return $sResult;
    }

//????
    function BuildTypeSelect( $type = '' )
    {
        global $CNK;
        $sResult  = '';
        if ($type=='0') $sSelected=' selected'; else $sSelected='';
        $sResult .= "<option value='0'$sSelected>{$CNK->__aWords['L_idx_form_single_page']}</option>\n";

        if ($type=='1') $sSelected=' selected'; else $sSelected='';
        $sResult .= "<option value='1'$sSelected>{$CNK->__aWords['L_idx_form_multi_page']}</option>\n";

        return $sResult;
    }

    // Builds select options list
    function GetSelect( $aData, $sDefault, $nMaxLen=255 )
    {
       $sResult = '';
       if ( is_array($aData) )
          foreach ( $aData as $key=>$val )
          {
             $sSelected = ( $key==$sDefault ) ? 'selected' : '';
             $val = CropText( $val, $nMaxLen );
             $sResult .= "<option value='$key' $sSelected>$val</option>\n";
          }
       return $sResult;
    }

    function GetPagesArray( $sPageId )
    {
       global $CNK;
       $aResult = array();
       $nPage   = 0;
       $CNK->__oDb->ExecQuery("SELECT pg_frm_id FROM pf_pages WHERE pg_id='$sPageId'");

       if ( $aRes = $CNK->__oDb->Fetch() )
       {
          $CNK->__oDb->ExecQuery("SELECT pg_id, pg_num, pg_title FROM pf_pages WHERE pg_frm_id='{$aRes['pg_frm_id']}' ORDER BY pg_num ASC");
          while ( $aRes1 = $CNK->__oDb->Fetch() )
          {
             $nPage++;
             $aResult[ $aRes1['pg_id'] ] = ( $nPage ) . " :: " . $aRes1['pg_title'];
          }
       }

       return $aResult;
    }

    function GetTypesArray()
    {
       global $CNK;
       $aResult = array();
       $CNK->__oDb->ExecQuery("SELECT type_id, type_name FROM pf_types");
       while ( $aRes = $CNK->__oDb->Fetch() )
        $aResult[ $aRes['type_id'] ] = $aRes['type_name'];

       return $aResult;
    }


    function BuildDestSelect( $Dest = '' )
    {
        global $CNK;
        $sResult  = '';
        if ($Dest=='0') $sSelected=' selected'; else $sSelected='';
        $sResult .= "<option value='0'$sSelected>{$CNK->__aWords['L_frmopts_form_dest_email']}</option>\n";

        if ($Dest=='1') $sSelected=' selected'; else $sSelected='';
        $sResult .= "<option value='1'$sSelected>{$CNK->__aWords['L_frmopts_form_dest_db']}</option>\n";

        if ($Dest=='2') $sSelected=' selected'; else $sSelected='';
        $sResult .= "<option value='2'$sSelected>{$CNK->__aWords['L_frmopts_form_dest_email_db']}</option>\n";

        return $sResult;
    }

    function BuildSQLSelect( $sTable, $sFieldVal, $sFieldKey, $sDefault = '', $nMaxLen = 40)
    {
       global $CNK;
       $sResult = "<option value=''></option>\n";

       $sSql = "SELECT {$sFieldVal}, {$sFieldKey} FROM {$sTable}";
       $mQRid1 = $CNK->__oDb->ExecQuery($sSql);
       while ( $aRes = $CNK->__oDb->Fetch($mQRid1) )
       {
          $sSelected = ( $aRes[$sFieldKey]==$sDefault ) ? ' selected': '';
          $sKey = $aRes[$sFieldKey];
          $sVal = CropText( $aRes[$sFieldVal], $nMaxLen );
          $sResult .= "<option value='$sKey' $sSelected>{$sVal}</option>\n";
       }
       return $sResult;
    }


    // Get Pages List of Current Form
    function GetPagesList( $sFormId )
    {
       global $CNK;
       $aIds = $aTitles = array();

       $CNK->__oDb->ExecQuery("SELECT pg_id, pg_title FROM pf_pages WHERE pg_frm_id='{$sFormId}' ORDER BY pg_num ASC");
       while ( $aRes = $CNK->__oDb->Fetch() )
       {
          $aIds[] = $aRes['pg_id']; $aTitles[] = $aRes['pg_title'];
       }
       return array( $aIds, $aTitles );
    }

    // Get Pages Ids List of Current Form
    function GetPagesIds( $sFormId )
    {
       global $CNK;
       $aResult = array();

       $mQRid = $CNK->__oDb->ExecQuery("SELECT pg_id FROM pf_pages WHERE pg_frm_id='{$sFormId}' ORDER BY pg_num ASC");
       while ( $aRes = $CNK->__oDb->Fetch($mQRid) )
         $aResult[] = $aRes['pg_id'];
       return $aResult;
    }

    function GetFormName( $sFormId )
    {
       global $CNK;
       $sResult = '';

       $CNK->__oDb->ExecQuery("SELECT frm_name FROM pf_forms WHERE frm_id='{$sFormId}'");
       if ( $aRes = $CNK->__oDb->Fetch() ) $sResult = $aRes['frm_name'];

       return $sResult;
    }

    function GetDefaultString( $sFieldId )
    {
       global $CNK;
       $sResult = '';
       $CNK->__oDb->ExecQuery("SELECT it_id, it_title FROM pf_items WHERE it_fld_id='$sFieldId'");
       if ( $aRes = $CNK->__oDb->Fetch() )
       {
          $sResult = $aRes['it_title'];
       }
       return $sResult;
    }


//// ?????????????
    function GetDefaultItems( $sFieldId )
    {
       global $CNK;
       $aResult = array();
       $CNK->__oDb->ExecQuery("SELECT it_id, it_title, it_default FROM pf_items WHERE it_fld_id='$sFieldId' ORDER BY it_num ASC");
       while ( $aRes = $CNK->__oDb->Fetch() )
       {
          $aResult[ $aRes['it_id'] ] = array( $aRes['it_default'], $aRes['it_title'] );
       }
       return $aResult;
    }

    // if $mVar is undefined, return mDefault. Otherwise return $mVar
    function SetDefault( $mVar, $mDefault='' )
    {
       if ( !isset($mVar) ) return $mDefault; 
        else return $mVar;
    }

    // if $mVar is not numeric, return mDefault. Otherwise return $mVar
    function MakeInteger( $mVar, $nDefault=0 )
    {
       if ( !is_numeric($mVar) ) return $nDefault; 
        else return (int)(floor($mVar));
    }

    // somelongtext...
    function CropText( $sText, $nMaxLength )
    {
       if ( strlen($sText)>$nMaxLength ) return substr($sText, 0, $nMaxLength) . "...";
         else return $sText;
    }

    // Create short unique id
    function ShortUniqueId( $len=10 )
    {
       mt_srand( (double)microtime() * 1000000 );
       $id = substr( md5(uniqid(mt_rand())), 0 ,$len  );
       return $id;
    }
    
?>