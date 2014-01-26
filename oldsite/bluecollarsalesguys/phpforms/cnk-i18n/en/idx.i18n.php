<?php

/* {{{ Common }}} */
$LANG["L_common_command"]        = "Command";
$LANG["L_common_save"]           = "Save"; // !not global 'save'
$LANG["L_common_return"]         = "Return <";
$LANG["L_common_general"]        = "General";
$LANG["L_common_from"]           = "from"; // FROM A to B
$LANG["L_common_to"]             = "to";   // from A TO B
$LANG["L_alt_move_up"]           = "Move up";
$LANG["L_alt_move_down"]         = "Move down";
$LANG["L_common_default_color"]  = "";


/* {{{ Phrases }}} */
$LANG["L_phr_are_u_sure"]        = "Are you sure?";
$LANG["L_phr_change_type_conf"]  = 'If you change field type some settings will be lost.\nDo you want to continue?';  // Type field changing will cause settings to be lost.
$LANG["L_phr_no_items"]          = "No items";
$LANG["L_phr_list_deleted"]      = "Predefined list <b><#name#></b> has been deleted.";
$LANG["L_phr_field_deleted"]     = "Field <b><#name#></b> has been deleted.";
$LANG["L_phr_page_deleted"]      = "Page <b><#name#></b> has been deleted.";
$LANG["L_phr_mtpl_deleted"]      = "Mail template <b><#name#></b> has been deleted.";
$LANG["L_phr_page_cant_del"]     = "Single page cannot be deleted.";

$LANG["L_phr_items_deleted"]     = "<#num#> item(s) deleted.";
$LANG["L_phr_frm_mtpl_email"]    = 'Email or mail template is incorrect.\nAre you sure you want to continue?';
$LANG["L_phr_file_not_found"]    = "Error: file not found or file read error.";
$LANG["L_phr_form_not_found"]    = "Error: no such form.";

$LANG["L_phr_must_be_number"]    = "must be a number!";
$LANG["L_phr_must_be_filled"]    = "must be not empty!";


//----------------------------------------------------------------------------
//----------------------------------------------------------------------------


/* {{{ Top menu }}} */
$LANG["L_menu_manage_forms"] = "Forms";
$LANG["L_menu_manage_pre"]   = "Predefined lists";
$LANG["L_menu_manage_mtpl"]  = "Mail templates";
$LANG["L_menu_view_submits"] = "Submissions";
$LANG["L_menu_help"]         = "Help";
$LANG["L_menu_logout"]       = "Logout";


/* {{{ All forms page }}} */
$LANG["L_page_frm_list"]           = "List of forms";
$LANG["L_head_frm_all_forms"]      = "List of forms";
$LANG["L_col_frm_form_name"]       = "Form name";
$LANG["L_col_frm_pages_count"]     = "Total pages";
$LANG["L_col_frm_fields_count"]    = "Total fields";
$LANG["L_col_frm_form_action"]     = "Send to";
$LANG["L_btn_frm_create_new_form"] = "Create form";
$LANG["L_alt_frm_edit_fields"]     = "Edit form fields";
$LANG["L_alt_frm_edit_options"]    = "Edit form options";
$LANG["L_alt_frm_delete"]          = "Delete form";
$LANG["L_alt_frm_preview"]         = "Form preview";
$LANG["L_alt_frm_get_code"]        = "Get code to insert on your site";
$LANG["L_alt_frm_to_email"]        = "Send to email";
$LANG["L_alt_frm_to_database"]     = "Save to database";


/* {{{ Form options page }}} */
$LANG["L_page_frmopts"]               = "Form options";
$LANG["L_head_frmopts"]               = "Form options";
$LANG["L_col_frmopts_default_prev"]   = "Previous";
$LANG["L_col_frmopts_default_next"]   = "Next";
$LANG["L_col_frmopts_default_submit"] = "Submit";
$LANG["L_btn_frmopts_save"]           = "Save";
$LANG["L_frmopts_form_name"]          = "Form name";
$LANG["L_frmopts_form_color"]         = "Form color";

