<?php
/*
+--------------------------------------------------------------------------
|
|   MySQL DUMP ( PART OF INSTALLATION MODULE )
|   phpForms, form processing script
|   =========================================
|   Module written by Igor M. Belobrov
|   Conkurent, LLC, Web Programming Department
|   Email: igor@conkurent.com
|   ICQ #: 276745076
|   =========================================
|   Powered by Conkurent Engine v2.0,
|   written by Cyrill Polikarpov
|   Conkurent, LLC, Web Programming Department
|   Email: clio@conkurent.com
|   ICQ #: 99885395
|   =========================================
|   (c) 2004-2007 Conkurent, LLC
|   Visit: http://conkurent.com
|   Email: company@conkurent.com
|
+--------------------------------------------------------------------------
*/

$aSql = array();

/* DEBUG, kill in final version */
/*
$aSql[] = "DROP TABLE IF EXISTS pf_admins;";
$aSql[] = "DROP TABLE IF EXISTS pf_checks;";
$aSql[] = "DROP TABLE IF EXISTS pf_checks_values;";
$aSql[] = "DROP TABLE IF EXISTS pf_colors;";
$aSql[] = "DROP TABLE IF EXISTS pf_fields;";
$aSql[] = "DROP TABLE IF EXISTS pf_forms;";
$aSql[] = "DROP TABLE IF EXISTS pf_items;";
$aSql[] = "DROP TABLE IF EXISTS pf_layouts;";
$aSql[] = "DROP TABLE IF EXISTS pf_mail_tpls;";
$aSql[] = "DROP TABLE IF EXISTS pf_pages;";
$aSql[] = "DROP TABLE IF EXISTS pf_pre_values;";
$aSql[] = "DROP TABLE IF EXISTS pf_predefined;";
$aSql[] = "DROP TABLE IF EXISTS pf_prop_values;";
$aSql[] = "DROP TABLE IF EXISTS pf_properties;";
$aSql[] = "DROP TABLE IF EXISTS pf_submissions;";
$aSql[] = "DROP TABLE IF EXISTS pf_types;";

/* END DEBUG  */


$aSql[] =
"CREATE TABLE pf_admins (
  adm_id varchar(32) NOT NULL default '',
  adm_login varchar(255) default NULL,
  adm_password varchar(255) default NULL,
  PRIMARY KEY  (adm_id)
) TYPE=MyISAM;";

$aSql[] =
"CREATE TABLE pf_checks (
  chk_id varchar(32) NOT NULL default '',
  chk_name varchar(255) NOT NULL default '',
  chk_rule varchar(255) NOT NULL default '',
  chk_type_id varchar(32) NOT NULL default '',
  PRIMARY KEY  (chk_id)
) TYPE=MyISAM;";

$aSql[] =
"CREATE TABLE pf_checks_values (
  val_id varchar(32) NOT NULL default '',
  val_chk_id varchar(32) NOT NULL default '',
  val_fld_id varchar(32) NOT NULL default '',
  val_fld_value varchar(255) NOT NULL default '',
  PRIMARY KEY  (val_id)
) TYPE=MyISAM;";

$aSql[] = 
"CREATE TABLE pf_colors (
  color_id varchar(32) NOT NULL default '',
  color_name varchar(255) NOT NULL default '',
  color_rgb varchar(6) NOT NULL default '000000',
  PRIMARY KEY  (color_id)
) TYPE=MyISAM;";

$aSql[] = 
"CREATE TABLE pf_fields (
  fld_id varchar(32) NOT NULL default '',
  fld_pg_id varchar(32) NOT NULL default '',
  fld_name varchar(255) NOT NULL default '',
  fld_color varchar(6) NOT NULL default '',
  fld_caption_style varchar(255) NOT NULL default '',
  fld_control_style varchar(255) NOT NULL default '',
  fld_type_id varchar(32) NOT NULL default '0',
  fld_num int(3) NOT NULL default '0',
  fld_required tinyint(1) NOT NULL default '0',
  fld_layout_id varchar(32) NOT NULL default '',
  fld_mtpl_id varchar(32) NOT NULL default '',
  PRIMARY KEY  (fld_id)
) TYPE=MyISAM;";


$aSql[] = 
"CREATE TABLE pf_forms (
  frm_id varchar(32) NOT NULL default '',
  frm_name varchar(255) NOT NULL default '',
  frm_type tinyint(1) NOT NULL default '0',
  frm_dest tinyint(3) unsigned NOT NULL default '0',
  frm_email varchar(255) default NULL,
  frm_color varchar(6) NOT NULL default '',
  frm_maxfilesize int(10) unsigned NOT NULL default '0',
  frm_width int(10) unsigned NOT NULL default '0',
  frm_mtpl_id varchar(32) NOT NULL default '',
  frm_after_sub_txt text NOT NULL,
  frm_redirect varchar(255) NOT NULL default '',
  frm_btn_prev varchar(255) NOT NULL default '',
  frm_btn_next varchar(255) NOT NULL default '',
  frm_btn_submit varchar(255) NOT NULL default '',
  PRIMARY KEY  (frm_id)
) TYPE=MyISAM;";

$aSql[] = 
"CREATE TABLE pf_items (
  it_id varchar(32) NOT NULL default '',
  it_fld_id varchar(32) NOT NULL default '',
  it_title text NOT NULL,
  it_value varchar(255) NOT NULL default '',
  it_num int(3) NOT NULL default '0',
  it_default int(11) NOT NULL default '0',
  PRIMARY KEY  (it_id)
) TYPE=MyISAM COMMENT='Item options';";

$aSql[] = 
"CREATE TABLE pf_layouts (
  layout_id varchar(32) NOT NULL default '0',
  layout_type_id varchar(32) NOT NULL default '',
  layout_file_name varchar(255) NOT NULL default '',
  PRIMARY KEY  (layout_id)
) TYPE=MyISAM;";

$aSql[] = 
"CREATE TABLE pf_mail_tpls (
  mtpl_id varchar(32) NOT NULL default '',
  mtpl_name varchar(255) NOT NULL default '',
  mtpl_tpl text,
  mtpl_from varchar(255) NOT NULL default '',
  mtpl_subj varchar(255) default NULL,
  mtpl_plain tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (mtpl_id)
) TYPE=MyISAM;";


$aSql[] = 
"CREATE TABLE pf_pages (
  pg_id varchar(32) NOT NULL default '',
  pg_title varchar(255) NOT NULL default '',
  pg_color varchar(32) NOT NULL default '',
  pg_top_text text NOT NULL,
  pg_bottom_text text NOT NULL,
  pg_width int(10) unsigned NOT NULL default '0',
  pg_frm_id varchar(32) NOT NULL default '',
  pg_num int(10) unsigned NOT NULL default '0',
  pg_btn_prev varchar(255) NOT NULL default '',
  pg_btn_next varchar(255) NOT NULL default '',
  PRIMARY KEY  (pg_id)
) TYPE=MyISAM COMMENT='Form pages';";

$aSql[] = 
"CREATE TABLE pf_pre_values (
  prv_id varchar(32) NOT NULL default '',
  prv_list_id varchar(32) NOT NULL default '',
  prv_title varchar(255) NOT NULL default '',
  prv_value varchar(255) NOT NULL default '',
  prv_num int(11) NOT NULL default '0',
  PRIMARY KEY  (prv_id)
) TYPE=MyISAM;";


$aSql[] = 
"CREATE TABLE pf_predefined (
  pre_id varchar(32) NOT NULL default '',
  pre_list_name varchar(255) NOT NULL default '',
  PRIMARY KEY  (pre_id)
) TYPE=MyISAM;";


$aSql[] =
"CREATE TABLE pf_prop_values (
  val_prop_id varchar(32) NOT NULL default '',
  val_fld_id varchar(32) NOT NULL default '',
  val_value varchar(255) NOT NULL default '',
  PRIMARY KEY  (val_fld_id,val_prop_id)
) TYPE=MyISAM COMMENT='HTML properties values';";

$aSql[] =
"CREATE TABLE pf_properties (
  prop_id varchar(32) NOT NULL default '',
  prop_type_id varchar(32) NOT NULL default '',
  prop_name varchar(255) NOT NULL default '',
  PRIMARY KEY  (prop_id)
) TYPE=MyISAM COMMENT='HTML properties';";


$aSql[] =
"CREATE TABLE pf_submissions (
  sub_id varchar(32) NOT NULL default '',
  sub_frm_id varchar(32) NOT NULL default '',
  sub_frm_name varchar(255) NOT NULL default '',
  sub_rec_id varchar(32) NOT NULL default '',
  sub_fld_num int(3) NOT NULL default '0',
  sub_fld_name varchar(255) NOT NULL default '',
  sub_fld_value text NOT NULL,
  sub_filename varchar(255) NOT NULL default '',
  sub_date datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (sub_id)
) TYPE=MyISAM;";

$aSql[] = 
"CREATE TABLE pf_types (
  type_id varchar(32) NOT NULL default '',
  type_name varchar(255) default NULL,
  type_props varchar(255) NOT NULL default '',
  type_extra varchar(255) NOT NULL default '',
  type_check_path varchar(255) default NULL,
  PRIMARY KEY  (type_id)
) TYPE=MyISAM COMMENT='Control types';";

//----------------------------------------------------------------------
// Insert values
//----------------------------------------------------------------------

