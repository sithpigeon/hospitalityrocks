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

class LS
{

	var $__aDirsArray	= Array();
	var $__aFilesArray	= Array();
	var $__aDirContent	= Array( 'dirs' => Array(), 'files' => Array() );

	/*----------------------------------------------------------------------------*/
	/* @ Class constructor	 										 */
	/*----------------------------------------------------------------------------*/
	
	Function LS()
	{
		// {{{ Constructor }}} //
	}

	/*----------------------------------------------------------------------------*/
	/* @ Directories' operations 										 */
	/*----------------------------------------------------------------------------*/

	
	Function GetDirsOnly( $sDir ) 
	{
   		
		/*
	 	 @ GetDirsOnly( $sDir )
	 	 @
	 	 @ param $sDir - path_to_target_directory
	 	 @
	 	 @ Fills $_DirsArray with directories in target
	 	 @ directory ( $sDir )
		*/
		
		global $oDEBUG;
		static $_fStream = '';
		static $_sPointer = '';

    	$sDir = preg_replace( "#^\.#", "", $sDir );
		$sDir = preg_replace( "#/$#", "", $sDir );

    	if ( file_exists($sDir) ) 
		{
  	   		if ( is_dir($sDir) ) 
			{

          		$_fStream = opendir($sDir);
             	while (($_sPointer = readdir($_fStream)) !== false) 
				{
		            if ( ($_sPointer != ".") && ($_sPointer != "..") ) 
				    {
		                if ( is_dir($sDir.'/'.$_sPointer) ) 
						{
		                   $this->__aDirsArray[] = $sDir.'/'.$_sPointer;
		                } 
						elseif ( is_file($sDir.'/'.$_sPointer) ) 
						{
                           next;
		                }
	                }
	            }
            	closedir($_fStream);
            	return $this->__aDirsArray;
            } 
			else 
			{
          		$oDEBUG->Error("Target is not a directory");
       		}
    	} 
		else 
		{
       		$oDEBUG->Error("Could not locate target");
    	}
 	}

 	Function GetFilesOnly( $sDir )
	{
		/*
	 	 @ GetFilesOnly( $sDir )
	 	 @
	 	 @ param $sDir [ string ] - path_to_target_directory
	 	 @
	 	 @ Fills $_FilesArray with files in target
	 	 @ directory ( $sDir )
		*/
		
		global $oDEBUG;
		static $_fStream = '';
		static $_sPointer = '';
		
		$sDir = preg_replace( "#^\.#", "", $sDir );
		$sDir = preg_replace( "#/$#", "", $sDir );

  		if ( file_exists($sDir) ) 
		{
    		if ( is_dir($sDir) ) 
			{
       			$_fStream = opendir($sDir);
       			while (($sFilename = readdir($_fStream)) !== false) 
				{
       	     		if (($sFilename != ".") && ($sFilename != "..")) 
					{
	            		if (is_dir($sDir."/".$sFilename)) 
						{
		           			LS::GetDirContent($sDir."/".$sFilename);
	            		} 
						else 
						{
		           			$this->__aFilesArray[] = $sDir."/".$sFilename;
		        		}
	         		}
       			}
       			closedir($_fStream);
       			return $this->__aFilesArray;
    		} 
			else 
			{
       			$oDEBUG->Error("Target is not a directory");
       			return FALSE;
    		}
  		} 
		else 
		{
    		$oDEBUG->Error("Could not locate {$sDir}");
    		return;
  		}
	}
	
