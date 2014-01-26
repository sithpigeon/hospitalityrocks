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

/*
SQL table description:

mysql> desc cnk_sessions;
+-------------------+-------------+------+-----+---------+-------+
| Field             | Type        | Null | Key | Default | Extra |
+-------------------+-------------+------+-----+---------+-------+
| session_id        | varchar(64) |      | PRI |         |       |
| session_data      | text        |      |     |         |       |
| session_host      | varchar(64) |      |     |         |       |
| session_agent     | text        |      |     |         |       |
| session_ip        | varchar(16) |      |     |         |       |
| session_lastclick | varchar(64) |      |     |         |       |
+-------------------+-------------+------+-----+---------+-------+

*/

class SESSION
{
    /* {{{ Storing method: Database [ db ] / Files [ fs ] }}} */
    var $__sMethod = 'fs';
    
    /* {{{ Use cookies: true / false }}} */
    var $__bCookie = true;
    
    /* {{{ Storage: FS path or table name }}} */
    var $__sSessSt = '';
    
    /* {{{ Session Id & stored data }}} */
    var $__sSessId = '';
    var $__aStored = Array();
    
    //-------------------------------------------------//
    // Class constructor                      		   //
    //-------------------------------------------------//    
    Function SESSION( $sMethod = '' )
    {
       global $CNK;
       static $_aSqlRes = array();
       static $_aOurDbTables = array();
       static $_sTblKey = "";
       static $_sSQL = "";
       static $_iKey = "";
        
       if ( !empty( $sMethod ) )
       {
            $this->__sMethod = $sMethod;   
       }
       
       if ( $this->__sMethod == 'fs' )
       {
            $this->__sSessSt = TOP . '.cnk-sessions/';
            if ( !file_exists( $CNK->__oStd->RelativeToReal( $this->__sSessSt ) ) )
            {
                $CNK->__oLs->CreateDir( 
                                      $CNK->__oStd->RelativeToReal( $this->__sSessSt ), 
                                      "0777" 
                                    );   
            }               
       }
       elseif ( $this->__sMethod == 'db' )
       {
            $this->__sSessSt = $CNK->__oDb->__sTblPrx.'sessions';            
            $_sTblKey = "Tables_in_{$CNK->__aCfg['DATABASE_NAME']}";
            
            $CNK->__oDb->ExecQuery("SHOW TABLES");
            while ( $_aSqlRes = $CNK->__oDb->Fetch() )
            {
                array_push( $_aOurDbTables, $_aSqlRes[ $_sTblKey ] );
            }
            
            $_iKey = array_search( $this->__sSessSt, $_aOurDbTables );
          
            if ( ( gettype( $_iKey ) == 'boolean' ) && ( !(bool)$_iKey ) )
            {
                $_sSQL = "
                CREATE TABLE {$this->__sSessSt} ( 
                session_id        VARCHAR(64)  NOT NULL,
                session_data      TEXT         NULL,
                session_host      VARCHAR(255) NULL,
                session_agent     TEXT         NULL,
                session_ip        VARCHAR(23)  NULL,
                session_lastclick VARCHAR(64)  NULL,
                PRIMARY KEY ( session_id )
                )";
                $CNK->__oDb->ExecQuery( $_sSQL );
            }
       }
    }
    
    //-------------------------------------------------//
    // Start session                          		   //
    //-------------------------------------------------//    
    Function SessionStart()
    {
        global $CNK;
        static $_sSQL = '';
        
        $this->__sSessId = $CNK->__oStd->my_UniqId();        
        
        if ( $this->__sMethod == 'fs' )
        {
            $CNK->__oLs->CreateFile( 
                  $CNK->__oStd->RelativeToReal( $this->__sSessSt ).$this->__sSessId.'.sid',
                  "0600" );
        }
        elseif ( $this->__sMethod == 'db' )
        {
            $_sSQL = "INSERT INTO {$this->__sSessSt} 
                      (session_id, session_data, session_host, 
                       session_agent, session_ip, session_lastclick ) 
                      VALUES 
                      ( '{$this->__sSessId}',
                        '',
                        '{$CNK->__aIn['USER_HOST']}',
                        '{$CNK->__aIn['USER_AGENT']}',
                        '{$CNK->__aIn['IP_ADDRESS']}',
                        '{$CNK->__aIn['LAST_CLICK']}' )
                     ";            
            
            $CNK->__oDb->ExecQuery( $_sSQL );            
        }   
        
        SESSION::SetVars( "session_host"     , $CNK->__aIn['USER_HOST']  );
        SESSION::SetVars( "session_agent"    , $CNK->__aIn['USER_AGENT'] );
        SESSION::SetVars( "session_ip"       , $CNK->__aIn['IP_ADDRESS'] );
        SESSION::SetVars( "session_lastclick", $CNK->__aIn['LAST_CLICK'] );     
        
        return $this->__sSessId;
    }
    