$LANG["L_frmopts_form_dest"]          = "Submit to";
$LANG["L_frmopts_form_dest_email"]     = "To e-mail";
$LANG["L_frmopts_form_dest_db"]        = "To database";
$LANG["L_frmopts_form_dest_email_db"]  = "To e-mail & database";

$LANG["L_frmopts_form_email"]         = "Send to email";
$LANG["L_frmopts_form_email_tpl"]     = "Email template";
$LANG["L_frmopts_form_width"]         = "Form width, in pixels (0-default)";
$LANG["L_frmopts_form_after_sub_txt"] = "Message displayed<br>  after submission";
$LANG["L_frmopts_form_redirect"]      = "Redirect to (http://... or blank)";
$LANG["L_frmopts_form_buttons"]       = "Button captions";


/* {{{ Get code }}} */
$LANG["L_page_gcode"]      = "Get code";
$LANG["L_btn_gcode_close"] = "Close";
$LANG["L_gcode_3_ways"]    = "There are three ways to add form on your site:";

$LANG["L_gcode_header"]    = "Get code to insert on your site";
$LANG["L_gcode_ins_link"]  = "You can insert a link into your form <b><#link#></b>";
$LANG["L_gcode_ins_html"]  = "You can insert HTML code on your page:";
$LANG["L_gcode_ins_php"]   = "Or you can insert <b>&lt;?php include('<#path#>phpforms.php');?&gt;</b> into the page where you want to show the form before <b>&lt;html&gt;</b> tag. ".
                             "And insert <b>&lt;?php form('<#fid#>');?&gt;</b> on page where you want to show form. ";


/* {{{ Form fields (and pages) }}}*/
$LANG["L_page_flds_form_fields"]      = "Form fields";
                                        // Form1 fields (9 on 5 pages)
$LANG["L_head_flds_on_x_pages"]       = "<#form#> fields, <#fn#> on <#pn#> page(s)";
$LANG["L_col_flds_field_name"]        = "Field name";
$LANG["L_col_flds_field_type"]        = "Field type";
$LANG["L_btn_flds_new_field"]         = "Add field";
$LANG["L_btn_flds_new_page"]          = "Add page";
$LANG["L_alt_flds_show_hide"]         = "Show/Hide";
$LANG["L_alt_flds_add_field"]         = "Add field";
$LANG["L_alt_flds_edit_opts"]         = "Edit page options";
$LANG["L_alt_flds_del_page"]          = "Delete page";
$LANG["L_alt_flds_edit_field"]        = "Edit field options";
$LANG["L_alt_flds_del_field"]         = "Delete field";


/* {{{  Page options panel }}} */
$LANG["L_head_pgopts"]         = "Page options";
$LANG["L_pgopts_page_title"]   = "Page title";
$LANG["L_pgopts_top_text"]     = "Top text";
$LANG["L_pgopts_bottom_text"]  = "Bottom text";
$LANG["L_pgopts_page_color"]   = "Page color";
$LANG["L_pgopts_page_width"]   = "Page width (pixels)";
$LANG["L_pgopts_prev_button"]  = '"Previous" button';
$LANG["L_pgopts_next_button"]  = '"Next" button';


/* {{{ Field options panel }}} */
$LANG["L_header_fldopts_fld_opts"] = "Field options";
// General
$LANG["L_btn_fldopts_advanced"]    = "Advanced...";
$LANG["L_fldopts_field_name"]      = "Field name";
$LANG["L_fldopts_required"]        = "Required mark";
$LANG["L_fldopts_page"]            = "Page";
$LANG["L_fldopts_field_type"]      = "Field type";
// Default
$LANG["L_fldopts_default"]         = "Default";
$LANG["L_fldopts_default_value"]   = "Default value";
// Text, textarea field check
$LANG["L_fldopts_field_check"]      = "Field check";
$LANG["L_fldopts_field_check_rule"] = "Check rule";
$LANG["L_fldopts_check_interval"]   = "Interval";