	Function GetDirContent( $sDir ) 
	{
  		/*
	 	 @ GetDirContent( $sDir )
	 	 @
	 	 @ param $sDir [ string ] - path_to_target_directory
	 	 @
	 	 @ Fills $_DirContent with directories and files in 
	 	 @ target directory ( $sDir )
		*/
		
		global $oDEBUG;
		static $_fStream = '';
		static $_sPointer = '';
		
		$sDir = preg_replace( "#^\.#", "", $sDir );
		$sDir = preg_replace( "#/$#", "", $sDir );

  		if ( file_exists($sDir) ) 
		{
    		if ( is_dir($sDir) ) 
			{
       			$_fStream = opendir($sDir);
       			while (($sFilename = readdir($_fStream)) !== false) 
				{
       	     		if (($sFilename != ".") && ($sFilename != "..")) 
					{
	            		if (is_dir($sDir."/".$sFilename)) 
						{
		           			$this->__aDirContent['dirs'][] = $sDir."/".$sFilename;
							LS::GetDirContent($sDir."/".$sFilename);
	            		} 
						else 
						{
		           			$this->__aDirContent['files'][] = $sDir."/".$sFilename;
		        		}
	         		}
       			}
       			closedir($_fStream);
       			return $this->__aDirContent;
    		} 
			else 
			{
       			$oDEBUG->Error("Target is not a directory.");
       			return FALSE;
    		}
  		} 
		else 
		{
    		$oDEBUG->Error("Could not locate target directory.");
    		return;
  		}
 	}

 	Function CreateDir( $sDir, $sPerm="0775" ) 
	{
   		/*
	 	 @ CreateDir( $sDir, $sPerm="0775" )
	 	 @
	 	 @ param $sDir  [ string ] - path_to_target_directory
		 @ param $sPerm [ string ] - permissions for new directory
	 	 @
	 	 @ Creates new directory ( $sDir ) with permissions ( $sPerm )
		*/
		
		global $oDEBUG;		
		
		$path_parts = pathinfo($sDir);
		if ( !is_writable( $path_parts['dirname'] ) )
		{
			$oDEBUG->Error("Can't create directory. One of parent directories isn't writable.");
		}		
		
		$sDir = preg_replace( "#^\.#", "", $sDir );
		$sDir = preg_replace( "#/$#", "", $sDir );

		if ( !file_exists( $sDir ) && !is_dir( $sDir ) ) 
		{
      		mkdir( $sDir, octdec($sPerm) );
   		} 
		else 
		{
      		$oDEBUG->Error("Can't create directory. Target exists.");
   		}
 	}

 	Function RenameDir( $sOldDirname, $sNewDirname ) 
 	{
		/*
	 	 @ RenameDir( $sOldDirname, $sNewDirname )
	 	 @
	 	 @ param $sOldDirname [ string ] - path_to_target_directory
		 @ param $sNewDirname [ string ] - path_to_new_directory
	 	 @
	 	 @ Renames target directory ( $sOldDirname ) to directory ( $sNewDirname )
		*/
		
		global $oDEBUG;
    	
		$sOldDirname = preg_replace( "#^\.#", "", $sOldDirname );
		$sOldDirname = preg_replace( "#/$#", "", $sOldDirname );
		$sNewDirname = preg_replace( "#^\.#", "", $sNewDirname );
		$sNewDirname = preg_replace( "#/$#", "", $sNewDirname );
		
		if ( file_exists( $sOldDirname ) && is_dir( $sOldDirname ) )
		{		
			if ( !rename($sOldDirname, $sNewDirname) )
			{
				$oDEBUG->Error("Unable to rename directory.");
			}
		}
		else
		{
			$oDEBUG->Error("Directory with old name doesn't exist.");
		}		
 	}

	Function RemoveDir( $sDir ) 
	{
   		/*
	 	 @ RemoveDir( $sDir )
	 	 @
	 	 @ param $sDir [ string ] - path_to_target_directory
	 	 @
	 	 @ Removes recursively target directory ( $sDir ) using 
		 @ function "exec"
		*/
		
		global $oDEBUG;

   		$sDir = preg_replace( "#^\.#", "", $sDir );
		$sDir = preg_replace( "#/$#", "", $sDir );

   		if ( file_exists( $sDir ) && is_dir( $sDir ) ) 
		{
      		if ( function_exists("exec") )
			{
				exec("rm -rf {$sDir}", $output, $status);
				
				clearstatcache();
				
				if ( file_exists( $sDir ) && is_dir( $sDir ) )
				{
					LS::_myRemoveDir( $sDir );
				}
				else				
				{
					return false;
				}
			}
			else
			{
				LS::_myRemoveDir( $sDir );
			}
		}	
		else 
		{
      		$oDEBUG->Error("Can't remove directory. Target doesn't exist.");
   		}	
 	}
	
