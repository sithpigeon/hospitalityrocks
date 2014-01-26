<?php

/*-
 * Copyright (c) 2001-2007
 * Conkurent, LLC. All rights reserved.
 *
 * ApE ( the Application Engine )
 *
 * Module written by Cyrill S. Polikarpov [ clio@conkurent.com ]
 *
 * vim: set expandtab softtabstop=4 tabstop=4 shiftwidth=4
 *
 */

class DB_mysql extends DB 
{
    /**
    * Constructor
    * @access public
    **/
    Function DB_mysql()
    {
        /* Just an empty constructor */
    }   
        
    /**
    * Connect to MySQL database
    * @access public 
    * @param array $aDsnInfo connection params
    * @param bool  $bPersist set persistant connectin on/off
    *			   default value is False
    *
    * @return MySQL resource id #
    **/
    Function Connect( $aDsnInfo, $bPersist=false, $bSilent=false )
    {
        global $oDEBUG;
        
        $aDsnInfo["DATABASE_HOST"] = preg_replace( "/NULL/", "localhost" , $aDsnInfo["DATABASE_HOST"] );
        $aDsnInfo["DATABASE_PORT"] = preg_replace( "/NULL/", ""          , $aDsnInfo["DATABASE_PORT"] );
        $aDsnInfo["DATABASE_NAME"] = preg_replace( "/NULL/", "test"      , $aDsnInfo["DATABASE_NAME"] );
        $aDsnInfo["DATABASE_USER"] = preg_replace( "/NULL/", "root"      , $aDsnInfo["DATABASE_USER"] );
        $aDsnInfo["DATABASE_PASS"] = preg_replace( "/NULL/", ""          , $aDsnInfo["DATABASE_PASS"] );
        
        $aDsnParams = DB_mysql::_ParseDSN( $aDsnInfo );        
        
        if ( (bool)$bPersist )
        {
            $this->__sCnId = @mysql_connect( $aDsnParams[0], $aDsnParams[1], $aDsnParams[2] );
        }
        else
        {
            $this->__sCnId = @mysql_connect( $aDsnParams[0], $aDsnParams[1], $aDsnParams[2] );
        }
               
        if ( !$this->__sCnId && !$bSilent )
        {
            $this->__aErrors[] = mysql_errno().':'.mysql_error();
            $oDEBUG->SqlError( mysql_errno().':'.mysql_error() );
        }
        elseif ( !$this->__sCnId && $bSilent )
        {
            return false;
        }
        
        $this->__sTblPrx = $aDsnInfo["DATABASE_TPRX"];
        
        DB_mysql::SelectDB( $aDsnInfo["DATABASE_NAME"] );

        return true;
    }
    
    /**
    * Select MySQL database
    * ---------------------------------------------------------
    * SelectDB( string dbname )
    * ---------------------------------------------------------
    * @access public
    * @param string $sDbName the name of database to connect to
    * @return boolean connection result
    **/
    Function SelectDB( $sDbName )
    {
        global $oDEBUG;
        static $_bFlag;
        
        $_bFlag = @mysql_select_db( $sDbName, $this->__sCnId );     
        
        if ( !(bool)$_bFlag )
        {
            $this->__aErrors[] = mysql_errno().':'.mysql_error();
            $oDEBUG->SqlError( ERROR_MYSQL_CANT_SELECT_DB.'<br>'.mysql_errno().':'.mysql_error() );
        }

        return $_bFlag;       
    }
    
    /**
    * Executes query
    * Changes table prefix to value from configuration;
    * Adds executed query result into $this->__aQueriesCatch[];
    * Increases total executed queries counter
    * ---------------------------------------------------------
    * ExecQuery( string query_string )
    * ---------------------------------------------------------
    * @access public 
    * @param string $sqlQueryString SQL query string
    * @return int Mysql query result id #
    **/
    Function ExecQuery( $sqlQueryString )
    {
        global $oDEBUG;
        
        if ( $this->__sTblPrx != "cnk_")
		{
		   $sqlQueryString = preg_replace( "/cnk_(\S+?)([\s\.,]|$)/", 
		                                   $this->__sTblPrx."\\1\\2", 
		                                   $sqlQueryString
		                                 );
		}
		
		$this->__sQrId = @mysql_query( $sqlQueryString, $this->__sCnId );
		if ( !$this->__sQrId )
		{
		    $oDEBUG->SqlError( ERROR_MYSQL_CANT_EXEC_QUERY.'<br>'.mysql_errno().':'.mysql_error() ); 
		}

		$this->__iQrCnt++;
		$this->__aQueriesCatch[] = $sqlQueryString;

		return $this->__sQrId; 
    }
    
