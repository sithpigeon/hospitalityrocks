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

class DEBUG
{
    
    var $__dCTimerStart = 0;
    var $__dCTimerStop  = 0;
    var $__iCtimer      = 0;
    var $__aErrors      = Array();  
    
    //-------------------------------------------------//
    // @ Class constructor                             //
    //-------------------------------------------------//
    
    Function DEBUG()
    {
    }

    //----------------------------------------------------//
    // CTimer operations                                  //
    //----------------------------------------------------//
    function GetMicroTime()
    {
        static $_iUSec = 0;
        static $_mSec  = 0;

        list($_iUSec, $_mSec) = explode( ' ', microtime() ); 
        return ( (float)$_iUSec + (float)$_mSec  );
    }

    function StartTimer()
    {
        $this->__dCTimerStart = $this->GetMicroTime();
        return true;
    }

    function StopTimer()
    {
        return round( $this->GetMicroTime() - $this->__dCTimerStart, 5 );
    }

    //----------------------------------------------------//
    // Get list of included files                         //
    //----------------------------------------------------//    
    Function LoadedFiles()
    {
       return @get_included_files();
    }   
    
    //----------------------------------------------------//
    // Local erorr                                        //
    //----------------------------------------------------//
    Function _DebugError()
    {
        global $CONF;
        static $_sErrBlock;
        static $_dExecTime = 0;
        static $c;
        
        /*
        error_reporting() 
        level constants and bit values
        +-------+-------------------+
        | value | constant          |
        +-------+-------------------+
        | 1     | E_ERROR           |
        | 2     | E_WARNING         |
        | 4     | E_PARSE           |
        | 8     | E_NOTICE          |
        | 16    | E_CORE_ERROR      |
        | 32    | E_CORE_WARNING    |
        | 64    | E_COMPILE_ERROR   |
        | 128   | E_COMPILE_WARNING |
        | 256   | E_USER_ERROR      |
        | 512   | E_USER_WARNING    |
        | 1024  | E_USER_NOTICE     |
        | 2047  | E_ALL             |
        +-------+-------------------+
        */
        
                
        for ($c=0;$c<count($this->__aErrors);$c++)
        {           
            if ( $this->__aErrors[$c]['err_type'] == 1   ||
               //$this->__aErrors[$c]['err_type'] == 2   ||
               //$this->__aErrors[$c]['err_type'] == 8   ||
                 $this->__aErrors[$c]['err_type'] == 16  || 
               //$this->__aErrors[$c]['err_type'] == 32  ||
                 $this->__aErrors[$c]['err_type'] == 64  ||
               //$this->__aErrors[$c]['err_type'] == 128 ||
                 $this->__aErrors[$c]['err_type'] == 256                 
               )
            {
               $_sErrBlock .= "
                 <strong>Error type : </strong> {$this->__aErrors[$c]['err_type']}<br>
                 <strong>Error text : </strong><br>
                 {$this->__aErrors[$c]['err_text']}
                 <br>
                 <strong>Error file : </strong> {$this->__aErrors[$c]['err_file']}<br>
                 <strong>Error line : <strong>  {$this->__aErrors[$c]['err_line']}<br>
               "; 
                
           }
           else
           {
               return;
           }            
        }       
        
        $_dExecTime = DEBUG::StopTimer();
        
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
                If this does not fix the error, you can contact the  
                <a href='mailto:{$_SERVER['SERVER_ADMIN']}?subject=System+Error'>site administrator</a>
                <br><br>
                <table width='300' cellSpacing='0' cellPadding='0' border='0'>
                <tr>
                 <td style='border: 1px dotted #dddddd; background-color: #f7f7f7; padding: 10px;'>
                   {$_sErrBlock}
                 <td>
                </tr></table>               
                <br>
                Script execution time: {$_dExecTime}
                </blockquote>
                </body>
                </html>
               ";    
        exit;
    }
    
    //----------------------------------------------------//
    // Public erorr                                       //
    //----------------------------------------------------//
    Function Error( $sErrMessage )
    {
        global $CONF;
        static $_sErrDate  = "";
        static $_sOutPut   = "";
        static $_dExecTime = 0;
        
        
        $_sErrDate = "Date: ".date("l dS of F Y h:i:s A");
        $_dExecTime = DEBUG::StopTimer();
        
        $_sOutPut = "<html>
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
                <a href='mailto:{$CONF['ADMIN_EMAIL']}?subject=System+Error'>
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
                <br>
                Script execution time: {$_dExecTime}
                </blockquote>
            </body>
            </html>
               ";
               
    
        echo($_sOutPut);
        die("");    
    }
    
    //----------------------------------------------------//
    // SQL erorr                                          //
    //----------------------------------------------------//
    Function SqlError( $sErrMessage )
    {
        global $CONF;
        static $_sErrDate  = "";
        static $_sOutPut   = "";
        static $_dExecTime = 0;
        
        
        $_sErrDate = "Date: ".date("l dS of F Y h:i:s A");
        $_dExecTime = DEBUG::StopTimer();
        
        $_sOutPut = "<html>
            <head>
            <title>SQL Error</title>
            <style>
                P,BODY,TD { font-family:arial,sans-serif; font-size:11px; }
            </style>
            </head>
            
            <body>
                &nbsp;<br><br>
                <blockquote>
                <h2 style='color: red;'>There appears to be an a SQL error.</h2>
                You can try to refresh the page by clicking 
                <a href=\"javascript:window.location=window.location;\">here</a>.
                <br> 
                If this does't fix the error, please, contact the site 
                <a href='mailto:{$CONF['ADMIN_EMAIL']}?subject=SQL+Error'>
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
                <br>
                Script execution time: {$_dExecTime}
                </blockquote>
            </body>
            </html>
               ";
               
    
        echo($_sOutPut);
        die("");    
    }
    
}

?>