// Field items section
$LANG["L_col_fldopts_title"]        = "Title";
$LANG["L_col_fldopts_value"]        = "Value";
$LANG["L_col_fldopts_checked"]      = "Checked";
$LANG["L_btn_fldopts_edit_items"]   = "Edit items";
$LANG["L_alt_fldopts_show_hide"]    = "Show/hide items section";
$LANG["L_fldopts_items"]            = "Items";
// Field mail options
$LANG["L_fldopts_field_mtpl_opts"]  = "Field mail options";
$LANG["L_fldopts_field_mtpl"]       = "Email template <sup>(1)</sup>";
// Date
$LANG["L_fldopts_date_col_name"]   = "Name";
$LANG["L_fldopts_date_col_show"]   = "Show";
$LANG["L_fldopts_date_col_order"]  = "Order";
$LANG["L_fldopts_date_items"]      = "Items";
$LANG["L_fldopts_date_day"]        = "Day";
$LANG["L_fldopts_date_month"]      = "Month";
$LANG["L_fldopts_date_year"]       = "Year";
$LANG["L_fldopts_date_period"]     = "Year interval";


/* {{{ Field items list section }}} */
// Brief field info
$LANG["L_fldit_field_info"]         = "Field info";
$LANG["L_fldit_field_name"]         = "Field name";
$LANG["L_fldit_page_title"]         = "Page title";
//
$LANG["L_head_fldit_header"]        = "Field items list";
$LANG["L_btn_fldit_add_item"]       = "Add item";
$LANG["L_btn_fldit_add_predefined"] = "Add predefined";
$LANG["L_alt_fldit_show_hide"]      = "Show/hide items section";
$LANG["L_alt_fldit_del_sel"]        = "Delete selected items";
$LANG["L_alt_fldit_del_item"]       = "Delete item";
$LANG["L_fldit_new_items"]          = "New items";
$LANG["L_fldit_title"]              = "Title";
$LANG["L_fldit_value"]              = "Value";
$LANG["L_fldit_predefined"]         = "Predefined";
$LANG["L_fldit_items"]              = "Items";
$LANG["L_fldit_checked"]            = "Checked";


/* {{{ Field advanced options }}} */
$LANG["L_page_adv_advanced"]       = "Field advanced options";
// Layout
$LANG["L_alt_adv_layout_prev"]     = "Previous";
$LANG["L_alt_adv_layout_cur"]      = "Current layout";
$LANG["L_alt_adv_layout_next"]     = "Next";
$LANG["L_adv_layout"]              = "Layout";
// HTML
$LANG["L_adv_html"]                = "HTML";
$LANG["L_adv_html_fld_color"]      = "Field color";
$LANG["L_adv_html_caption_style"]  = "Caption style";
$LANG["L_adv_html_control_style"]  = "Control style";
 // HTML options of control types
 $LANG["L_adv_html_opt_rows"]      = "Rows";
 $LANG["L_adv_html_opt_maxlength"] = "Max length";
 $LANG["L_adv_html_opt_size"]      = "Size";
 $LANG["L_adv_html_opt_filesize"]  = "Max File Size";


/* {{{ Predefined lists }}} */
$LANG["L_page_pre_lists"]       = "Predefined values";
$LANG["L_head_pre_lists"]       = "Predefined lists";
$LANG["L_header_pre_lists"]     = "Edit list";
$LANG["L_col_pre_name"]         = "List name";
$LANG["L_col_pre_title"]        = "Title";
$LANG["L_col_pre_value"]        = "Value";
$LANG["L_btn_pre_new_list"]     = "Add list";
$LANG["L_btn_pre_add_N_items"]  = "Add items";
$LANG["L_alt_pre_edit_list"]    = "Edit list";
$LANG["L_alt_pre_del_list"]     = "Delete list";
$LANG["L_alt_pre_del_sel"]      = "Delete selected items";
$LANG["L_alt_pre_del_item"]     = "Delete item";

$LANG["L_pre_list_name"]        = "List name";
$LANG["L_pre_add_items"]        = "Add items";
$LANG["L_pre_items"]            = "Items";