// Checks ( pf_checks )
$aSql[] = 'INSERT INTO pf_checks VALUES("0", "Not empty", "not_empty", "0");';
$aSql[] = 'INSERT INTO pf_checks VALUES("1", "Numbers only", "numbers_only", "0");';
$aSql[] = 'INSERT INTO pf_checks VALUES("2", "Letters only", "letters_only", "0");';
$aSql[] = 'INSERT INTO pf_checks VALUES("3", "Email", "email", "0");';
$aSql[] = 'INSERT INTO pf_checks VALUES("4", "Chars interval", "from_to_chars", "0");';
$aSql[] = 'INSERT INTO pf_checks VALUES("5", "<", "less_than_opts", "1");';
$aSql[] = 'INSERT INTO pf_checks VALUES("6", ">", "more_than_opts", "1");';
$aSql[] = 'INSERT INTO pf_checks VALUES("7", "=", "equal_opts", "1");';
$aSql[] = 'INSERT INTO pf_checks VALUES("8", "Interval", "interval_opts", "1");';

// Colors ( pf_colors )
$aSql[] = 'INSERT INTO pf_colors VALUES("001", "aqua", "00FFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("002", "lima", "00FF00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("003", "teal", "008080");';
$aSql[] = 'INSERT INTO pf_colors VALUES("004", "whitesmoke", "F5F5F5");';
$aSql[] = 'INSERT INTO pf_colors VALUES("005", "gainsboro", "DCDCDC");';
$aSql[] = 'INSERT INTO pf_colors VALUES("006", "oldlace", "FDF5E6");';
$aSql[] = 'INSERT INTO pf_colors VALUES("007", "linen", "FAF0E6");';
$aSql[] = 'INSERT INTO pf_colors VALUES("008", "antiquewhite", "FAEBD7");';
$aSql[] = 'INSERT INTO pf_colors VALUES("009", "papayawhip", "FFEFD5");';
$aSql[] = 'INSERT INTO pf_colors VALUES("010", "blanchedalmond", "FFEBCD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("011", "bisque", "FFE4C4");';
$aSql[] = 'INSERT INTO pf_colors VALUES("012", "peachpuff", "FFDAB9");';
$aSql[] = 'INSERT INTO pf_colors VALUES("013", "navajowhite", "FFDEAD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("014", "moccasin", "FFE4B5");';
$aSql[] = 'INSERT INTO pf_colors VALUES("015", "cornsilk", "FFF8DC");';
$aSql[] = 'INSERT INTO pf_colors VALUES("016", "ivory", "FFFFF0");';
$aSql[] = 'INSERT INTO pf_colors VALUES("017", "lemonchiffon", "FFFACD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("018", "seashell", "FFF5EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("019", "mintcream", "F5FFFA");';
$aSql[] = 'INSERT INTO pf_colors VALUES("020", "azure", "F0FFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("021", "aliceblue", "F0F8FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("022", "lavender", "E6E6FA");';
$aSql[] = 'INSERT INTO pf_colors VALUES("023", "lavenderblush", "FFF0F5");';
$aSql[] = 'INSERT INTO pf_colors VALUES("024", "mistyrose", "FFE4E1");';
$aSql[] = 'INSERT INTO pf_colors VALUES("025", "branco", "FFFFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("026", "preto", "000000");';
$aSql[] = 'INSERT INTO pf_colors VALUES("027", "darkslategray", "270707");';
$aSql[] = 'INSERT INTO pf_colors VALUES("028", "dimgray", "696969");';
$aSql[] = 'INSERT INTO pf_colors VALUES("029", "slategray", "708090");';
$aSql[] = 'INSERT INTO pf_colors VALUES("030", "lightslategray", "778899");';
$aSql[] = 'INSERT INTO pf_colors VALUES("031", "cinza", "BEBEBE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("032", "cinzaclaro", "D3D3D3");';
$aSql[] = 'INSERT INTO pf_colors VALUES("033", "midnightblue", "151570");';
$aSql[] = 'INSERT INTO pf_colors VALUES("034", "navy", "000080");';
$aSql[] = 'INSERT INTO pf_colors VALUES("035", "cornflowerblue", "6495ED");';
$aSql[] = 'INSERT INTO pf_colors VALUES("036", "darkslateblue", "3A318B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("037", "slateblue", "6A00CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("038", "mediumslateblue", "7B68EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("039", "lightslateblue", "8470FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("040", "mediumblue", "0000CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("041", "royalblue", "3569E1");';
$aSql[] = 'INSERT INTO pf_colors VALUES("042", "azul", "0000FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("043", "dodgerblue", "1890FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("044", "deepskyblue", "00BFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("045", "skyblue", "87CEEB");';
$aSql[] = 'INSERT INTO pf_colors VALUES("046", "lightskyblue", "87CEFA");';
$aSql[] = 'INSERT INTO pf_colors VALUES("047", "steelblue", "3882B4");';
$aSql[] = 'INSERT INTO pf_colors VALUES("048", "vermelhoclaro", "D3A7A8");';
$aSql[] = 'INSERT INTO pf_colors VALUES("049", "lightsteelblue", "B0C4DE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("050", "lightblue", "ADD8E6");';
$aSql[] = 'INSERT INTO pf_colors VALUES("051", "powderblue", "B0E0E6");';
$aSql[] = 'INSERT INTO pf_colors VALUES("052", "paleturquoise", "AFEEEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("053", "darkturquoise", "00CED1");';
$aSql[] = 'INSERT INTO pf_colors VALUES("054", "mediumturquoise", "3AD1CC");';
$aSql[] = 'INSERT INTO pf_colors VALUES("055", "turquoise", "34E0D0");';
$aSql[] = 'INSERT INTO pf_colors VALUES("056", "cyan", "00FFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("057", "lightcyan", "E0FFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("058", "cadetblue", "009EA0");';
$aSql[] = 'INSERT INTO pf_colors VALUES("059", "mediumaquamarine", "66CDAA");';
$aSql[] = 'INSERT INTO pf_colors VALUES("060", "aquamarine", "7FFFD4");';
$aSql[] = 'INSERT INTO pf_colors VALUES("061", "darkgreen", "006400");';
$aSql[] = 'INSERT INTO pf_colors VALUES("062", "darkolivegreen", "006B27");';
$aSql[] = 'INSERT INTO pf_colors VALUES("063", "darkseagreen", "8FBC8F");';
$aSql[] = 'INSERT INTO pf_colors VALUES("064", "seagreen", "268B00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("065", "mediumseagreen", "30B371");';
$aSql[] = 'INSERT INTO pf_colors VALUES("066", "lightseagreen", "1AB2AA");';
$aSql[] = 'INSERT INTO pf_colors VALUES("067", "palegreen", "98FB98");';
$aSql[] = 'INSERT INTO pf_colors VALUES("068", "springgreen", "00FF7F");';
$aSql[] = 'INSERT INTO pf_colors VALUES("069", "lawngreen", "7CFC00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("070", "verde", "00FF00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("071", "chartreuse", "7FFF00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("072", "mediumspringgreen", "00FA9A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("073", "greenyellow", "ADFF27");';
$aSql[] = 'INSERT INTO pf_colors VALUES("074", "limegreen", "28CD28");';
$aSql[] = 'INSERT INTO pf_colors VALUES("075", "yellowgreen", "9ACD28");';
$aSql[] = 'INSERT INTO pf_colors VALUES("076", "forestgreen", "1C8B1C");';
$aSql[] = 'INSERT INTO pf_colors VALUES("077", "olivedrab", "6B8E1D");';
$aSql[] = 'INSERT INTO pf_colors VALUES("078", "darkkhaki", "BDB76B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("079", "khaki", "F0E68C");';
$aSql[] = 'INSERT INTO pf_colors VALUES("080", "palegoldenrod", "EEE8AA");';
$aSql[] = 'INSERT INTO pf_colors VALUES("081", "lightgoldenrodyellow", "FAFAD2");';
$aSql[] = 'INSERT INTO pf_colors VALUES("082", "lightyellow", "FFFFC8");';
$aSql[] = 'INSERT INTO pf_colors VALUES("083", "amarelo", "FFFF00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("084", "ouro", "FFD700");';
$aSql[] = 'INSERT INTO pf_colors VALUES("085", "lightgoldenrod", "EEDD82");';
$aSql[] = 'INSERT INTO pf_colors VALUES("086", "goldenrod", "DAA51A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("087", "darkgoldenrod", "B88609");';
$aSql[] = 'INSERT INTO pf_colors VALUES("088", "rosybrown", "BC8F8F");';
$aSql[] = 'INSERT INTO pf_colors VALUES("089", "indianred", "CD0000");';
$aSql[] = 'INSERT INTO pf_colors VALUES("090", "saddlebrown", "8B0601");';
$aSql[] = 'INSERT INTO pf_colors VALUES("091", "sienna", "A00025");';
$aSql[] = 'INSERT INTO pf_colors VALUES("092", "peru", "CD8533");';
$aSql[] = 'INSERT INTO pf_colors VALUES("093", "burlywood", "DEB887");';
$aSql[] = 'INSERT INTO pf_colors VALUES("094", "beige", "F5F5DC");';
$aSql[] = 'INSERT INTO pf_colors VALUES("095", "wheat", "F5DEB3");';
$aSql[] = 'INSERT INTO pf_colors VALUES("096", "sandybrown", "F4A400");';
$aSql[] = 'INSERT INTO pf_colors VALUES("097", "tan", "D2B48C");';
$aSql[] = 'INSERT INTO pf_colors VALUES("098", "chocolate", "D26918");';
$aSql[] = 'INSERT INTO pf_colors VALUES("099", "firebrick", "B21C1C");';
$aSql[] = 'INSERT INTO pf_colors VALUES("100", "brown", "A52222");';
$aSql[] = 'INSERT INTO pf_colors VALUES("101", "darksalmon", "E9967A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("102", "salmon", "FA8072");';
$aSql[] = 'INSERT INTO pf_colors VALUES("103", "lightsalmon", "FFA07A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("104", "laranja", "FFA500");';
$aSql[] = 'INSERT INTO pf_colors VALUES("105", "darkorange", "FF8C00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("106", "coral", "FF7F00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("107", "lightcoral", "F08080");';
$aSql[] = 'INSERT INTO pf_colors VALUES("108", "tomato", "FF0039");';
$aSql[] = 'INSERT INTO pf_colors VALUES("109", "orangered", "FF0600");';
$aSql[] = 'INSERT INTO pf_colors VALUES("110", "vermelho", "FF0000");';
$aSql[] = 'INSERT INTO pf_colors VALUES("111", "hotpink", "FF69B4");';
$aSql[] = 'INSERT INTO pf_colors VALUES("112", "deeppink", "FF1093");';
$aSql[] = 'INSERT INTO pf_colors VALUES("113", "rosa", "FFC0CB");';
$aSql[] = 'INSERT INTO pf_colors VALUES("114", "rosaclaro", "FFB6C1");';
$aSql[] = 'INSERT INTO pf_colors VALUES("115", "palevioletred", "DB7093");';
$aSql[] = 'INSERT INTO pf_colors VALUES("116", "maroon", "B00400");';
$aSql[] = 'INSERT INTO pf_colors VALUES("117", "mediumvioletred", "C71185");';
$aSql[] = 'INSERT INTO pf_colors VALUES("118", "violetred", "D01A90");';
$aSql[] = 'INSERT INTO pf_colors VALUES("119", "magenta", "FF00FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("120", "violeta", "EE82EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("121", "plum", "DDA0DD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("122", "orchid", "DA70D6");';
$aSql[] = 'INSERT INTO pf_colors VALUES("123", "mediumorchid", "BA00D3");';
$aSql[] = 'INSERT INTO pf_colors VALUES("124", "darkorchid", "9928CC");';
$aSql[] = 'INSERT INTO pf_colors VALUES("125", "darkviolet", "9400D3");';
$aSql[] = 'INSERT INTO pf_colors VALUES("126", "blueviolet", "8A23E2");';
$aSql[] = 'INSERT INTO pf_colors VALUES("127", "purple", "A01AF0");';
$aSql[] = 'INSERT INTO pf_colors VALUES("128", "mediumpurple", "9370DB");';
$aSql[] = 'INSERT INTO pf_colors VALUES("129", "thistle", "D8BFD8");';
$aSql[] = 'INSERT INTO pf_colors VALUES("130", "snow1", "FFFAFA");';
$aSql[] = 'INSERT INTO pf_colors VALUES("131", "snow2", "EEE9E9");';
$aSql[] = 'INSERT INTO pf_colors VALUES("132", "snow3", "CDC9C9");';
$aSql[] = 'INSERT INTO pf_colors VALUES("133", "snow4", "8B8989");';
$aSql[] = 'INSERT INTO pf_colors VALUES("134", "seashell1", "FFF5EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("135", "seashell2", "EEE5DE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("136", "seashell3", "CDC5BF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("137", "seashell4", "8B8682");';
$aSql[] = 'INSERT INTO pf_colors VALUES("138", "AntiqueWhite1", "FFEFDB");';
$aSql[] = 'INSERT INTO pf_colors VALUES("139", "AntiqueWhite2", "EEDFCC");';
$aSql[] = 'INSERT INTO pf_colors VALUES("140", "AntiqueWhite3", "CDC0B0");';
$aSql[] = 'INSERT INTO pf_colors VALUES("141", "AntiqueWhite4", "8B8378");';
$aSql[] = 'INSERT INTO pf_colors VALUES("142", "bisque1", "FFE4C4");';
$aSql[] = 'INSERT INTO pf_colors VALUES("143", "bisque2", "EED5B7");';
$aSql[] = 'INSERT INTO pf_colors VALUES("144", "bisque3", "CDB79E");';
$aSql[] = 'INSERT INTO pf_colors VALUES("145", "bisque4", "8B7D6B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("146", "peachPuff1", "FFDAB9");';
$aSql[] = 'INSERT INTO pf_colors VALUES("147", "peachpuff2", "EECBAD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("148", "peachpuff3", "CDAF95");';
$aSql[] = 'INSERT INTO pf_colors VALUES("149", "peachpuff4", "8B7765");';
$aSql[] = 'INSERT INTO pf_colors VALUES("150", "navajowhite1", "FFDEAD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("151", "navajowhite2", "EECFA1");';
$aSql[] = 'INSERT INTO pf_colors VALUES("152", "navajowhite3", "CDB38B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("153", "navajowhite4", "8B795E");';
$aSql[] = 'INSERT INTO pf_colors VALUES("154", "lemonchiffon1", "FFFACD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("155", "lemonchiffon2", "EEE9BF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("156", "lemonchiffon3", "CDC9A5");';
$aSql[] = 'INSERT INTO pf_colors VALUES("157", "lemonchiffon4", "8B8970");';
$aSql[] = 'INSERT INTO pf_colors VALUES("158", "ivory1", "FFFFF0");';
$aSql[] = 'INSERT INTO pf_colors VALUES("159", "ivory2", "EEEEE0");';
$aSql[] = 'INSERT INTO pf_colors VALUES("160", "ivory3", "CDCDC1");';
$aSql[] = 'INSERT INTO pf_colors VALUES("161", "ivory4", "8B8B83");';
$aSql[] = 'INSERT INTO pf_colors VALUES("162", "honeydew", "C1CDC1");';
$aSql[] = 'INSERT INTO pf_colors VALUES("163", "lavenderblush1", "FFF0F5");';
$aSql[] = 'INSERT INTO pf_colors VALUES("164", "lavenderblush2", "EEE0E5");';
$aSql[] = 'INSERT INTO pf_colors VALUES("165", "lavenderblush3", "CDC1C5");';
$aSql[] = 'INSERT INTO pf_colors VALUES("166", "lavenderblush4", "8B8386");';
$aSql[] = 'INSERT INTO pf_colors VALUES("167", "mistyrose1", "FFE4E1");';
$aSql[] = 'INSERT INTO pf_colors VALUES("168", "mistyrose2", "EED5D2");';
$aSql[] = 'INSERT INTO pf_colors VALUES("169", "mistyrose3", "CDB7B5");';
$aSql[] = 'INSERT INTO pf_colors VALUES("170", "mistyrose4", "8B7D7B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("171", "azure1", "F0FFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("172", "azure2", "E0EEEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("173", "azure3", "C1CDCD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("174", "azure4", "838B8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("175", "slateblue1", "836FFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("176", "slateblue2", "7A67EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("177", "slateblue3", "6900CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("178", "slateblue4", "39308B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("179", "royalblue1", "3A76FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("180", "royalblue2", "376EEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("181", "royalblue3", "0500CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("182", "royalblue4", "03348B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("183", "dodgerblue1", "1890FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("184", "dodgerblue2", "0286EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("185", "dodgerblue3", "1474CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("186", "dodgerblue4", "0E078B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("187", "steelblue1", "00B8FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("188", "steelblue2", "00ACEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("189", "steelblue3", "0794CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("190", "steelblue4", "2C648B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("191", "deepskyblue1", "00BFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("192", "deepskyblue2", "00B2EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("193", "deepskyblue3", "009ACD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("194", "deepskyblue4", "00688B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("195", "azulcelestial1", "87CEFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("196", "azulcelestial2", "7EC0EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("197", "azulcelestial3", "6CA6CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("198", "azulcelestial4", "4A708B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("199", "lightskyblue1", "B0E2FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("200", "lightskyblue2", "A4D3EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("201", "lightskyblue3", "8DB6CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("202", "lightskyblue4", "607B8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("203", "slategray1", "C6E2FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("204", "slategray2", "B9D3EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("205", "slategray3", "9FB6CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("206", "slategray4", "6C7B8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("207", "lightsteelblue1", "CAE1FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("208", "lightsteelblue2", "BCD2EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("209", "lightsteelblue3", "A2B5CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("210", "lightsteelblue4", "6E7B8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("211", "azulclaro1", "BFEFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("212", "azulclaro2", "B2DFEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("213", "azulclaro3", "9AC0CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("214", "azulclaro4", "68838B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("215", "lightcyan1", "E0FFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("216", "lightcyan2", "D1EEEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("217", "lightcyan3", "B4CDCD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("218", "lightcyan4", "7A8B8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("219", "paleturquoise1", "BBFFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("220", "paleturquoise2", "AEEEEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("221", "paleturquoise3", "96CDCD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("222", "paleturquoise4", "668B8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("223", "cadetblue1", "98F5FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("224", "cadetblue2", "8EE5EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("225", "cadetblue3", "7AC5CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("226", "cadetblue4", "53868B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("227", "turquoise1", "00F5FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("228", "turquoise2", "00E5EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("229", "turquoise3", "00C5CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("230", "turquoise4", "00868B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("231", "cyan1", "00FFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("232", "cyan2", "00EEEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("233", "cyan3", "00CDCD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("234", "cyan4", "008B8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("235", "darkslategray1", "97FFFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("236", "darkslategray2", "8DEEEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("237", "darkslategray3", "79CDCD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("238", "darkslategray4", "528B8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("239", "aquamarine1", "7FFFD4");';
$aSql[] = 'INSERT INTO pf_colors VALUES("240", "aquamarine2", "76EEC6");';
$aSql[] = 'INSERT INTO pf_colors VALUES("241", "aquamarine3", "66CDAA");';
$aSql[] = 'INSERT INTO pf_colors VALUES("242", "aquamarine4", "458B74");';
$aSql[] = 'INSERT INTO pf_colors VALUES("243", "darkseagreen1", "C1FFC1");';
$aSql[] = 'INSERT INTO pf_colors VALUES("244", "darkseagreen2", "B4EEB4");';
$aSql[] = 'INSERT INTO pf_colors VALUES("245", "darkseagreen3", "9BCD9B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("246", "darkseagreen4", "698B69");';
$aSql[] = 'INSERT INTO pf_colors VALUES("247", "seagreen1", "54FF9F");';
$aSql[] = 'INSERT INTO pf_colors VALUES("248", "seagreen2", "4EEE94");';
$aSql[] = 'INSERT INTO pf_colors VALUES("249", "seagreen3", "43CD80");';
$aSql[] = 'INSERT INTO pf_colors VALUES("250", "seagreen4", "2E8B57");';
$aSql[] = 'INSERT INTO pf_colors VALUES("251", "palegreen1", "9AFF9A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("252", "palegreen2", "90EE90");';
$aSql[] = 'INSERT INTO pf_colors VALUES("253", "palegreen3", "7CCD7C");';
$aSql[] = 'INSERT INTO pf_colors VALUES("254", "palegreen4", "548B54");';
$aSql[] = 'INSERT INTO pf_colors VALUES("255", "springgreen1", "00FF7F");';
$aSql[] = 'INSERT INTO pf_colors VALUES("256", "springgreen2", "00EE76");';
$aSql[] = 'INSERT INTO pf_colors VALUES("257", "springgreen3", "00CD66");';
$aSql[] = 'INSERT INTO pf_colors VALUES("258", "springgreen4", "008B45");';
$aSql[] = 'INSERT INTO pf_colors VALUES("259", "chartreuse1", "7FFF00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("260", "chartreuse2", "76EE00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("261", "chartreuse3", "66CD00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("262", "chartreuse4", "458B00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("263", "olivedrab1", "C0FF3E");';
$aSql[] = 'INSERT INTO pf_colors VALUES("264", "olivedrab2", "B3EE3A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("265", "olivedrab3", "9ACD32");';
$aSql[] = 'INSERT INTO pf_colors VALUES("266", "olivedrab4", "698B22");';
$aSql[] = 'INSERT INTO pf_colors VALUES("267", "darkolivegreen1", "CAFF70");';
$aSql[] = 'INSERT INTO pf_colors VALUES("268", "darkolivegreen2", "BCEE68");';
$aSql[] = 'INSERT INTO pf_colors VALUES("269", "darkolivegreen3", "A2CD5A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("270", "darkolivegreen4", "6E8B3D");';
$aSql[] = 'INSERT INTO pf_colors VALUES("271", "khaki1", "FFF68F");';
$aSql[] = 'INSERT INTO pf_colors VALUES("272", "khaki2", "EEE685");';
$aSql[] = 'INSERT INTO pf_colors VALUES("273", "khaki3", "CDC673");';
$aSql[] = 'INSERT INTO pf_colors VALUES("274", "khaki4", "8B864E");';
$aSql[] = 'INSERT INTO pf_colors VALUES("275", "lightgoldenrod1", "FFEC8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("276", "lightgoldenrod2", "EEDC82");';
$aSql[] = 'INSERT INTO pf_colors VALUES("277", "lightgoldenrod3", "CDBE70");';
$aSql[] = 'INSERT INTO pf_colors VALUES("278", "lightgoldenrod4", "8B814C");';
$aSql[] = 'INSERT INTO pf_colors VALUES("279", "amarelo1", "FFFF00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("280", "amarelo2", "EEEE00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("281", "amarelo3", "CDCD00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("282", "amarelo4", "8B8B00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("283", "ouro1", "FFD700");';
$aSql[] = 'INSERT INTO pf_colors VALUES("284", "ouro2", "EEC900");';
$aSql[] = 'INSERT INTO pf_colors VALUES("285", "ouro3", "CDAD00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("286", "ouro4", "8B7500");';
$aSql[] = 'INSERT INTO pf_colors VALUES("287", "goldenrod1", "FFC125");';
$aSql[] = 'INSERT INTO pf_colors VALUES("288", "goldenrod2", "EEB422");';
$aSql[] = 'INSERT INTO pf_colors VALUES("289", "goldenrod3", "CD9B1D");';
$aSql[] = 'INSERT INTO pf_colors VALUES("290", "goldenrod4", "8B6914");';
$aSql[] = 'INSERT INTO pf_colors VALUES("291", "darkgoldenrod1", "FFB90F");';
$aSql[] = 'INSERT INTO pf_colors VALUES("292", "darkgoldenrod2", "EEAD0E");';
$aSql[] = 'INSERT INTO pf_colors VALUES("293", "darkgoldenrod3", "CD950C");';
$aSql[] = 'INSERT INTO pf_colors VALUES("294", "darkgoldenrod4", "8B6508");';
$aSql[] = 'INSERT INTO pf_colors VALUES("295", "rosybrown1", "FFC1C1");';
$aSql[] = 'INSERT INTO pf_colors VALUES("296", "rosybrown2", "EEB4B4");';
$aSql[] = 'INSERT INTO pf_colors VALUES("297", "rosybrown3", "CD9B9B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("298", "rosybrown4", "8B6969");';
$aSql[] = 'INSERT INTO pf_colors VALUES("299", "indianred1", "FF6A6A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("300", "indianred2", "EE6363");';
$aSql[] = 'INSERT INTO pf_colors VALUES("301", "indianred3", "CD5555");';
$aSql[] = 'INSERT INTO pf_colors VALUES("302", "indianred4", "8B3A3A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("303", "sienna1", "FF8247");';
$aSql[] = 'INSERT INTO pf_colors VALUES("304", "sienna2", "EE7942");';
$aSql[] = 'INSERT INTO pf_colors VALUES("305", "sienna3", "CD6839");';
$aSql[] = 'INSERT INTO pf_colors VALUES("306", "sienna4", "8B4726");';
$aSql[] = 'INSERT INTO pf_colors VALUES("307", "burlywood1", "FFD39B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("308", "burlywood2", "EEC591");';
$aSql[] = 'INSERT INTO pf_colors VALUES("309", "burlywood3", "CDAA7D");';
$aSql[] = 'INSERT INTO pf_colors VALUES("310", "burlywood4", "8B7355");';
$aSql[] = 'INSERT INTO pf_colors VALUES("311", "wheat1", "FFE7BA");';
$aSql[] = 'INSERT INTO pf_colors VALUES("312", "wheat2", "EED8AE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("313", "wheat3", "CDBA96");';
$aSql[] = 'INSERT INTO pf_colors VALUES("314", "wheat4", "8B7E66");';
$aSql[] = 'INSERT INTO pf_colors VALUES("315", "tan1", "FFA54F");';
$aSql[] = 'INSERT INTO pf_colors VALUES("316", "tan2", "EE9A49");';
$aSql[] = 'INSERT INTO pf_colors VALUES("317", "tan3", "CD853F");';
$aSql[] = 'INSERT INTO pf_colors VALUES("318", "tan4", "8B5A2B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("319", "chocolate1", "FF7F24");';
$aSql[] = 'INSERT INTO pf_colors VALUES("320", "chocolate2", "EE7621");';
$aSql[] = 'INSERT INTO pf_colors VALUES("321", "chocolate3", "CD661D");';
$aSql[] = 'INSERT INTO pf_colors VALUES("322", "chocolate4", "8B4513");';
$aSql[] = 'INSERT INTO pf_colors VALUES("323", "firebrick1", "FF3030");';
$aSql[] = 'INSERT INTO pf_colors VALUES("324", "firebrick2", "EE2C2C");';
$aSql[] = 'INSERT INTO pf_colors VALUES("325", "firebrick3", "CD2626");';
$aSql[] = 'INSERT INTO pf_colors VALUES("326", "firebrick4", "8B1A1A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("327", "brown1", "FF4040");';
$aSql[] = 'INSERT INTO pf_colors VALUES("328", "brown2", "EE3B3B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("329", "brown3", "CD3333");';
$aSql[] = 'INSERT INTO pf_colors VALUES("330", "brown4", "8B2323");';
$aSql[] = 'INSERT INTO pf_colors VALUES("331", "salmon1", "FF8C69");';
$aSql[] = 'INSERT INTO pf_colors VALUES("332", "salmon2", "EE8262");';
$aSql[] = 'INSERT INTO pf_colors VALUES("333", "salmon3", "CD7054");';
$aSql[] = 'INSERT INTO pf_colors VALUES("334", "salmon4", "8B4C39");';
$aSql[] = 'INSERT INTO pf_colors VALUES("335", "lightsalmon1", "FFA07A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("336", "lightsalmon2", "EE9572");';
$aSql[] = 'INSERT INTO pf_colors VALUES("337", "lightsalmon3", "CD8162");';
$aSql[] = 'INSERT INTO pf_colors VALUES("338", "lightsalmon4", "8B5742");';
$aSql[] = 'INSERT INTO pf_colors VALUES("339", "laranja1", "FFA500");';
$aSql[] = 'INSERT INTO pf_colors VALUES("340", "laranja2", "EE9A00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("341", "laranja3", "CD8500");';
$aSql[] = 'INSERT INTO pf_colors VALUES("342", "laranja4", "8B5A00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("343", "darkorange1", "FF7F00");';
$aSql[] = 'INSERT INTO pf_colors VALUES("344", "darkorange2", "EE7600");';
$aSql[] = 'INSERT INTO pf_colors VALUES("345", "darkorange3", "CD6600");';
$aSql[] = 'INSERT INTO pf_colors VALUES("346", "darkorange4", "8B4500");';
$aSql[] = 'INSERT INTO pf_colors VALUES("347", "coral1", "FF7256");';
$aSql[] = 'INSERT INTO pf_colors VALUES("348", "coral2", "EE6A50");';
$aSql[] = 'INSERT INTO pf_colors VALUES("349", "coral3", "CD5B45");';
$aSql[] = 'INSERT INTO pf_colors VALUES("350", "coral4", "8B3E2F");';
$aSql[] = 'INSERT INTO pf_colors VALUES("351", "tomato1", "FF6347");';
$aSql[] = 'INSERT INTO pf_colors VALUES("352", "tomato2", "EE5C42");';
$aSql[] = 'INSERT INTO pf_colors VALUES("353", "tomato3", "CD4F39");';
$aSql[] = 'INSERT INTO pf_colors VALUES("354", "tomato4", "8B3626");';
$aSql[] = 'INSERT INTO pf_colors VALUES("355", "orangered1", "FF4500");';
$aSql[] = 'INSERT INTO pf_colors VALUES("356", "orangered2", "EE4000");';
$aSql[] = 'INSERT INTO pf_colors VALUES("357", "orangered3", "CD3700");';
$aSql[] = 'INSERT INTO pf_colors VALUES("358", "orangered4", "8B2500");';
$aSql[] = 'INSERT INTO pf_colors VALUES("359", "deeppink1", "FF1493");';
$aSql[] = 'INSERT INTO pf_colors VALUES("360", "deeppink2", "EE1289");';
$aSql[] = 'INSERT INTO pf_colors VALUES("361", "deeppink3", "CD1076");';
$aSql[] = 'INSERT INTO pf_colors VALUES("362", "deeppink4", "8B0A50");';
$aSql[] = 'INSERT INTO pf_colors VALUES("363", "hotpink1", "FF6EB4");';
$aSql[] = 'INSERT INTO pf_colors VALUES("364", "hotpink2", "EE6AA7");';
$aSql[] = 'INSERT INTO pf_colors VALUES("365", "hotpink3", "CD6090");';
$aSql[] = 'INSERT INTO pf_colors VALUES("366", "hotpink4", "8B3A62");';
$aSql[] = 'INSERT INTO pf_colors VALUES("367", "rosa1", "FFB5C5");';
$aSql[] = 'INSERT INTO pf_colors VALUES("368", "rosa2", "EEA9B8");';
$aSql[] = 'INSERT INTO pf_colors VALUES("369", "rosa3", "CD919E");';
$aSql[] = 'INSERT INTO pf_colors VALUES("370", "rosa4", "8B636C");';
$aSql[] = 'INSERT INTO pf_colors VALUES("371", "lightpink1", "FFAEB9");';
$aSql[] = 'INSERT INTO pf_colors VALUES("372", "lightpink2", "EEA2AD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("373", "lightpink3", "CD8C95");';
$aSql[] = 'INSERT INTO pf_colors VALUES("374", "lightpink4", "8B5F65");';
$aSql[] = 'INSERT INTO pf_colors VALUES("375", "palevioletred1", "FF82AB");';
$aSql[] = 'INSERT INTO pf_colors VALUES("376", "palevioletred2", "EE799F");';
$aSql[] = 'INSERT INTO pf_colors VALUES("377", "palevioletred3", "CD6889");';
$aSql[] = 'INSERT INTO pf_colors VALUES("378", "palevioletred4", "8B475D");';
$aSql[] = 'INSERT INTO pf_colors VALUES("379", "maroon1", "FF34B3");';
$aSql[] = 'INSERT INTO pf_colors VALUES("380", "maroon2", "EE30A7");';
$aSql[] = 'INSERT INTO pf_colors VALUES("381", "maroon3", "CD2990");';
$aSql[] = 'INSERT INTO pf_colors VALUES("382", "maroon4", "8B1C62");';
$aSql[] = 'INSERT INTO pf_colors VALUES("383", "violetred1", "FF3E96");';
$aSql[] = 'INSERT INTO pf_colors VALUES("384", "violetred2", "EE3A8C");';
$aSql[] = 'INSERT INTO pf_colors VALUES("385", "violetred3", "CD3278");';
$aSql[] = 'INSERT INTO pf_colors VALUES("386", "violetred4", "8B2252");';
$aSql[] = 'INSERT INTO pf_colors VALUES("387", "magenta1", "FF00FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("388", "magenta2", "EE00EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("389", "magenta3", "CD00CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("390", "magenta4", "8B008B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("391", "mediumred", "8C2222");';
$aSql[] = 'INSERT INTO pf_colors VALUES("392", "orchid1", "FF83FA");';
$aSql[] = 'INSERT INTO pf_colors VALUES("393", "orchid2", "EE7AE9");';
$aSql[] = 'INSERT INTO pf_colors VALUES("394", "orchid3", "CD69C9");';
$aSql[] = 'INSERT INTO pf_colors VALUES("395", "orchid4", "8B4789");';
$aSql[] = 'INSERT INTO pf_colors VALUES("396", "plum1", "FFBBFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("397", "plum2", "EEAEEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("398", "plum3", "CD96CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("399", "plum4", "8B668B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("400", "mediumorchid1", "E066FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("401", "mediumorchid2", "D15FEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("402", "mediumorchid3", "B452CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("403", "mediumorchid4", "7A378B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("404", "darkorchid1", "BF3EFF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("405", "darkorchid2", "B23AEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("406", "darkorchid3", "9A32CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("407", "darkorchid4", "68228B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("408", "purple1", "9B30FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("409", "purple2", "912CEE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("410", "purple3", "7D26CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("411", "purple4", "551A8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("412", "mediumpurple1", "AB82FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("413", "mediumpurple2", "9F79EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("414", "mediumpurple3", "8968CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("415", "mediumpurple4", "5D478B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("416", "thistle1", "FFE1FF");';
$aSql[] = 'INSERT INTO pf_colors VALUES("417", "thistle2", "EED2EE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("418", "thistle3", "CDB5CD");';
$aSql[] = 'INSERT INTO pf_colors VALUES("419", "thistle4", "8B7B8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("420", "cinza1", "0A0A0A");';
$aSql[] = 'INSERT INTO pf_colors VALUES("421", "cinza2", "28281E");';
$aSql[] = 'INSERT INTO pf_colors VALUES("422", "cinza3", "464646");';
$aSql[] = 'INSERT INTO pf_colors VALUES("423", "cinza4", "646464");';
$aSql[] = 'INSERT INTO pf_colors VALUES("424", "cinza5", "828282");';
$aSql[] = 'INSERT INTO pf_colors VALUES("425", "cinza6", "A0A0A0");';
$aSql[] = 'INSERT INTO pf_colors VALUES("426", "cinza7", "BEBEBE");';
$aSql[] = 'INSERT INTO pf_colors VALUES("427", "cinza8", "D2D2D2");';
$aSql[] = 'INSERT INTO pf_colors VALUES("428", "cinza9", "F0F0F0");';
$aSql[] = 'INSERT INTO pf_colors VALUES("429", "cinzaescuro", "646464");';
$aSql[] = 'INSERT INTO pf_colors VALUES("430", "azulescuro", "00008B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("431", "darkcyan", "008B8B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("432", "darkmagenta", "8B008B");';
$aSql[] = 'INSERT INTO pf_colors VALUES("433", "vermelhoescuro", "8B0000");';
$aSql[] = 'INSERT INTO pf_colors VALUES("434", "prata", "C0C0C0");';
$aSql[] = 'INSERT INTO pf_colors VALUES("435", "eggplant", "90B0A8");';
$aSql[] = 'INSERT INTO pf_colors VALUES("436", "verdeclaro", "90EE90");';

