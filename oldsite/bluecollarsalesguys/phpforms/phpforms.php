<?php
/*
+--------------------------------------------------------------------------
|
|   FORM RETRIEVING
|   phpForms, form processing script
|   by Igor M. Belobrov,
|   Conkurent, LLC, Web Programming Department
|   Email: igor@conkurent.com
|   ICQ #: 276745076
|   =========================================
|   (c) 2004-2007 Conkurent, LLC
|   Visit: http://conkurent.com
|   Email: company@conkurent.com
|
+--------------------------------------------------------------------------
*/

function form( $fid )
{
   $aMatches = array();
   $sContent = '';
   $mSFile   = '';

   $sUrl = "http://www.bluecollarsalesguys.com/phpforms/";

   $sDns  = preg_replace( "/^http:\/\//i", '', $sUrl );
   preg_match( "/\/(.*)$/", $sDns, $aMatches );
   if ( isset($aMatches[1]) ) $sPath = $aMatches[1]; else $sPath = '';
   $sPath .= "index.php?fid={$fid}&embed=1";
   $sDns  = preg_replace( "/\/.*$/",       '', $sDns );

   if ( $mSFile = fsockopen( $sDns, 80, &$errno, &$errstr, 80 ) )
   {
      fputs( $mSFile, "GET /$sPath HTTP/1.0\r\nHost: $sDns\r\n\r\n");
      while ( !feof($mSFile) ) $sContent .= fgets( $mSFile, 0xFFF );
      fclose( $mSFile );
      $sContent = preg_replace("/^.*?\r\n\r\n/s", "", $sContent);
      echo $sContent;
   }
}

?>