/* {{{ Mail templates }}} */
// Template list page
$LANG["L_page_mtpl_list"]       = "Mail templates";
$LANG["L_head_mtpl_lists"]      = "Mail templates";
$LANG["L_col_mtpl_name"]        = "Mail template name";
$LANG["L_btn_mtpl_add_new"]     = "Add template";
$LANG["L_alt_mtpl_edit"]        = "Edit mail template";
$LANG["L_alt_mtpl_del"]         = "Delete mail template";
// Mail template editor
$LANG["L_page_mtpl_edit"]       = "Edit mail template";
$LANG["L_btn_mtpl_save"]        = "Save template";
$LANG["L_btn_mtpl_form_name"]   = "Name";
$LANG["L_btn_mtpl_form_data"]   = "Data";
$LANG["L_btn_mtpl_field_name"]  = "Name";
$LANG["L_btn_mtpl_field_data"]  = "Data";

$LANG["L_mtpl_name"]            = "Template name";
$LANG["L_mtpl_from_addr"]       = "From - address";
$LANG["L_mtpl_email_subj"]      = "E-mail Subject";
$LANG["L_mtpl_forms"]           = "Forms";
$LANG["L_mtpl_page_field"]      = "Page.Field - Field name";


/* {{{ User submits }}} */
$LANG["L_page_sub"]                  = "Form submissions";
$LANG["L_head_sub_list"]             = "Form submissions";
$LANG["L_head_sub_data"]             = "Submitted data";
$LANG["L_col_sub_form_name"]         = "Form name";
$LANG["L_col_sub_date_time"]         = "Date/Time";
$LANG["L_btn_sub_delete_all"]        = "Delete all";
$LANG["L_alt_sub_view_submit"]       = "View submit";
$LANG["L_alt_sub_del_submit"]        = "Delete submit";


/* {{{ New Items }}} */
$LANG["L_vars_form_name"]      = "NewForm";
$LANG["L_vars_list_name"]      = "NewList";
$LANG["L_vars_mtpl_name"]      = "NewMailTemplate";
$LANG["L_vars_field_name"]     = "NewField";

/* {{{ Form preview }}} */
$LANG["L_frmview_demo"]        = "Form preview";
$LANG["L_frmview_demo_note"]   = "Note: in the preview mode form results are not submitted neither to database nor to email.";


/* {{{ Fill form }}} */
$LANG["L_page_frmfill"]        = "Fill form";

//----------------------------------------------------------------------------
//----------------------------------------------------------------------------


/* {{{ Java script check alerts }}} */
 // Checkbox
 $LANG["L_js_chk_cbx_equal_opts"]     = "'You must check exactly '+num+' item(s) in field \"'+field_title+'\"'";
 $LANG["L_js_chk_cbx_interval_opts"]  = "'You must check from '+from+' to '+to+' item(s) in field \"'+field_title+'\"'";
 $LANG["L_js_chk_cbx_less_than_opts"] = "'You must check less than '+num+' item(s) in field \"'+field_title+'\"'";
 $LANG["L_js_chk_cbx_more_than_opts"] = "'You must check more than '+num+' item(s) in field \"'+field_title+'\"'";

 // Multiple select
 $LANG["L_js_chk_mul_equal_opts"]     = "'You must select exactly '+num+' item(s) in field \"'+field_title+'\"'";
 $LANG["L_js_chk_mul_interval_opts"]  = "'You must select from '+from+' to '+to+' items in field \"'+field_title+'\"'";
 $LANG["L_js_chk_mul_less_than_opts"] = "'You must select less than '+num+' item(s) in field \"'+field_title+'\"'";
 $LANG["L_js_chk_mul_more_than_opts"] = "'You must select more than '+num+' item(s) in field \"'+field_title+'\"'";

 // String
 $LANG["L_js_chk_str_email"]          = "'\"'+field_title+'\" must be email'";
 $LANG["L_js_chk_str_from_to_chars"]  = "'Field \"'+field_title+'\" length should be between '+from+' and '+to+' chars'";
 $LANG["L_js_chk_str_letters_only"]   = "'\"' + field_title + '\" must contain letters only'";
 $LANG["L_js_chk_str_not_empty"]      = "'You should fill \"' + field_title + '\"'";
 $LANG["L_js_chk_str_numbers_only"]   = "'\"' + field_title + '\" must be a number'";


?>