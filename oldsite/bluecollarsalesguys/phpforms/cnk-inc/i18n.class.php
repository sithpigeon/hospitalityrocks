<?php

class LANG
{

    var $__sPackageDir     = '';
    var $__sDefaultPackage = '';
    var $__sActivePackage  = '';
    var $__aSupported      = array();

    Function Lang( $sLang )
    {
        global $aCFG,$oDEBUG;

        $this->__sDefaultPackage = $aCFG["LANG_DEFAULT"];

        $this->__aSupported = explode( ',', $aCFG["LANG_SUPPORTED"], 2 );
        if ( !in_array( $sLang, $this->__aSupported ) )
        {
            $this->__sActivePackage = $this->__sDefaultPackage;
        }
        else
        {
            $this->__sActivePackage = $sLang;
        }

        $this->__sPackageDir = I18N . $this->__sActivePackage . '/';

        if ( !(bool)LANG::_IsPackageDirExists() )
        {
            $oDEBUG->Error( ERROR_I18N_NO_PACKAGE_DIR );
        }

        // Load common words
        LANG::_LoadDefaultWors();

    }

    Function _IsPackageDirExists()
    {
        return file_exists($this->__sPackageDir);
    }


    Function _LoadDefaultWors()
    {
        global $CNK;
        static $_sPackage;

        $_sPackage = $this->__sPackageDir . 'global.i18n' . PHP_EXT;
        define( 'F_LANG_DEF_PACKAGE', $_sPackage );
        @require_once( F_LANG_DEF_PACKAGE );
        $CNK->__aWords = $LANG;

        return true;
    }

    Function LoadSection( $sSection='' )
    {
        global $CNK;
        static $_sPackage;

        if ( !empty( $sSection ) )
        {
            $_sPackage = $this->__sPackageDir . $sSection . '.i18n' . PHP_EXT;
        }
        else
        {
            $_sPackage = $this->__sPackageDir . $CNK->__sWorkingModule . '.i18n' . PHP_EXT;
        }

        if ( !file_exists($_sPackage) )
        {
            $CNK->__oDebug->Error( ERROR_I18N_NO_PACKAGE_FILE );
        }

        define( 'F_LANG_SECT_PACKAGE', $_sPackage );
        @require_once( F_LANG_SECT_PACKAGE );
        $CNK->__aWords = array_merge( (array)$CNK->__aWords, (array) $LANG );
        return true;
    }

    Function LoadDoc( $sTextFile='' )
    {
        global $aCFG, $CNK;

        $sText = '';

        $sDocFile = I18N .
                    $this->__sActivePackage .'/'.
                    $aCFG["LANG_DOCS_DIR"] . '/'.
                    $sTextFile . $aCFG["LANG_DOCS_EXT"];

        if ( file_exists( $sDocFile ) )
        {
            $_fStream = @fopen( $sDocFile, "r" );
            while ( !feof( $_fStream ) )  $sText .= @fgetc( $_fStream );
            @fclose( $_fStream );
        }
        return $sText;
    }

}


?>