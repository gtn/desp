<?php
global $DB, $COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div_lernplan.php';
require_once dirname(__FILE__) . '/lib/lib.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);
$do = optional_param('do', null, PARAM_ALPHANUMEXT);

require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/lernpartner_einschaetzung.php';
$PAGE->set_url($url);

/*
function block_desp_get_first_lernpartner_check_item($checkitemid) {
	global $USER, $DB;

	// read first item data
	$sql = 
		'SELECT lernplans.id, lernplans.title, u.id AS uid, u.firstname, u.lastname, skill.id AS skillid, lang.name AS language, check_lang.id AS userlanguageid, skill.title AS skill'.
		' FROM {block_desp_learnplans} AS lernplans'.
		' JOIN {block_desp_skills} AS skill ON lernplans.skillid=skill.id'.
		' JOIN {user} AS u ON u.id=lernplans.userid'.
		' JOIN {block_desp_learnplans_lang} AS check_lang ON check_lang.id=lernplans.langid AND check_lang.userid = u.id'.
		' JOIN {block_desp_lang} AS lang ON check_lang.langid=lang.id'.
		' WHERE lernplans.lernpartnerid=? AND lernplans.id=?';
	$checkitem = $DB->get_record_sql($sql, array($USER->id, $checkitemid));
	if (!$checkitem) die('no checkitem');
	return $checkitem;
}


function block_desp_get_all_lernpartner_check_item($checkitem) {
	global $USER, $DB;
	
	// read all items
	$sql = 
		'SELECT lernplans.id, lernplans.title, lernplans.lernpartner_kommentar, lernplans.lernpartner_einschaetzung, u.id AS uid, u.firstname, u.lastname, lang.name AS language, skill.title AS skill'.
		' FROM {block_desp_learnplans} AS lernplans'.
		' JOIN {block_desp_skills} AS skill ON lernplans.skillid=skill.id'.
		' JOIN {user} AS u ON u.id=lernplans.userid'.
		' JOIN {block_desp_learnplans_lang} AS check_lang ON check_lang.id=lernplans.langid AND check_lang.userid = u.id'.
		' JOIN {block_desp_lang} AS lang ON check_lang.langid=lang.id'.
		' WHERE lernplans.lernpartnerid=? AND lernplans.langid=? AND lernplans.skillid=?'.
		' ORDER BY lernplans.title';
	$items = $DB->get_records_sql($sql, array($USER->id, $checkitem->userlanguageid, $checkitem->skillid));
	return $items;
}



if ($checkitemid = optional_param('checkitemid', null, PARAM_INT)) {

	$checkitem = block_desp_get_first_lernpartner_check_item($checkitemid);
	$check_items = block_desp_get_all_lernpartner_check_item($checkitem);
	
	
	if ($do == 'save') {

		foreach ($_POST['check_item'] as $id => $check_item) {

			// don't allow to change other "fremde" check_items
			if (!@$check_items[$id]) continue;
			
			$dbDescriptor = new stdClass;
			$dbDescriptor->lernpartner_kommentar = @$check_item['lernpartner_kommentar'].'';
			$dbDescriptor->id = $id;
			$DB->update_record('block_desp_learnplans', $dbDescriptor);
		}
		
		redirect($_SERVER['REQUEST_URI']);
		exit;
	}
	
	
	block_desp_print_header("lernpartner_fremdeinschaetzung");

	echo "<h2>Bewertung für ".fullname($checkitem).' / '.$checkitem->language.' / '.$checkitem->skill.'</h2>';

	?>
		<form method="post">
			<input type="hidden" name="do" value="save" />
		<?php
		
		$levelCnt1 = 0;
		$lastNiveau = 'keines';
		foreach ($check_items as $check_item) {
			
			$check_item->niveau = 'tmp';

			if ($lastNiveau != $check_item->niveau) {
				if ($levelCnt1) echo '</table><br /><br />';
				$levelCnt1++;
				$lastNiveau = $check_item->niveau;
				
				?>
				<table class="tableform2">
					<tr>
						<!-- th><h2><?php echo $check_item->niveau; ?></h2></th -->
						<th class="listselect" colspan="2">Einsch&auml;tzung von Anderen</th>
					</tr>
				<?php
			            

			}
			?>
                <tr>
                    <td><?php echo $check_item->title; ?></td>
                    <td class="listselect">  
					<textarea name="check_item[<?php echo $check_item->id; ?>][lernpartner_kommentar]"><?php echo @$check_item->lernpartner_kommentar; ?></textarea>
                    </td>
                </tr>
		
		<?php } ?>
			</table>
			<input type="submit" value="speichern" />
		</form>
	<?php

	echo $OUTPUT->footer($course);
	
	exit;
}

*/