// Layouts ( pf_layouts )
$aSql[] = 'INSERT INTO pf_layouts VALUES("0", "", "ordinary");';
$aSql[] = 'INSERT INTO pf_layouts VALUES("1", "", "top-left");';
$aSql[] = 'INSERT INTO pf_layouts VALUES("2", "", "top-center");';

// HTML properties ( pf_properties )
$aSql[] = 'INSERT INTO pf_properties VALUES("00", "00", "maxlength");';
$aSql[] = 'INSERT INTO pf_properties VALUES("01", "00", "size");';
$aSql[] = 'INSERT INTO pf_properties VALUES("02", "01", "maxlength");';
$aSql[] = 'INSERT INTO pf_properties VALUES("03", "01", "size");';
$aSql[] = 'INSERT INTO pf_properties VALUES("04", "05", "rows");';
$aSql[] = 'INSERT INTO pf_properties VALUES("05", "06", "rows");';
$aSql[] = 'INSERT INTO pf_properties VALUES("06", "07", "MAX_FILE_SIZE");';

// Field types (pf_types)
$aSql[] = 'INSERT INTO pf_types VALUES("00", "Text", "MDG", "", "string");';
$aSql[] = 'INSERT INTO pf_types VALUES("01", "Password", "DG", "", "string");';
$aSql[] = 'INSERT INTO pf_types VALUES("02", "Checkbox", "IMCSB", "", "checkbox");';
$aSql[] = 'INSERT INTO pf_types VALUES("03", "Radio button", "IRMSB", "", "");';
$aSql[] = 'INSERT INTO pf_types VALUES("04", "Select", "IRMO", "", "");';
$aSql[] = 'INSERT INTO pf_types VALUES("05", "Multiple select", "IMCOW", "", "multiple");';
$aSql[] = 'INSERT INTO pf_types VALUES("06", "Textarea", "DGW", "", "string");';
$aSql[] = 'INSERT INTO pf_types VALUES("07", "File upload", "U", "", "");';
$aSql[] = 'INSERT INTO pf_types VALUES("08", "Hidden", "-^MD", "", "");';
$aSql[] = 'INSERT INTO pf_types VALUES("09", "HTML", "-DH", "", "");';
$aSql[] = 'INSERT INTO pf_types VALUES("10", "Date", "d", "", "");';

