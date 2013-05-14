<?php

/* * *************************************************************
 *  Copyright notice
*
*  (c) 2011 exabis internet solutions <info@exabis.at>
*  All rights reserved
*
*  You can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  This module is based on the Collaborative Moodle Modules from
*  NCSA Education Division (http://www.ncsa.uiuc.edu)
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
* ************************************************************* */

ini_set('max_execution_time', 3000);
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div.php';
require_once dirname(__FILE__) . '/lib/xmllib.php';

global $COURSE, $CFG, $OUTPUT;
$content = "";

require_login($COURSE->id);
$context = get_context_instance(CONTEXT_SYSTEM);
$action = optional_param('action', "", PARAM_ALPHA);

//require_capability('block/desp:admin', $context);

//$url = '/blocks/desp/import.php';
$url = '/blocks/desp/import.php';
$PAGE->set_url($url);
$url = $CFG->wwwroot.$url;

block_desp_print_header("admintabimport");
echo '<div id="desp">';
echo "<div class='block_excomp_center'>";

if($action == "xml") {

	block_desp_import_languages("xml/desp_languages.xml");

	if (block_desp_check_table("block_desp_cultures_items")){
		block_desp_xml_truncate("block_desp_cultures_items");
	}
	block_desp_importtable("xml/desp_lernculture_items.xml","block_desp_cultures_items");

	if (block_desp_check_table("block_desp_cultures_item_cat")){
		block_desp_xml_truncate("block_desp_cultures_item_cat");
	}
	block_desp_importtable("xml/desp_lernculture_items_cats.xml","block_desp_cultures_item_cat");

	if (block_desp_check_table("block_desp_learnplan_items")){
		block_desp_xml_truncate("block_desp_learnplan_items");
	}
	block_desp_importtable("xml/desp_learnplan_items.xml","block_desp_learnplan_items");

	$import = block_desp_xml_do_import("desp");
}
$check = block_desp_xml_check_import();

if($check)
	echo $OUTPUT->box(text_to_html(get_string("importdone", "block_desp")));
else
	echo $OUTPUT->box(text_to_html(get_string("importpending", "block_desp")));

echo $OUTPUT->box(text_to_html('<a href="'.$url.'?action=xml">'.get_string("doimport", "block_desp").'</a>'));

if(isset($import)) {
	if($import)
		echo $OUTPUT->box(text_to_html(get_string("importsuccess", "block_desp")));
	else
		echo $OUTPUT->box(text_to_html(get_string("importfail", "block_desp")));

}
echo $content;
echo "</div></div>";
echo $OUTPUT->footer();