	Function _myRemoveDir( $sDir )
	{
		/*
	 	 @ myRemoveDir( $sDir )
	 	 @
	 	 @ param $sDir [ string ] - path_to_target_directory
	 	 @
	 	 @ Removes recursively target directory ( $sDir ) without 
		 @ using function "exec"
		*/
		
		global $oDEBUG;
		
		$sDir = preg_replace( "#^\.#", "", $sDir );
		$sDir = preg_replace( "#/$#", "", $sDir );
		
		if ( file_exists( $sDir ) && is_dir( $sDir ) ) 
		{
			LS::GetDirContent( $sDir );

       		for ( $i=0; $i<count($this->__aDirContent['files']);$i++)
			{
				unlink($this->__aDirContent['files'][$i]);	
			}
			
			for ( $i=0; $i<count($this->__aDirContent['dirs']);$i++)
			{
				unlink($this->__aDirContent['dirs'][$i]);	
			}
			
			rmdir($sDir);
			
			clearstatcache();
			
			if ( file_exists( $sDir ) && is_dir( $sDir ) )
			{
				$oDEBUG->Error("Can't remove directory. Maybe permissions denied.");
			}

   		} 
		else 
		{
      		$oDEBUG->Error("Can't remove directory. Target doesn't exist.");
   		}
	
	}
	
	/*----------------------------------------------------------------------------*/
	/* @ Files operations											              */
	/*----------------------------------------------------------------------------*/
	
	Function CreateFile( $sFile, $sPerm="0644" )
	{
		/*
	 	 @ CreateFile( $sFile, $sPerm="0644" )
	 	 @
	 	 @ param $sFile [ string ] - path_to_target_file
		 @ param $sPerm [ string ] - permissions to create file with
	 	 @
	 	 @ Creates file ( $sFile ) with permissions ( $sPerm ) 
		*/
		
		global $oDEBUG;
		static $_fStream = '';
		static $_mReturn = '';
		
		if ( !file_exists($sFile) )
		{
			if ( function_exists("touch") )
			{
				touch($sFile);				
			}
			else
			{
				$_fStream = fopen($sFile,"w");
				fclose($_fStream);
			}
			chmod($sFile, octdec($sPerm));
			$_mReturn = Array($sFile,$sPerm);
	    } 
		else
		{
			$oDEBUG->Error("Can't create file. Target exists.");
		}
		
		return $_mReturn;
	}
	
	Function RenameFile( $sOldFile, $sNewFile )
	{
		/*
	 	 @ RenameFile( $sOldFile, $new_file )
	 	 @
	 	 @ param $sOldFile [ string ] - path_to_target_file_old
		 @ param $new_file [ string ] - path_to_target_file_new
	 	 @
	 	 @ Renames $sOldFile to $sNewFile 
		*/
		
		global $oDEBUG;
		
		if ( file_exists($sOldFile) && is_file($sOldFile) )
		{
			if (  file_exists($sNewFile) && is_file($sNewFile) )
			{
				$oDEBUG->Error("Can't rename [ old ] file. Target [ new ] exists.");
			}
			
			if ( function_exists("rename") )
			{
				rename( $sOldFile, $sNewFile );
				return true;
			}
			else
			{
				LS::_myCopyMoveFile( $sOldFile, $sNewFile, "move" );
			}
		}
		else
		{
			$oDEBUG->Error("Can't rename file. Target doesn't exist.");
		}	
	}
	
	
	Function CopyMoveFile( $sOldFile, $sNewFile, $sAction="copy" )
	{
		/*
	 	 @ CopyMoveFile( $sOldFile, $new_file, $action="copy" )
	 	 @
	 	 @ param $sOldFile [ string ] - path_to_target_file_old
		 @ param $sNewFile [ string ] - path_to_target_file_new
		 @ param $sAction   [ string ] - 'move' - to move file, 
		 @						 'copy' - to copy file
	 	 @
	 	 @ Copy / Move $sOldFile to $sNewFile 
		*/
		
		global $oDEBUG;
		
		if ( file_exists($sOldFile) && is_file($sOldFile) )
		{
			if (  file_exists($sNewFile) && is_file($sNewFile) )
			{
				$oDEBUG->Error("Can't copy [ old ] file. Target with new name exists.");
			}
			
			if ( $sAction == "copy" )
			{
				if ( function_exists("copy") )
				{
					copy( $sOldFile, $sNewFile );
					return true;
				}
				else
				{
					LS::_myCopyMoveFile( $sOldFile, $sNewFile, "copy" );
				}
			}
			elseif ( $sAction == "move" )
			{			
				LS::_myCopyMoveFile( $sOldFile, $sNewFile, "move" );
			}
			
		}
		else
		{
			$oDEBUG->Error("Can't rename file. Target doesn't exist.");
		}
	
	}
	