// Examples:

// Mail templates (pf_mail_tpls)
$aSql[] = 'INSERT INTO pf_mail_tpls VALUES( "b2951c8b27", "Universal Template", "[form-name]<br>----<br>[form-data]<br>----<br>Client&#39;s IP: [ip-address]", "phpForms script&lt;phpforms@site.com&gt;", "Form submit", "0" );';

// Predefied lists (pf_predefined)
$aSql[] = 'INSERT INTO pf_predefined VALUES( "ab7d123d56", "Countries" );';
$aSql[] = 'INSERT INTO pf_predefined VALUES( "d805d29710", "US states" );';

// Predefied values (pf_pre_values)
// Countries
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "636b971c46a9", "ab7d123d56", "Afghanistan", "Afganistan", "0");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2bc7ddfe2b5a", "ab7d123d56", "Albania", "Albania", "1");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "62fc734ee2bf", "ab7d123d56", "Algeria", "Algeria", "2");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "1f8a6c8aa82d", "ab7d123d56", "American Samoa", "American Samoa", "3");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "274d498a2667", "ab7d123d56", "Andorra", "Andorra", "4");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f1f65393c512", "ab7d123d56", "Angola", "Angola", "5");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "7c3d9f08657b", "ab7d123d56", "Anguilla", "Anguilla", "6");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "14cc1539c0f8", "ab7d123d56", "Antigua & Barbuda", "Antigua & Barbuda", "7");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f99d7d9b3f9d", "ab7d123d56", "Argentina", "Argentina", "8");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "4d19db4f465e", "ab7d123d56", "Armenia", "Armenia", "9");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "da2bb1e45abc", "ab7d123d56", "Aruba", "Aruba", "10");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "727f44d36166", "ab7d123d56", "Australia", "Australia", "11");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8fb13f0817c7", "ab7d123d56", "Austria", "Austria", "12");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "4737c2365c8b", "ab7d123d56", "Azerbaijan", "Azerbaijan", "13");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "ae493b7f51f3", "ab7d123d56", "Azores", "Azores", "14");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "119bc9c15068", "ab7d123d56", "Bahamas", "Bahamas", "15");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "44a2e7fb3b88", "ab7d123d56", "Bahrain", "Bahrain", "16");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "13f8c3ca97b9", "ab7d123d56", "Bangladesh", "Bangladesh", "17");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "6496c2bb0ac4", "ab7d123d56", "Barbados", "Barbados", "18");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "642915b7dca4", "ab7d123d56", "Belarus", "Belarus", "19");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "1238f0e4e4b1", "ab7d123d56", "Belgium", "Belgium", "20");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "64c847b07d49", "ab7d123d56", "Belize", "Belize", "21");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d39b88647543", "ab7d123d56", "Benin", "Benin", "22");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "45b7eea76357", "ab7d123d56", "Bermuda", "Bermuda", "23");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "7e8a4a658e3f", "ab7d123d56", "Bhutan", "Bhutan", "24");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d4a9f43bed1e", "ab7d123d56", "Bolivia", "Bolivia", "25");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8bcf7a984c12", "ab7d123d56", "Bonaire", "Bonaire", "26");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "1c05be79c4ce", "ab7d123d56", "Bosnia & Herzegovina", "Bosnia & Herzegovina", "27");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c541dd750696", "ab7d123d56", "Botswana", "Botswana", "28");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "5e971c396af6", "ab7d123d56", "Brazil", "Brazil", "29");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c37448f97c32", "ab7d123d56", "British Indian Ocean Ter", "British Indian Ocean Ter", "30");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "ca3f12ac62ef", "ab7d123d56", "Brunei", "Brunei", "31");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "90c2f62c70ff", "ab7d123d56", "Bulgaria", "Bulgaria", "32");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8a68055a27f3", "ab7d123d56", "Burkina Faso", "Burkina Faso", "33");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c4eb66fff9e4", "ab7d123d56", "Burundi", "Burundi", "34");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f88c4b618231", "ab7d123d56", "Cambodia", "Cambodia", "35");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "babdde8deece", "ab7d123d56", "Cameroon", "Cameroon", "36");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "eb709ff610b5", "ab7d123d56", "Canada", "Canada", "37");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "72655db0a952", "ab7d123d56", "Canary Islands", "Canary Islands", "38");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "313590e3e13d", "ab7d123d56", "Cape Verde", "Cape Verde", "39");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "fcd0b7fcc0eb", "ab7d123d56", "Cayman Islands", "Cayman Islands", "40");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "076a1096a2fd", "ab7d123d56", "Central African Republic", "Central African Republic", "41");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e7b8cf94d9ca", "ab7d123d56", "Chad", "Chad", "42");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "89e4f9c8fa2a", "ab7d123d56", "Channel Islands", "Channel Islands", "43");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "5b1a63923617", "ab7d123d56", "Chile", "Chile", "44");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "cda6fc088dbd", "ab7d123d56", "China", "China", "45");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "4fadb808eed3", "ab7d123d56", "Christmas Island", "Christmas Island", "46");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "10459ebe4565", "ab7d123d56", "Cocos Island", "Cocos Island", "47");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e88dbefa4c9d", "ab7d123d56", "Columbia", "Columbia", "48");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "450b3d131af9", "ab7d123d56", "Comoros", "Comoros", "49");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "90cce47362c9", "ab7d123d56", "Congo", "Congo", "50");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c8c88b51b2a2", "ab7d123d56", "Cook Islands", "Cook Islands", "51");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "024130edcfac", "ab7d123d56", "Costa Rica", "Costa Rica", "52");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d0bff42b3269", "ab7d123d56", "Cote D\'Ivoire", "Cote DIvoire", "53");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "3e8b7362c6eb", "ab7d123d56", "Croatia", "Croatia", "54");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "4d7a43e41001", "ab7d123d56", "Cuba", "Cuba", "55");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "9b2f6084ac8c", "ab7d123d56", "Curacao", "Curaco", "56");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "fbf07a560468", "ab7d123d56", "Cyprus", "Cyprus", "57");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "6532c3181398", "ab7d123d56", "Czech Republic", "Czech Republic", "58");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "0fa4c14d064e", "ab7d123d56", "Denmark", "Denmark", "59");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "03372275438f", "ab7d123d56", "Djibouti", "Djibouti", "60");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "ed317f3d06bd", "ab7d123d56", "Dominica", "Dominica", "61");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "89e6f39d77ec", "ab7d123d56", "Dominican Republic", "Dominican Republic", "62");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2f523d5f429f", "ab7d123d56", "East Timor", "East Timor", "63");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "7befe44a1965", "ab7d123d56", "Ecuador", "Ecuador", "64");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "49db40e03c5e", "ab7d123d56", "Egypt", "Egypt", "65");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2f0282127f15", "ab7d123d56", "El Salvador", "El Salvador", "66");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "460438ffaa7e", "ab7d123d56", "Equatorial Guinea", "Equatorial Guinea", "67");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "062466c190e4", "ab7d123d56", "Eritrea", "Eritrea", "68");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "40c7b50915f6", "ab7d123d56", "Estonia", "Estonia", "69");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "4705a68744f4", "ab7d123d56", "Ethiopia", "Ethiopia", "70");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e170fa907c8f", "ab7d123d56", "Falkland Islands", "Falkland Islands", "71");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c5250cc7119d", "ab7d123d56", "Faroe Islands", "Faroe Islands", "72");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "6fb85cc3a0f2", "ab7d123d56", "Fiji", "Fiji", "73");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "9be8005247ef", "ab7d123d56", "Finland", "Finland", "74");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "14a0f4109002", "ab7d123d56", "France", "France", "75");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "5b4247a9e61b", "ab7d123d56", "French Guiana", "French Guiana", "76");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2e3938538879", "ab7d123d56", "French Polynesia", "French Polynesia", "77");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "cd658bcf4dae", "ab7d123d56", "French Southern Ter", "French Southern Ter", "78");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "ab51686c5351", "ab7d123d56", "Gabon", "Gabon", "79");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "6e8017d9aec5", "ab7d123d56", "Gambia", "Gambia", "80");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "862f6ffafd21", "ab7d123d56", "Georgia", "Georgia", "81");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c7a93f81085b", "ab7d123d56", "Germany", "Germany", "82");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "27ac19d2f6e1", "ab7d123d56", "Ghana", "Ghana", "83");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "1ba26877fd8e", "ab7d123d56", "Gibraltar", "Gibraltar", "84");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "01e9d2b39d7a", "ab7d123d56", "Great Britain", "Great Britain", "85");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "56f31153b485", "ab7d123d56", "Greece", "Greece", "86");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "ceca58239deb", "ab7d123d56", "Greenland", "Greenland", "87");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "5517e61e3512", "ab7d123d56", "Grenada", "Grenada", "88");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "16d9e6f35a08", "ab7d123d56", "Guadeloupe", "Guadeloupe", "89");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "3560239fe92a", "ab7d123d56", "Guam", "Guam", "90");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "0411f9ffd5bc", "ab7d123d56", "Guatemala", "Guatemala", "91");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "eaab3c5b30cf", "ab7d123d56", "Guinea", "Guinea", "92");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d2813b698c55", "ab7d123d56", "Guyana", "Guyana", "93");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "03972622630a", "ab7d123d56", "Haiti", "Haiti", "94");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c7fe75504331", "ab7d123d56", "Hawaii", "Hawaii", "95");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "b9814b6ea7a3", "ab7d123d56", "Honduras", "Honduras", "96");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8b11e0ef498b", "ab7d123d56", "Hong Kong", "Hong Kong", "97");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "26f8871124c2", "ab7d123d56", "Hungary", "Hungary", "98");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8d645d489477", "ab7d123d56", "Iceland", "Iceland", "99");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "a35524ebf78f", "ab7d123d56", "India", "India", "100");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c832c44b2102", "ab7d123d56", "Indonesia", "Indonesia", "101");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2b44e13ecbcd", "ab7d123d56", "Iran", "Iran", "102");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2aaa325eda1f", "ab7d123d56", "Iraq", "Iraq", "103");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "565e39b6a27c", "ab7d123d56", "Ireland", "Ireland", "104");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "b4dcc7f973cf", "ab7d123d56", "Isle of Man", "Isle of Man", "105");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f0f571ecc504", "ab7d123d56", "Israel", "Israel", "106");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "be3957bc94c0", "ab7d123d56", "Italy", "Italy", "107");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "53caca3491a5", "ab7d123d56", "Jamaica", "Jamaica", "108");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d37982b7ed9c", "ab7d123d56", "Japan", "Japan", "109");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f3f37be33fa0", "ab7d123d56", "Jordan", "Jordan", "110");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "92b8a8f4dae9", "ab7d123d56", "Kazakhstan", "Kazakhstan", "111");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "3844b3e11a6c", "ab7d123d56", "Kenya", "Kenya", "112");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c763643480dd", "ab7d123d56", "Kiribati", "Kiribati", "113");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "6c8d46b78099", "ab7d123d56", "Korea North", "Korea North", "114");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e8b1a27eb494", "ab7d123d56", "Korea South", "Korea Sout", "115");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c4c360539ef5", "ab7d123d56", "Kuwait", "Kuwait", "116");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "428dafdab8a7", "ab7d123d56", "Kyrgyzstan", "Kyrgyzstan", "117");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "32c6581c4ce9", "ab7d123d56", "Laos", "Laos", "118");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "4c3d10724b58", "ab7d123d56", "Latvia", "Latvia", "119");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "0defc094fe28", "ab7d123d56", "Lebanon", "Lebanon", "120");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "9cda7957dba2", "ab7d123d56", "Lesotho", "Lesotho", "121");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "25ffc69c69f2", "ab7d123d56", "Liberia", "Liberia", "122");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d9b9b493ddee", "ab7d123d56", "Libya", "Libya", "123");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f4b6a627e3ed", "ab7d123d56", "Liechtenstein", "Liechtenstein", "124");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "68af079f4818", "ab7d123d56", "Lithuania", "Lithuania", "125");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2553ef1e3b59", "ab7d123d56", "Luxembourg", "Luxembourg", "126");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "35a498862ab7", "ab7d123d56", "Macau", "Macau", "127");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "398a3ae142b9", "ab7d123d56", "Macedonia", "Macedonia", "128");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f197ed88f4f6", "ab7d123d56", "Madagascar", "Madagascar", "129");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "32afbf810c15", "ab7d123d56", "Malawi", "Malawi", "130");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "dd0660024de1", "ab7d123d56", "Malaysia", "Malaysia", "131");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "833ecbad48a3", "ab7d123d56", "Maldives", "Maldives", "132");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "b8420000173e", "ab7d123d56", "Mali", "Mali", "133");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8fdc380bcfac", "ab7d123d56", "Malta", "Malta", "134");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "3f83a2a8aa34", "ab7d123d56", "Marshall Islands", "Marshall Islands", "135");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e326c40a6e43", "ab7d123d56", "Martinique", "Martinique", "136");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "0878ae601cf7", "ab7d123d56", "Mauritania", "Mauritania", "137");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "868c023636b1", "ab7d123d56", "Mauritius", "Mauritius", "138");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c8000f480fca", "ab7d123d56", "Mayotte", "Mayotte", "139");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "b7a82982910f", "ab7d123d56", "Mexico", "Mexico", "140");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "259471a41770", "ab7d123d56", "Midway Islands", "Midway Islands", "141");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2cbd07b5b40c", "ab7d123d56", "Moldova", "Moldova", "142");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "60adf1e97420", "ab7d123d56", "Monaco", "Monaco", "143");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "23550ad06a06", "ab7d123d56", "Mongolia", "Mongolia", "144");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "fd870dae2bcb", "ab7d123d56", "Montserrat", "Montserrat", "145");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "18452fa57ee8", "ab7d123d56", "Morocco", "Morocco", "146");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "ba11c0a7aafc", "ab7d123d56", "Mozambique", "Mozambique", "147");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f5e9a5c8579d", "ab7d123d56", "Myanmar", "Myanmar", "148");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "b81c2774156c", "ab7d123d56", "Nambia", "Nambia", "149");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "9e1da3675ca8", "ab7d123d56", "Nauru", "Nauru", "150");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "891f298ff189", "ab7d123d56", "Nepal", "Nepal", "151");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f9b478df7de4", "ab7d123d56", "Netherland Antilles", "Netherland Antilles", "152");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "7ed3c7245504", "ab7d123d56", "Netherlands", "Netherlands", "153");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "6c66d984edd7", "ab7d123d56", "Nevis", "Nevis", "154");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "0ef41947ba2c", "ab7d123d56", "New Caledonia", "New Caledonia", "155");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e06c7e21cfdb", "ab7d123d56", "New Zealand", "New Zealand", "156");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "634125cf0448", "ab7d123d56", "Nicaragua", "Nicaragua", "157");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2f1e976ec80c", "ab7d123d56", "Niger", "Niger", "158");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "28debb2dc50a", "ab7d123d56", "Nigeria", "Nigeria", "159");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "9b18fff817f5", "ab7d123d56", "Niue", "Niue", "160");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "88e86f043e2d", "ab7d123d56", "Norfolk Island", "Norfolk Island", "161");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f4870d352c3a", "ab7d123d56", "Norway", "Norway", "162");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f0aa4f703430", "ab7d123d56", "Oman", "Oman", "163");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d99a994828bb", "ab7d123d56", "Pakistan", "Pakistan", "164");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "0646f445a3de", "ab7d123d56", "Palau Island", "Palau Island", "165");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "b1ed0c6bfcd0", "ab7d123d56", "Palestine", "Palestine", "166");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "5fdff0613a1f", "ab7d123d56", "Panama", "Panama", "167");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8ab77a416aae", "ab7d123d56", "Papua New Guinea", "Papua New Guinea", "168");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e0eb9a229f0c", "ab7d123d56", "Paraguay", "Paraguay", "169");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "4c7f07ae48da", "ab7d123d56", "Peru", "Peru", "170");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "7380d2c3d090", "ab7d123d56", "Philippines", "Phillipines", "171");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8db0e8b9422c", "ab7d123d56", "Pitcairn Island", "Pitcairn Island", "172");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f430f88f1996", "ab7d123d56", "Poland", "Poland", "173");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "a08bf1acfc04", "ab7d123d56", "Portugal", "Portugal", "174");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "22f7a221d3e1", "ab7d123d56", "Puerto Rico", "Puerto Rico", "175");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e236935dfd72", "ab7d123d56", "Qatar", "Qatar", "176");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "93b1138908c2", "ab7d123d56", "Reunion", "Reunion", "177");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "dedf6cf1fb69", "ab7d123d56", "Romania", "Romania", "178");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "7c0d6c9d5546", "ab7d123d56", "Russia", "Russia", "179");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "3809699e763d", "ab7d123d56", "Rwanda", "Rwanda", "180");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e03717aed8fe", "ab7d123d56", "Saipan", "Saipan", "181");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c790a635f214", "ab7d123d56", "Samoa", "Samoa", "182");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "9e33acce3424", "ab7d123d56", "Samoa American", "Samoa American", "183");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2b76954a9315", "ab7d123d56", "San Marino", "San Marino", "184");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "626be881af20", "ab7d123d56", "Sao Tome & Principe", "Sao Tome & Principe", "185");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f622f5199c2e", "ab7d123d56", "Saudi Arabia", "Saudi Arabia", "186");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "1aab230a89bd", "ab7d123d56", "Senegal", "Senegal", "187");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e911e53fc2cc", "ab7d123d56", "Serbia & Montenegro", "Serbia & Montenegro", "188");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "849a22117b5e", "ab7d123d56", "Seychelles", "Seychelles", "189");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "018bcee9e8d9", "ab7d123d56", "Sierra Leone", "Sierra Leone", "190");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f7666bd345af", "ab7d123d56", "Singapore", "Singapore", "191");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "de4692aeb938", "ab7d123d56", "Slovakia", "Slovakia", "192");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "b34a4b675398", "ab7d123d56", "Slovenia", "Slovenia", "193");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "be1cef83c068", "ab7d123d56", "Solomon Islands", "Solomon Islands", "194");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d7c924a89694", "ab7d123d56", "Somalia", "Somalia", "195");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "fa1e41d98f1f", "ab7d123d56", "South Africa", "South Africa", "196");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "db7e18f70a64", "ab7d123d56", "Spain", "Spain", "197");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "af0f0cedeba0", "ab7d123d56", "Sri Lanka", "Sri Lanka", "198");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "7b3544f4993f", "ab7d123d56", "St Barthelemy", "St Barthelemy", "199");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "981bd3b23046", "ab7d123d56", "St Eustatius", "St Eustatius", "200");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "fcad44278aa0", "ab7d123d56", "St Helena", "St Helena", "201");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f830a04aa992", "ab7d123d56", "St Kitts-Nevis", "St Kitts-Nevis", "202");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "094837d4e362", "ab7d123d56", "St Lucia", "St Lucia", "203");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "28c31334ea91", "ab7d123d56", "St Maarten", "St Maarten", "204");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "b1ec055eda01", "ab7d123d56", "St Pierre & Miquelon", "St Pierre & Miquelon", "205");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "82e56c9419fa", "ab7d123d56", "St Vincent & Grenadines", "St Vincent & Grenadines", "206");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8692222418d8", "ab7d123d56", "Sudan", "Sudan", "207");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "359d2a0b4bdd", "ab7d123d56", "Suriname", "Suriname", "208");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "80ae8b2cb423", "ab7d123d56", "Swaziland", "Swaziland", "209");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "59149f95d114", "ab7d123d56", "Sweden", "Sweden", "210");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "30ba51bd149a", "ab7d123d56", "Switzerland", "Switzerland", "211");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "ca1c9aea4bdd", "ab7d123d56", "Syria", "Syria", "212");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "303c652647c1", "ab7d123d56", "Tahiti", "Tahiti", "213");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "44d184de5b14", "ab7d123d56", "Taiwan", "Taiwan", "214");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "46b43af26f24", "ab7d123d56", "Tajikistan", "Tajikistan", "215");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "4418806fbe8c", "ab7d123d56", "Tanzania", "Tanzania", "216");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "872961e191a5", "ab7d123d56", "Thailand", "Thailand", "217");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d3b892963bcb", "ab7d123d56", "Togo", "Togo", "218");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "306fc7148c56", "ab7d123d56", "Tokelau", "Tokelau", "219");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "36f05df7d2b3", "ab7d123d56", "Tonga", "Tonga", "220");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "1d2f63cde3f8", "ab7d123d56", "Trinidad & Tobago", "Trinidad & Tobago", "221");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d00ad57f952d", "ab7d123d56", "Tunisia", "Tunisia", "222");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d0cf249cfb3b", "ab7d123d56", "Turkey", "Turkey", "223");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "45f6e325cd76", "ab7d123d56", "Turkmenistan", "Turkmenistan", "224");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "99f074cec67e", "ab7d123d56", "Turks & Caicos Is", "Turks & Caicos Is", "225");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "96707e7581aa", "ab7d123d56", "Tuvalu", "Tuvalu", "226");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c38e99da50de", "ab7d123d56", "Uganda", "Uganda", "227");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "48f2803e2baa", "ab7d123d56", "Ukraine", "Ukraine", "228");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "add70dec473e", "ab7d123d56", "United Arab Emirates", "United Arab Erimates", "229");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "adebb3370865", "ab7d123d56", "United Kingdom", "United Kingdom", "230");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "64b6ef25c696", "ab7d123d56", "United States of America", "United States of America", "231");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "79163af0abf9", "ab7d123d56", "Uruguay", "Uraguay", "232");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "68c1dcdfa1db", "ab7d123d56", "Uzbekistan", "Uzbekistan", "233");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "3e1026e9c018", "ab7d123d56", "Vanuatu", "Vanuatu", "234");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "9bab7dddbbaa", "ab7d123d56", "Vatican City State", "Vatican City State", "235");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d19a0b12627a", "ab7d123d56", "Venezuela", "Venezuela", "236");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c30696ef1e65", "ab7d123d56", "Vietnam", "Vietnam", "237");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "955bc5a37c16", "ab7d123d56", "Virgin Islands (Brit)", "Virgin Islands (Brit)", "238");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "757e3cab8d9d", "ab7d123d56", "Virgin Islands (USA)", "Virgin Islands (USA)", "239");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "66588ebc2118", "ab7d123d56", "Wake Island", "Wake Island", "240");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "0f5bb5ee8db0", "ab7d123d56", "Wallis & Futana Is", "Wallis & Futana Is", "241");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "741a490f816e", "ab7d123d56", "Yemen", "Yemen", "242");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "70c5cdadefde", "ab7d123d56", "Zaire", "Zaire", "243");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "6c81749a4ed3", "ab7d123d56", "Zambia", "Zambia", "244");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f70d414089de", "ab7d123d56", "Zimbabwe", "Zimbabwe", "245");';

