<?php


/* {{{ INSTALL: CLASS ERRORCODES }}} */
define( 'ERROR_NO_INSTALLATION_FOUND' , "Sorry, you cannot use the script now. Installation is not complete." ); 
define( "ERROR_DIR_PERMISSION_DENIED" , "Can't remove <b><#DIR#></b> directory. Maybe permissions denied." );

/* {{{ INDEX: FSO CLASS ERRORCODES }}} */
define( 'ERROR_FS_TARGET_IS_NOT_A_DIR' , "Pointed target is not a directory." );
define( 'ERROR_FS_TARGET_DOESNT_EXIST' , "Pointed target doesn't exist." );

/* {{{ DB: MYSQL DATABASE ERRORCODES }}} */
define( 'ERROR_MYSQL_CANT_SELECT_DB'   , "Sorry, can't connect to MySQL database" );
define( 'ERROR_MYSQL_CANT_EXEC_QUERY'  , "Sorry, can't execute SQL query" );

/* {{{ I18N: MULTILINGUAL LIB ERRORCODES }}} */
define( 'ERROR_I18N_NO_PACKAGE_DIR'    , "Sorry, language package directory doesn't exists or directory corrupted." );
define( 'ERROR_I18N_NO_PACKAGE_FILE'   , "Sorry, language package for this section doesn't exists or file corrupted." );

/* {{{ DISPLAY: TEMPLATES PROCESSING }}} */
define( 'ERROR_DISPLAY_NO_TPL_FILE'    , "Sorry, template file doesn't exist." );

/* {{{ DISPLAY: TEMPLATES PROCESSING }}} */
define( 'ERROR_DISPLAY_NO_FILE'        , "Sorry, file doesn't exist." );


?>