	Function _myCopyMoveFile( $sOldFile, $sNewFile, $sAction="copy" )
	{
		/*
	 	 @ _myCopyMoveFile( $old_file, $sNewFile, $action="copy" )
	 	 @
	 	 @ param $sOldFile [ string ] - path_to_target_file_old
		 @ param $sNewFile [ string ] - path_to_target_file_new
		 @ param $sAction  [ string ] - 'move' - to move file, 
		 @						        'copy' - to copy file
	 	 @
	 	 @ Copy / Moves $sOldFile to $sNewFile using FILE_HANDLES
		*/
		
		global $oDEBUG;
		static $_fStream_OLD = '';
		static $_fStream_NEW = '';
		
		if ( file_exists($sOldFile) && is_file($sOldFile) )
		{
			if (  file_exists($sNewFile) && is_file($sNewFile) )
			{
				$oDEBUG->Error("Can't copy [ old ] file. Target with new name exists.");
			}
		
			$_fStream_OLD = fopen($sOldFile,"r");
			$_fStream_NEW = fopen($sNewFile,"a+");
			if ( $_fStream_OLD && $_fStream_NEW )
			{
				while ( !feof($_fStream_OLD) )
				{
					fputs($_fStream_NEW,fgets($_fStream_OLD));
				}				
				fclose($_fStream_NEW);
				fclose($_fStream_OLD);
				if ( $sAction == "move" )
				{
					unlink($sOldFile);
				}
				return true;
			}
			else
			{
				$oDEBUG->Error("Can't rename file. Permissions denied or STREAM opening failed.");
			}	
		}
	
	}
	
	Function RemoveFiles( $mFiles )
	{
		/*
	 	 @ RemoveFiles( $sFiles )
	 	 @
	 	 @ param $mFiles [ array ] / [ string ]
		 @ 
		 @ $mFiles = 'path_to_file';
		 @
		 @ $mFiles = (
	 	 @	'0'	=> 'path_to_file',
	 	 @   '1'	=> 'path_to_file',
	 	 @   ......
	 	 @   'n'	=> 'path_to_file'
	 	 @ );
		 @
	 	 @ Removes all files in array $mFiles or file if string
		*/

		global $oDEBUG;
		
		if ( !is_array( $mFiles ) )
		{
			$return = unlink( $mFiles );
		}		
		elseif ( is_array( $mFiles ) )
		{
		    for ( $i=0;$i<count($mFiles);$i++ )
		    {
			     $isOk = unlink($mFiles[$i]);
			     $return[$i] = Array($mFiles[$i],$isOk);
		    }
		}
		
		return $return;
	}
	
	
	Function SetPermissions( $aFiles )
	{
		/*
	 	 @ SetPermissions( $aFiles )
	 	 @
	 	 @ param $aFiles [ array ] - array of pathes_to_target_files
		 @
		 @ $sFiles (
	 	 @	'0'	=> Array ('path_to_file','file_permissions'),
	 	 @   '1'	=> Array ('path_to_file','file_permissions'),
	 	 @   ......
	 	 @   'n'	=> Array ('path_to_file','file_permissions')
	 	 @ );
		 @
	 	 @ Sets permissions to target files from array $sFiles
		*/
		
		if ( !is_array($aFiles) )
		{
			return false;
		}
		
		for ($i=0;$i<count($aFiles);$i++)
		{
			$isOk = chmod($aFiles[$i][0], octdec($aFiles[$i][1]));	
			$return[$i] = Array($aFiles[$i][0],$isOk);
		}
		return $return;
	
	}

}

?>