    //-------------------------------------------------//
    // Update session                        		   //
    //-------------------------------------------------//    
    Function SessionUpdate( $sSid )
    {
        global $CNK;
        static $_sSQL='';
        
        $this->__sSessId = $CNK->__oStd->my_UniqId();
        
        if ( $this->__sMethod == 'fs' )
        {
            $CNK->__oLs->RenameFile(
                  $CNK->__oStd->RelativeToReal( $this->__sSessSt ).$sSid.'.sid',
                  $CNK->__oStd->RelativeToReal( $this->__sSessSt ).$this->__sSessId.'.sid'
                  );
            SESSION::SetVars( "session_lastclick", $CNK->__aIn['LAST_CLICK'] );
        }
        elseif ( $this->__sMethod == 'db' )
        {
            $_sSQL = "UPDATE {$this->__sSessSt} 
                      SET session_id = '{$this->__sSessId}',
                          sessio_lastclick = '{$CNK->__aIn['LAST_CLICK']}'
                      WHERE session_id = '{$sSid}'
                     ";
            $CNK->__oDb->ExecQuery( $_sSQL );
            SESSION::SetVars( "session_lastclick", $CNK->__aIn['LAST_CLICK'] );
        } 

        return $this->__sSessId;       
    }
    
    //-------------------------------------------------//
    // Close session                          		   //
    //-------------------------------------------------//    
    Function SessionClose()
    {
        global $CNK;
        static $_sSQL='';
        
        if ( $this->__sMethod == 'fs' )
        {
            $CNK->__oLs->RemoveFiles( 
                  $CNK->__oStd->RelativeToReal( $this->__sSessSt ).$this->__sSessId.'.sid' );
        }
        elseif ( $this->__sMethod == 'db' )
        {
            $_sSQL = "
            DELETE FROM {$this->__sSessSt} 
            WHERE session_id = '{$this->__sSessId}'
            ";
            $CNK->__oDb->ExecQuery( $_sSQL );
        }
        
        return true;   
    }
    
    //-------------------------------------------------//
    // Add variables to stored hash          		   //
    //-------------------------------------------------//    
    Function SetVars( $sVarName, $sVarValue )
    {
       global $CNK;
       static $_fStream;
       static $_sSQL='';
       static $_sSerialized='';
       
       $this->__aStored[ $sVarName ] = $sVarValue;
      
       if ( $this->__sMethod == 'fs' )
       {
           $_fStream = @fopen( 
                       $CNK->__oStd->RelativeToReal( $this->__sSessSt ).$this->__sSessId.'.sid',
                       "w"
                       );
           @fputs( $_fStream, serialize($this->__aStored) );
           @fclose( $_fStream );
       }
       elseif ( $this->__sMethod == 'db' )
       {
           $_sSerialized = serialize($this->__aStored); 
           $_sSQL = "UPDATE {$this->__sSessSt} 
           SET session_data = '{$_sSerialized}' 
           WHERE session_id = '{$this->__sSessId}'
           ";
           $CNK->__oDb->ExecQuery( $_sSQL );
       }
        
       return true;
        
    }
    
    //-------------------------------------------------//
    // Get variables from stored hash          		   //
    //-------------------------------------------------//    
    Function GetVars( $sVarName )
    {
       global $CNK;
       static $_fStream;
       static $_sStoredString = '';
       static $_aUnserializedData = Array();
       static $_sSQL='';
       static $_aSqlRes = array();
     
       if ( $this->__sMethod == 'fs' )
       {
           $_fStream = @fopen( 
                       $CNK->__oStd->RelativeToReal( $this->__sSessSt ).$this->__sSessId.'.sid',
                       "r"
                       );
           while ( !feof( $_fStream ) )
           {
               $_sStoredString .= @fgetc( $_fStream );
           }
           @fclose( $_fStream );
       }
       elseif ( $this->__sMethod == 'db' )
       {
           $_sSQL = "SELECT session_data
           FROM {$this->__sSessSt}  
           WHERE session_id = '{$this->__sSessId}'
           ";
           $CNK->__oDb->ExecQuery( $_sSQL );
           $_aSqlRes = $CNK->__oDb->Fetch();
           
           $_sStoredString = $_aSqlRes['session_data']; 
       }
       
       $_aUnserializedData = unserialize( $_sStoredString );
        
       return $_aUnserializedData[ $sVarName ];
    }
    
    //-------------------------------------------------//
    // Clean stored variables hash          		   //
    //-------------------------------------------------//
    Function CleanHash()
    {
        global $CNK;
        static $_sSQL='';
        
        $this->__aStored = Array();
        
        
        return true;   
    }

}


?>