    /**
    * Proceeds with fetch
    * -------------------------------------------------------------------
    * Fetch( [int query_id] )
    * -------------------------------------------------------------------
    * @access public
    * @param int $iQrId number of executed query from chatch [ optional ]
    * @return array of fetched results
    **/
    Function Fetch( $iQrId = "" ) 
    {    
    	if ( empty($iQrId) )
    	{
    		$iQrId = $this->__sQrId;
    	}    	
        return @mysql_fetch_array( $iQrId , MYSQL_ASSOC );        
    }
    
    /**
    * Gets count of fetched rows
    * ----------------------------------------------------------------
    * NumRows( [int query_id] )
    * ----------------------------------------------------------------
    * @access public
    * @param int $iQrId number of executed query from chatch [ optional ]
    **/
    Function NumRows( $iQrId = "" ) 
    {    
    	if ( empty($iQrId) )
    	{
    		$iQrId = $this->__sQrId;
    	}    	
        return @mysql_num_rows( $iQrId );        
    }
    
    /**
    * Gets count of all executed queries
    * ----------------------------------------------------------------
    * GetQueriesCount( void )
    * ----------------------------------------------------------------
    * @access public
    * @return int $this->__iQrCnt count of all executed queries
    **/
    Function GetQueriesCount() 
    {	
        return $this->__iQrCnt;        
    }
    
    /**
    * Gets any/all queries from catch
    * ----------------------------------------------------------------
    * GetQueryFromCatch( [int query_id] )
    * ----------------------------------------------------------------
    * @access public
    * @return string/array any/all queries from catch
    **/
    Function GetQueryFromCatch( $iQId = "" ) 
    {   	
    	if ( $iQId == "" )
        {
            return $this->__aQueriesCatch[ $iQId ];        
        }
        else
        {
            return $this->__aQueriesCatch;        
        }
    }
    
    /**
    * Frees queries' catch
    * ----------------------------------------------------------------
    * ClearQueriesCatch( void )
    * ----------------------------------------------------------------
    * @access public
    * @return void
    **/
    Function ClearQueriesCatch() 
    {	
        $this->__aQueriesCatch = array();
    }
    
    /**
    * Frees mysql result
    * if not specified valid query_id, free results from last query
    * ----------------------------------------------------------------
    * FreeResult( [int query_id] )
    * ----------------------------------------------------------------
    * @access public
    * @return void
    **/
    Function FreeResult( $iQrId = "" )
    {
        if ( empty($iQrId) )
    	{
    		$iQrId = $this->__sQrId;
    	}
    	@mysql_free_result( $iQrId );
    }
        
    /**
    * Closes MySQL connection
    * ----------------------------------------------------------------
    * CloseConnection( void )
    * ----------------------------------------------------------------
    * @access public
    * @return void
    **/
    Function CloseConnection() 
    {	
        return @mysql_close( $this->__sCnId );
    }
    
    /**
     * Parses DSNstring
     * -------------------------------------------------------
     * DB_mysql::_ParseDSN( array $aDsnInfo )
     * -------------------------------------------------------
     * @access local
     * @param array $aDsnInfo an array of connection params
     * @return string $_sDsnString formatted DSN string 
     */
    Function _ParseDSN( $aDsnInfo )
    {
        static $_sDsnString;        
        
        if (  !empty($aDsnInfo["DATABASE_PORT"]) && $aDsnInfo["DATABASE_PORT"] != 'NULL' )
        {
            $_sDsnString[] = "{$aDsnInfo['DATABASE_HOST']}:{$aDsnInfo['DATABASE_PORT']}";   
        }
        else
        {
            $_sDsnString[] = "{$aDsnInfo['DATABASE_HOST']}";
        }
        
        $_sDsnString[] = "{$aDsnInfo['DATABASE_USER']}";
        $_sDsnString[] = "{$aDsnInfo['DATABASE_PASS']}";

        return $_sDsnString;        
    }
    
}

?>
