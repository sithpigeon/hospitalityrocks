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

class DB
{

    // Global params
    var $__sCnId         = '';
    var $__aoDb          = array();
    var $__bPersist      = false;
    
    // Tables' names params
    var $__sTblPrx       = 'cnk_';
    
    // Queries params
    var $__sQrId         = '';
    var $__iQrCnt        = 0;
    var $__aQueriesCatch = array();
    
    // Errors
    var $__aErrors       = array();


    Function DB()
    {
      // Just empty constructor
    }
    
    //----------------------------------------------------//
    // Class factory			                          //
    //----------------------------------------------------//
    Function &Loader( $sType )
    {
        static $_sDriver    = "";
        static $_oObj       = "";
        static $_sClassName = "";
        
        $_sDriver = DRIVERS . $sType . '.drv' . PHP_EXT;
        define( 'F_DRIVER', $_sDriver );
        @require( F_DRIVER );
        
        $_sClassName = 'DB_'.$sType;
        
        @$oObj =& new $_sClassName;
        
        return $oObj;
    }    

}

?>
