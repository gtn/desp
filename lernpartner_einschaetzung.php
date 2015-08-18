<?php
global $DB, $COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div_partner.php';
$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);
$do = optional_param('do', null, PARAM_ALPHANUMEXT);
$bl = optional_param('bl', "", PARAM_ALPHANUMEXT);
require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/lernpartner_einschaetzung.php';
$PAGE->set_url($url);


function block_desp_get_first_lernpartner_check_item($checkitemid) {
	global $USER, $DB;
$langcode=get_string("langcode","block_desp");
	// read first item data
	$sql = 
		'SELECT u.id AS uid, check_item.id AS firstid, u.firstname, u.lastname, check_lang.id AS userlanguageid, lang.'.$langcode.' AS language, '.
		' skill.id AS skillid, skill.title AS skill, niveau.id AS niveauid, niveau.title AS niveau'.
		' FROM {block_desp_check_lang} AS check_lang'.
		' JOIN {user} AS u ON u.id=check_lang.userid'.
		' JOIN {block_desp_lang} AS lang ON check_lang.langid=lang.id'.
		' JOIN {block_desp_check_item} AS check_item ON check_item.languageid=check_lang.id AND check_item.lernpartnerid=?'.
		' JOIN {block_desp_descriptors} AS des ON check_item.descriptorid=des.id'.
		' JOIN {block_desp_skills} AS skill ON skill.id=des.skillid'.
		' JOIN {block_desp_niveaus} AS niveau_sub ON niveau_sub.id=des.niveauid'.
		' JOIN {block_desp_niveaus} AS niveau ON (niveau.id=niveau_sub.parent_niveau OR (niveau_sub.parent_niveau=0 AND niveau.id=niveau_sub.id))'.
		' WHERE check_item.id=?';
	$checkitem = $DB->get_record_sql($sql, array($USER->id, $checkitemid));
	if (!$checkitem) die('no checkitem');
	return $checkitem;
}


function block_desp_get_all_lernpartner_check_item($checkitem) {
	global $USER, $DB;
	
	// read all items
	$sql = 
	// 
	// check_item.id, des.id AS desid, des.title, sub.title AS niveau, 
		'SELECT check_item.*, des.title, niveau_sub.title AS niveau'.
		' FROM {block_desp_check_item} AS check_item'.
		' JOIN {block_desp_descriptors} AS des ON check_item.descriptorid=des.id'.
		' JOIN {block_desp_niveaus} AS niveau_sub ON niveau_sub.id=des.niveauid'.
		' JOIN {block_desp_niveaus} AS niveau ON (niveau.id=niveau_sub.parent_niveau OR (niveau_sub.parent_niveau=0 AND niveau.id=niveau_sub.id))'.
		' WHERE check_item.lernpartnerid=? AND check_item.languageid=? AND des.skillid=? AND niveau.id=?'.
		' ORDER BY niveau.sorting';
	$items = $DB->get_records_sql($sql, array($USER->id, $checkitem->userlanguageid, $checkitem->skillid, $checkitem->niveauid));
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
			$dbDescriptor->einschaetzung_fremd = @$check_item['einschaetzung_fremd'].'';
			$dbDescriptor->id = $id;
			$DB->update_record('block_desp_check_item', $dbDescriptor);
		}
		if ($bl=="center")redirect($CFG->wwwroot.'/blocks/desp/center.php?courseid='.$courseid);
		else redirect($_SERVER['REQUEST_URI']);
		exit;
	}
	
	
	block_desp_print_header("lernpartner_einschaetzung");
  echo '<div id="desp">';
	echo "<h2>".get_string('bewertungfuer', 'block_desp').' '.$checkitem->firstname.' '.$checkitem->lastname.' / '.$checkitem->language.' / '.$checkitem->skill.' '.$checkitem->niveau.'</h2>';

	//action="'.$CFG->wwwroot.'/blocks/desp/lernpartner_einschaetzung.php?courseid='.$courseid.'&bl='.$bl.'&checkitemid='.optional_param('checkitemid', null, PARAM_INT).'"
		echo '<form method="post">';
		echo '<div><input type="hidden" name="do" value="save" /></div>';
		
		
		$levelCnt1 = 0;
		$lastNiveau = 'keines';
		foreach ($check_items as $check_item) {

			if ($lastNiveau != $check_item->niveau) {
				if ($levelCnt1) echo '</table><br /><br />';
				$levelCnt1++;
				$lastNiveau = $check_item->niveau;
				
				?>
				<table class="tableform2">
					<tr>
						<th><h2><?php echo $check_item->niveau; ?></h2></th>
						<th class="listselect"><?php echo get_string('einschaetzungvonanderen','block_desp'); ?></th>
					</tr>
				<?php
			            

			}
			?>
                <tr>
                    <td><?php echo $check_item->title; ?></td>
                    <td class="listselect">    <select size="1" name="check_item[<?php echo $check_item->id; ?>][einschaetzung_fremd]">
                            <option></option>
                            <option value="2" <?php if(@$check_item->einschaetzung_fremd==2) echo 'selected="selected"'; ?>>&#10003;&#10003;</option>
                            <option value="1" <?php if(@$check_item->einschaetzung_fremd==1) echo 'selected="selected"'; ?>>&#10003;</option>
                        </select>
                    </td>
                </tr>
		
		<?php } ?>
			</table>
			<div><input type="submit" value="<?php echo get_string('save','block_desp'); ?>" /></div>
		</form>
	<?php
echo '<div><br /><br /><br /></div>';
 include_once ("despfooter.php");
echo '</div>';
	echo $OUTPUT->footer($course);
	exit;
}


$items =get_lernpartner_check($USER->id);



block_desp_print_header("lernpartner_einschaetzung");
echo '<div id="desp">';
echo '<h2>'.get_string('lernpartnereinschaetzen', 'block_desp').'</h2>';
if (!$items) {
	echo '<div id="messageboxslp3" style="background: url(images/message_lp.gif) no-repeat left top;">
            <div id="messagetxtslp3"><span>'.get_string('keinelernpartner', 'block_desp').'</span>
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
		echo get_string('bereichbewertung', 'block_desp').$item->skill.' '.$item->niveau.'<br />';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?courseid='.$COURSE->id.'&amp;checkitemid='.$item->id.'">'.get_string('einschaetzen', 'block_desp').'</a><br />';
		echo '<br />';
	}
}
echo '<div><br /><br /><br /></div>';
 include_once ("despfooter.php");
echo '</div>';
echo $OUTPUT->footer($course);