// US states
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e2db27b8f8", "d805d29710", "Alabama", "AL", "0");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "fe9ce13fb2", "d805d29710", "Alaska", "AK", "1");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "3766e376b2", "d805d29710", "Arizona", "AZ", "2");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8aa06addf4", "d805d29710", "Arkansas", "AR", "3");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2af4051c2a", "d805d29710", "California", "CA", "4");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "7f87b0e926", "d805d29710", "Colorado", "CO", "5");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "30b260ef63", "d805d29710", "Connecticut", "CT", "6");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c45092e5c3", "d805d29710", "D.C.", "DC", "7");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e976869a01", "d805d29710", "Delaware", "DE", "8");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "b21c459475", "d805d29710", "Florida", "FL", "9");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "0ad6f5859d", "d805d29710", "Georgia", "GA", "10");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "ea7d694ce8", "d805d29710", "Hawaii", "HI", "11");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e1e25e9bf7", "d805d29710", "Idaho", "ID", "12");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "552a680282", "d805d29710", "Illinois", "IL", "13");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "f2742a1026", "d805d29710", "Indiana", "IN", "14");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2d32d77aff", "d805d29710", "Iowa", "IA", "15");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "206af62994", "d805d29710", "Kansas", "KS", "16");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "99816d0fce", "d805d29710", "Kentucky", "KY", "17");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "3f34ce3dac", "d805d29710", "Louisiana", "LA", "18");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "acd98329f0", "d805d29710", "Maine", "ME", "19");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8b41ccdc5e", "d805d29710", "Maryland", "MD", "20");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "6c449fdd57", "d805d29710", "Massachusetts", "MA", "21");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "db24794954", "d805d29710", "Michigan", "MI", "22");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "b51aabf8f8", "d805d29710", "Minnesota", "MN", "23");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "667b6d001a", "d805d29710", "Mississippi", "MS", "24");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "764e02748c", "d805d29710", "Missouri", "MO", "25");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "6079efef60", "d805d29710", "Montana", "MT", "26");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "541f8429d0", "d805d29710", "Nebraska", "NE", "27");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "e2980a3497", "d805d29710", "Nevada", "NV", "28");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "65ec8275f0", "d805d29710", "New Hampshire", "NH", "29");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "df6ef60bb9", "d805d29710", "New Jersey", "NJ", "30");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "884c4ec675", "d805d29710", "New Mexico", "NM", "31");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "fea041c1e5", "d805d29710", "New York", "NY", "32");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "93e7872410", "d805d29710", "North Carolina", "NC", "33");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "8c3e320aeb", "d805d29710", "North Dakota", "ND", "34");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "1ae54cfe86", "d805d29710", "Ohio", "OH", "35");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "1349b328d1", "d805d29710", "Oklahoma", "OK", "36");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "020e8c1783", "d805d29710", "Oregon", "OR", "37");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "97f166d0af", "d805d29710", "Pennsylvania", "PA", "38");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "68f92a7c1f", "d805d29710", "Rhode Island", "RI", "39");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "77878f2620", "d805d29710", "South Carolina", "SC", "40");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d84ff80a2d", "d805d29710", "South Dakota", "SD", "41");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c76f432c64", "d805d29710", "Tennessee", "TN", "42");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "2cb33f3421", "d805d29710", "Texas", "TX", "43");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "6986c5bbbd", "d805d29710", "Utah", "UT", "44");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c248c48498", "d805d29710", "Vermont", "VT", "45");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "c79f2abe7b", "d805d29710", "Virginia", "VA", "46");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "d7ef46eca9", "d805d29710", "Washington", "WA", "47");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "a2efd19a45", "d805d29710", "West Virginia", "WV", "48");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "a12dee18ec", "d805d29710", "Wisconsin", "WI", "49");';
$aSql[] = 'INSERT INTO pf_pre_values VALUES( "9094e223c4", "d805d29710", "Wyoming", "WY", "50");';

