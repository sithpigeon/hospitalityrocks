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

class DISPLAY
{    
    var $__bDebugMode    = false;
    var $__aBuffer       = array();
    var $__aSourceCache  = array();
    var $__sEvaledCache  = '';
    var $__sCacheId      = '';
    var $__sLastInsertId = '';
    
    //----------------------------------------------------//
    // Init class and set debug mode to TRUE/FALSE [opt]  //
    //----------------------------------------------------//   
    Function DISPLAY( $bDebugMode=false )
    {        
        $this->__bDebugMode = $bDebugMode;        
    }
    
    //----------------------------------------------------//
    // Set debug mode to TRUE/FALSE                       //
    //----------------------------------------------------//    
    Function SetDebugMode( $bDebugMode=false )
    {
        $this->__bDebugMode = $bDebugMode;   
    }
    
    //----------------------------------------------------//
    // Public accessible LoadTpl                          //
    //----------------------------------------------------//
    Function LoadTpl( $sTplName='' )
    {
        if ( $sTplName == '' )
        {
            return $this->__sLastInsertId;   
        }
        else
        {
            DISPLAY::_LoadTpl( $sTplName );
            return $this->__sLastInsertId;
        }           
    }
    
    //----------------------------------------------------//
    // Local accessible LoadTpl - really loads template   //
    //----------------------------------------------------//
    Function _LoadTpl( $sTplName )
    {
        global $CNK;
        static $_sTplFile;
        static $_fStream;
        
        $this->__sLastInsertId = $CNK->__oStd->my_UniqId();
        $this->__aSourceCache[ $this->__sLastInsertId ] = '';
        
        $_sTplFile = $CNK->__aModules[ $CNK->__sWorkingModule ] . 'display/' . $sTplName . $CNK->__aCfg["TPL_EXT"];
        
        if ( !file_exists( $_sTplFile ) )
        {
            $CNK->__oDebug->Error( ERROR_DISPLAY_NO_TPL_FILE.'<br>'.$_sTplFile );   
        }
        
        $_fStream = @fopen( $_sTplFile, "r" );
        while ( !feof( $_fStream ) )
        {
            $this->__aSourceCache[ $this->__sLastInsertId ] .= @fgetc( $_fStream );
        }
        @fclose( $_fStream );
    }

    //----------------------------------------------------//
    // Public accessible "LoadTplFile()"                  //
    // ! For phpForms project only !                      //
    // ! @$sLayFile must be a full path to file !         //
    //----------------------------------------------------//
    Function LoadTplFile( $sLayFile )
    {
        global $CNK;
        /* {{{ Read LAyout file }}} */
        if ( !file_exists( $sLayFile ) )
        {
            $CNK->__oDebug->Error( ERROR_DISPLAY_NO_FILE.'<br>'.$sLayFile );
        }

        $_sCache = '';
        $_fStream = @fopen( $sLayFile, "r" );
        while ( !feof( $_fStream ) )
        {
             $_sCache .= @fgetc( $_fStream );  
        }
        @fclose( $_fStream );
        return $_sCache;
    }

    //----------------------------------------------------//
    // Public accessible "EvalTplFile()"                  //
    // ! For phpForms project only !                      //
    // ! @$sLayFile must be a full path to file !         //
    //   @$aData contains variables to replace !          //
    //----------------------------------------------------//
    Function EvalTplFile( $aData, $sText, $bOut=False )
    {
        global $CNK;
        /* {{{ Parse vars }}} */
        $sText = preg_replace( "/<#(.+?)#>/ies", "\$aData['\\1']", $sText );
        if ( (bool)$bOut ) echo $sText;
        else return $sText;
    }

