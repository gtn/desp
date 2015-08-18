<?php
global $DB, $COURSE;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div_partner.php';
require_once dirname(__FILE__) . '/lib/lib.php';
$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);
$do = optional_param('do', null, PARAM_ALPHANUMEXT);

require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/lernpartner_einschaetzung.php';
$PAGE->set_url($url);


function block_desp_get_first_lernpartner_check_item($checkitemid) {
	global $USER, $DB;
$langcode=get_string("langcode","block_desp");
	// read first item data
	$sql = 
		'SELECT lernplans.id, lernplans.title, u.id AS uid, u.firstname, u.lastname, skill.id AS skillid, lang.'.$langcode.' AS language, check_lang.id AS userlanguageid, skill.title AS skill'.
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
	$langcode=get_string("langcode","block_desp");
	// read all items
	$sql = 
		'SELECT lernplans.id, lernplans.title, lernplans.lernpartner_kommentar, lernplans.lernpartner_einschaetzung, u.id AS uid, u.firstname, u.lastname, lang.'.$langcode.' AS language, skill.title AS skill'.
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



if ($checkitemid = optional_param('checkitemid', null, PARAM_INT)) 
{

	$checkitem = block_desp_get_first_lernpartner_check_item($checkitemid);
	$check_items = block_desp_get_all_lernpartner_check_item($checkitem);
	
	
	if ($do == 'save') {

		foreach ($_POST['check_item'] as $id => $check_item) {

			// don't allow to change other "fremde" check_items
			if (!@$check_items[$id]) continue;
			
			$dbDescriptor = new stdClass;
			$dbDescriptor->lernpartner_kommentar = @$check_item['lernpartner_kommentar'].'';
			$dbDescriptor->id = $id;
			
			if (@$check_item['lernpartner_einschaetzung']=="on") $lptemp=1;
			else $lptemp=0;
			
			$dbDescriptor->lernpartner_einschaetzung = $lptemp;
			$dbDescriptor->kommentar_gelesen = 0;
			$DB->update_record('block_desp_learnplans', $dbDescriptor);
		}
		//http://gtn02.gtn-solutions.com/moodle20/blocks/desp/sprachlernplan_lernpartner_einschaetzung.php?courseid=1
		if ($bl="center") redirect($CFG->wwwroot.'/blocks/desp/center.php?courseid='.$courseid);
		else redirect($CFG->wwwroot.'/blocks/desp/sprachlernplan_lernpartner_einschaetzung.php?courseid='.$courseid);
		exit;
	}
	
	
	block_desp_print_header("lernpartner_einschaetzung");

	echo "<h2>".get_string('bewertungfuer', 'block_desp').' '.$checkitem->firstname.' '.$checkitem->lastname.' / '.$checkitem->language.' / '.block_desp_skilltitle($checkitem->skill).'</h2>';

	?>
		<form method="post">
			<div><input type="hidden" name="do" value="save" /></div>
		<?php
		
		$levelCnt1 = 0;
		$lastNiveau = 'keines';
		foreach ($check_items as $check_item) {
			
			$check_item->niveau = 'tmp';
			if (@$check_item->lernpartner_einschaetzung==1) $tchecked=' checked="checked"';
			else $tchecked='';
			if ($lastNiveau != $check_item->niveau) {
				if ($levelCnt1) echo '</table><br /><br />';
				$levelCnt1++;
				$lastNiveau = $check_item->niveau;
				
				?>
				<table class="tableform2">
					<tr>
						<!-- th><h2><?php echo $check_item->niveau; ?></h2></th -->
						<th class="listselect" colspan="3"><?php echo get_string('einschaetzungvonanderen', 'block_desp');?></th>
					</tr>
				<?php
			            

			}
			?>
                <tr>
                    <td><?php echo $check_item->title; ?></td>
                    <td class="desp_lp_check">
                    	<input type="checkbox" value="on" name="check_item[<?php echo $check_item->id; ?>][lernpartner_einschaetzung]" <?php echo $tchecked; ?> />
                    	</td>
                    <td class="listselect">  
					<textarea name="check_item[<?php echo $check_item->id; ?>][lernpartner_kommentar]" cols="50" rows="3"><?php echo @$check_item->lernpartner_kommentar; ?></textarea>
                    </td>
                </tr>
		
		<?php } ?>
			</table>
			<div><input type="submit" value="<?php echo get_string('save', 'block_desp');?>" /></div>
		</form>
	<?php

	echo $OUTPUT->footer($course);
	
	exit;
} //ende if checkitem/detailansicht






$items=get_lernpartner($USER->id);
block_desp_print_header("lernpartner_einschaetzung");

echo '<div id="desp"><h2>'.get_string('lerpartnerunterstuetzen', 'block_desp').'</h2>';
if (!$items) {
	echo '<div id="messageboxslp3" style="background: url(images/message_lp.gif) no-repeat left top;">
            <div id="messagetxtslp3">
               '.get_string('keinelernpartner', 'block_desp').'</span></a>
            </div>
        </div>';
} else {
	$lastUid = null;
	foreach ($items as $item) {
		if ($item->uid != $lastUid) {
			echo '<br /><b>'.$item->firstname.' '.$item->lastname.'</b><br />';
			$lastUid = $item->uid;
		}
		echo get_string('sprachebewertung', 'block_desp').$item->language.'<br />';
		echo get_string('bereichbewertung', 'block_desp').block_desp_skilltitle($item->skill).'<br />';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?courseid='.$COURSE->id.'&amp;checkitemid='.$item->id.'">'.get_string('einschaetzen', 'block_desp').'</a><br />';
		echo '<br />';
	}
}
echo '<br /><br /><br /></div>';
include_once ("despfooter.php");
echo $OUTPUT->footer($course);