// Sample form
// Dumping data for table `pf_forms` 
$aSql[] = 'INSERT INTO `pf_forms` VALUES ("63681eef6d", "Contact Form Example", 0, 1, "", "", 0, 0, "", "Thank you.<br>Your message has been saved.", "", "Previous", "Next", "Submit");';

// Dumping data for table `pf_pages`
$aSql[] = 'INSERT INTO `pf_pages` VALUES ("801e316097", "", "", "", "", 0, "63681eef6d", 0, "", "");';

// Dumping data for table `pf_fields`
$aSql[] = 'INSERT INTO `pf_fields` VALUES ("3336ce928f", "801e316097", "Name", "", "", "", "00", 0, 1, "", "");';
$aSql[] = 'INSERT INTO `pf_fields` VALUES ("591c5cde4c", "801e316097", "Email", "", "", "", "00", 1, 1, "", "");';
$aSql[] = 'INSERT INTO `pf_fields` VALUES ("a5b4af54c2", "801e316097", "Phone", "", "", "", "00", 2, 0, "", "");';
$aSql[] = 'INSERT INTO `pf_fields` VALUES ("462fe02e5e", "801e316097", "Message", "", "", "", "06", 3, 1, "", "");';

// Dumping data for table `pf_checks_values`
$aSql[] = 'INSERT INTO `pf_checks_values` VALUES ("a00326c0eb", "0", "3336ce928f", "0,0");';
$aSql[] = 'INSERT INTO `pf_checks_values` VALUES ("8b5991f904", "3", "591c5cde4c", "0,0");';
$aSql[] = 'INSERT INTO `pf_checks_values` VALUES ("b1384f5b36", "0", "462fe02e5e", "0,0");';

/**/
?>