    //----------------------------------------------------//
    // Public accessible "EvalTplFile()"                  //
    // ! For phpForms project only !                      //
    // ! @$sLayFile must be a full path to file !         //
    //   @$aData contains variables to replace !          //
    //----------------------------------------------------//
    Function LoadEvalTplFile( $aData, $sLayFile, $bOut=False )
    {
        global $CNK;
        $_sCache = '';

        /* {{{ Read Layout file }}} */
        if ( !file_exists( $sLayFile ) )
        {
            $CNK->__oDebug->Error( ERROR_DISPLAY_NO_FILE.'<br>'.$sLayFile );
        }

        $_sCache = '';
        $_fStream = @fopen( $sLayFile, "r" );
        while ( !feof( $_fStream ) )
        {
             $_sCache .= @fgetc( $_fStream );  
        }
        @fclose( $_fStream );

        /* {{{ Parse vars }}} */
        $_sCache = preg_replace( "/<#(.+?)#>/ies", "\$aData['\\1']", $_sCache );
        if ( (bool)$bOut )
        {
            echo $_sCache;   
        }
        else
        {
            return $_sCache;
        }
    }

    //----------------------------------------------------//
    // Public accessible EvalTpl                          //
    //----------------------------------------------------//
    Function EvalTpl( $aData, $sTplName='', $sCacheId='', $bOut=False )
    {
        /*{{{ Is $sData is really an array? }}}*/
        if ( !is_array( $aData ) )
        {
            return false;   
        }
        
        /*{{{ Do we need to load template from file or use cache? }}}*/
        if ( $sTplName != '' && $sCacheId == '' )
        {
            DISPLAY::_LoadTpl( $sTplName );
            $this->__sEvaledCache = DISPLAY::_PreParseVars( $aData, 
                                                            $this->__sLastInsertId 
                                                          );   
        }
        elseif ( $sTplName == '' && $sCacheId != '' )
        {
            $this->__sEvaledCache = DISPLAY::_PreParseVars( $aData, $sCacheId );
        }
        else 
        {
            $this->__sEvaledCache = DISPLAY::_PreParseVars( $aData, 
                                                            $this->__sLastInsertId 
                                                          );
        } 
        
        /*{{{ Do we need to make output or return as string? }}}*/
        if ( (bool)$bOut )
        {
           echo $this->__sEvaledCache; 
        }
        else
        {
           return $this->__sEvaledCache; 
        }     
        
    }
       
    //----------------------------------------------------//
    // Public accessible EvalTpl without real parsing     //
    //----------------------------------------------------//
    Function GetTplSource( $sTplName='', $sCacheId='', $bOut=false )
    {
        /*{{{ Do we need to load template from file or use cache? }}}*/
        if ( !empty($sTplName) && empty($sCacheId) )
        {
            DISPLAY::_LoadTpl( $sTplName );
            $this->__sEvaledCache = DISPLAY::_NoParseVars( $this->__sLastInsertId );   
        }
        elseif ( empty($sTplName) && !empty($sCacheId) )
        {
            $this->__sEvaledCache = DISPLAY::_NoParseVars( $sCacheId );
        }
        else 
        {
            $this->__sEvaledCache = DISPLAY::_NoParseVars( $this->__sLastInsertId );
        } 
        
        /*{{{ Do we need to make output or return as string? }}}*/
        if ( (bool)$bOut )
        {
           echo $this->__sEvaledCache; 
        }
        else
        {
           return $this->__sEvaledCache; 
        }     
        
    }
    
    Function LoopTpl( $sTplName='', $sCacheId='', $bOut=false )
    {
        /*{{{ Depricated, 'cause no sense. }}}*/
    }
    
    //----------------------------------------------------//
    // Variables parsing                                  //
    //----------------------------------------------------//    
    Function _PreParseVars( $aData, $sCacheId )
    {
        if ( !(bool)$this->__bDebugMode )
        {
           return DISPLAY::_ParseVars( $aData, $sCacheId ); 
        }
        else
        {
           return DISPLAY::_NoParseVars( $sCacheId ); 
        }   
    }
    
    Function _ParseVars( $aData, $sCacheId )
    {
        return preg_replace( "/<#(.+?)#>/ies", "\$aData['\\1']", 
                             $this->__aSourceCache[ $sCacheId ] 
                           );
    }
    
    Function _NoParseVars( $sCacheId )
    {
        return $this->__aSourceCache[ $sCacheId ];
    }

}

?>