/*$sql = 
	'SELECT lernplans.id, lernplans.title, lernplans.lernpartner_kommentar, u.id AS uid, u.firstname, u.lastname, lang.name AS language, skill.title AS skill'.
	' FROM {block_desp_learnplans} AS lernplans'.
	' JOIN {block_desp_skills} AS skill ON lernplans.skillid=skill.id'.
	' JOIN {user} AS u ON u.id=lernplans.lernpartnerid'.
	' JOIN {block_desp_learnplans_lang} AS check_lang ON check_lang.id=lernplans.langid AND check_lang.userid = u.id'.
	' JOIN {block_desp_lang} AS lang ON check_lang.langid=lang.id'.
	' WHERE lernplans.userid=?'.
	' GROUP BY u.id, lang.id, skill.id'.
	' ORDER BY u.lastname, u.firstname, lang.name, skill.sorting, lernplans.title';
$items = $DB->get_records_sql($sql, array($USER->id));*/
$sql="SELECT * FROM {block_desp_learnplans} WHERE userid=? AND (lernpartner_kommentar<>'' OR lernpartner_einschaetzung=1)";
$items = $DB->get_records_sql($sql,array("userid"=>$USER->id));

block_desp_print_header("lernpartner_fremdeinschaetzung");
echo '<div id="desp">';
echo '<h2>'.get_string('einschaetzunglernpartners', 'block_desp').'</h2>';
if (empty($items)) {
	echo '<div id="messageboxslp3" style="background: url(images/message_lp.gif) no-repeat left top;">
            <div id="messagetxtslp3"><span>
               '.get_string('nochkeineeinschaetzung', 'block_desp').'</span>
            </div>
        </div>';
} else {
	
	$lastUid = null;
	foreach ($items as $item) {
	
		if(!$item->lernpartner_kommentar)
			continue;
			
		//$language = $DB->get_record('block_desp_lang',array("id"=>$item->langid));
		$language=block_desp_get_lang_title($item->langid);
		$skill = $DB->get_record('block_desp_skills',array("id"=>$item->skillid));
		if ($item->lernpartnerid != $lastUid && $item->lernpartnerid != 0) {
			$user = $DB->get_record('user',array("id"=>$item->lernpartnerid));
			echo '<br /><b>'.$user->lastname . " " . $user->firstname.'</b><br />';
			$lastUid = $item->lernpartnerid;
		}
		echo '<b>'.get_string('sprachebewertung', 'block_desp').$language.'</b><br />';
		echo get_string('bereichbewertung', 'block_desp').block_desp_skilltitle($skill->title).'<br />';
		echo get_string('titelbewertung', 'block_desp').$item->title.'<br />';
		echo get_string('kommentarbewertung', 'block_desp').$item->lernpartner_kommentar.'<br />';
		if ($item->lernpartner_einschaetzung==1){
			echo get_string('lernplanerfuellt', 'block_desp');
		}
		echo '<br /><br />';
		
		$item->kommentar_gelesen = 1;
		$DB->update_record('block_desp_learnplans',$item);
	}
	echo '<form method="post" action="'.$CFG->wwwroot.'/blocks/desp/sprachenbiografie.php?courseid='.$courseid.'">';
	echo '<div><input type="submit" value="'.get_string('zurkenntnis', 'block_desp').'" /></div>';
	echo '</form>';
}
echo '<br /><br /><br /></div>';
include_once ("despfooter.php");
echo $OUTPUT->footer($course);

