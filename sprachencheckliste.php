<?php
global $DB, $COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div.php';
require_once dirname(__FILE__) . '/lib/lib.php';
$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);
$do = optional_param('do', null, PARAM_ALPHANUMEXT);

require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/sprachencheckliste.php';
$PAGE->set_url($url);

$tlang=required_param('desplang', PARAM_INT);
$tskill=required_param('skillid', PARAM_INT);
$tniveau=required_param('niveauid', PARAM_INT);
$langcode=get_string("langcode","block_desp");
$language = $DB->get_record_sql(
	'SELECT user_lang.*, lang.'.$langcode.',lang.id as ulangid FROM {block_desp_check_lang} AS user_lang'.
	' JOIN {block_desp_lang} AS lang ON lang.id=user_lang.langid'.
	' WHERE user_lang.id = ? AND user_lang.userid = ?', array($tlang, $USER->id));
if (!$language) die('no language');

$skill = $DB->get_record('block_desp_skills', array("id" => $tskill));
$niveau = $DB->get_record('block_desp_niveaus', array("id" => $tniveau));

if (!$skill) die('skill not found');
if (!$niveau) die('niveau not found');

$niveau->text = $DB->get_field('block_desp_niveau_texte', 'title', array('skillid'=>$skill->id, 'niveauid'=>$niveau->id));


$sql = 
	'SELECT des.id AS desid, des.*, sub.title AS niveau,'.
	' check_item.*'.
	' FROM {block_desp_descriptors} AS des'.
	' LEFT JOIN {block_desp_niveaus} AS sub ON sub.id=des.niveauid'.
	' LEFT JOIN {block_desp_check_item} AS check_item ON check_item.descriptorid=des.id AND check_item.languageid=?'.
	' WHERE des.skillid = ? AND (des.niveauid=? OR sub.parent_niveau=?) AND des.parent_id = 0'.
	' ORDER BY des.sorting';
$descriptors = $DB->get_records_sql($sql, array($language->id, $skill->id, $niveau->id, $niveau->id));

$sorted_descriptors = array();

foreach($descriptors as $descriptor) {
	$sql = 
		'SELECT des.id AS desid, des.*, sub.title AS niveau,'.
		' check_item.*'.
		' FROM {block_desp_descriptors} AS des'.
		' LEFT JOIN {block_desp_niveaus} AS sub ON sub.id=des.niveauid'.
		' LEFT JOIN {block_desp_check_item} AS check_item ON check_item.descriptorid=des.id AND check_item.languageid=?'.
		' WHERE des.skillid = ? AND (des.niveauid=? OR sub.parent_niveau=?) AND des.parent_id = ?'.
		' ORDER BY des.sorting';
		
		$child_descriptors = $DB->get_records_sql($sql, array($language->id, $skill->id, $niveau->id, $niveau->id, $descriptor->desid));

	$sorted_descriptors[] = $descriptor;
	if(count($child_descriptors)>0)
		$sorted_descriptors = array_merge($sorted_descriptors, $child_descriptors);
}

$descriptors = $sorted_descriptors;

$possibleLernpartner = get_enrolled_users(get_context_instance(CONTEXT_COURSE, $COURSE->id));
// no sharing to myself
unset($possibleLernpartner[$USER->id]);



if ($do == 'save') {

	$notifyLernpartner = array();
	
	foreach ($_POST['descriptor'] as $id => $descriptor) {

		$dbDescriptor = new stdClass;
		$dbDescriptor->einschaetzung_selbst = @$descriptor['einschaetzung_selbst'].'';
		$dbDescriptor->lernziel = @$descriptor['lernziel'].'';
		
		$dbDescriptor->lernpartnerid = @$descriptor['lernpartnerid'].'';
		
		$notify = false;

		if ($dbTest = $DB->get_record('block_desp_check_item', array('languageid'=>$language->id, 'descriptorid'=>$id))) {
			$dbDescriptor->id = $dbTest->id;
			$DB->update_record('block_desp_check_item', $dbDescriptor);
			$dbDescriptor->descriptorid = $dbTest->descriptorid;

			$notify = ($dbDescriptor->lernpartnerid && $dbDescriptor->lernpartnerid != $dbTest->lernpartnerid);
		} else {
			$dbDescriptor->languageid = $language->id;
			$dbDescriptor->descriptorid = $id;
			$dbDescriptor->einschaetzung_fremd = 0;
			$dbDescriptor->erreicht=0;
			$DB->insert_record('block_desp_check_item', $dbDescriptor);
			
			$notify = true;
		}

		if ($notify && $dbDescriptor->lernpartnerid!=""){
			@$notifyLernpartner[$dbDescriptor->lernpartnerid][] = @$descriptors[$descriptor['arrid']]->title;
		}
	}

	//var_dump($notifyLernpartner);
	foreach ($notifyLernpartner as $userid => $changedDescriptors) {
		$user = $possibleLernpartner[$userid];
		// testen: email adresse ueberschreiben
		//$user->email = $USER->email;
		// 'dprieler@gmail.com';

		$text =
			'Lieber '.fullname($user)."\n\n".
			fullname($USER)." hat dich bei folgenden Deskriptoren als Lernpartner gewählt:\n".
			join("\n", $changedDescriptors);
			
		// echo $text."\n\n\n";

		//directly email rather than using the messaging system to ensure its not routed to a popup or jabber
		@email_to_user($user, $USER, 'Lernpartner', $text);
	}
	
	redirect($_SERVER['REQUEST_URI']);
	exit;
}








$hdrtmp=block_desp_print_header("sprachencheckliste",true,false);
$hdrers='
<link rel="stylesheet" type="text/css" media="all"
		href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/ui-darkness/jquery-ui.css"/>
	<script type="text/javascript"
		src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js">
	</script>
	<script type="text/javascript"
		src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js">
	</script>
'."
<script type=\"text/javascript\">
    $(function($) {

		$.datepicker.regional['de_AT'] = {
			monthNames: ['Jänner','Februar','März','April','Mai','Juni',
			'Juli','August','September','Oktober','November','Dezember'],
			monthNamesShort: ['Jän','Feb','Mär','Apr','Mai','Jun',
			'Jul','Aug','Sep','Okt','Nov','Dez'],
			dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
			dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
			dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
			dateFormat: 'dd.mm.yyyy', firstDay: 1,
			prevText: '&#x3c;zurück', prevStatus: 'letzten Monat zeigen',
			prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: '',
			nextText: 'Vor&#x3e;', nextStatus: 'nächsten Monat zeigen',
			nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: '',
			currentText: 'heute', currentStatus: '',
			todayText: 'heute', todayStatus: '',
			clearText: 'löschen', clearStatus: 'aktuelles Datum löschen',
			closeText: 'schließen', closeStatus: 'ohne Änderungen schließen',
			yearStatus: 'anderes Jahr anzeigen', monthStatus: 'anderen Monat anzeigen',
			weekText: 'Wo', weekStatus: 'Woche des Monats',
			dayStatus: 'Wähle D, M d', defaultStatus: 'Wähle ein Datum',
			isRTL: false
		};
		$.datepicker.setDefaults($.datepicker.regional['de_AT']);

		$( '.datepicker' ).datepicker({ dateFormat: 'dd.mm.yy' });
	});
	</script>
";
$hdrtmp=str_replace("</head>",$hdrers."</head>",$hdrtmp);
echo $hdrtmp;
echo '<script type="text/javascript" src="lib/wz_tooltip.js"></script>';
?>



<div id="desp">
<div id="page_margins">
    <div id="content">

        <h1 class="h1back h1lesen" style="background: #fdd68e url('images/ov_skill<?php echo $skill->id ?>.gif') no-repeat left top; height: 33px; padding-left: 50px;padding-top:15px;"><?php echo $language->$langcode.' '.$skill->title.' '.$niveau->title; ?></h1>

        <br />

        <p>
           <?php echo get_string('daskannichsicher','block_desp'); ?>
        </p>

        <br /><br />

            <table class="tableform2">
                <tr>
                    <th colspan="6" style="text-align:left;"><?php echo get_string(strtolower(str_replace(' ', '', str_replace('ä', 'ae', str_replace('ö', 'oe', $niveau->title.$skill->title)))), 'block_desp'); ?>
                    </th>
                </tr>
			</table>
			<p>&nbsp;</p>
			<?php
			
		echo '<form method="post" action="'.$CFG->wwwroot.'/blocks/desp/sprachencheckliste.php?courseid='.$courseid.'&amp;desplang='.$tlang.'&amp;skillid='.$tskill.'&amp;niveauid='.$tniveau.'">';
		echo '<div><input type="hidden" name="do" value="save" /></div>';
		
		$i=0;
		$levelCnt1 = 0;
		$lastNiveau = 'keines';
		foreach ($descriptors as $descriptor) {

			if ($lastNiveau != $descriptor->niveau) {
				if ($levelCnt1) echo '</table><p><input type="submit" value="'.get_string('save', 'block_desp').'" />&nbsp;</p>';
				$levelCnt1++;
				$lastNiveau = $descriptor->niveau;
				
				?>
				<table class="tableform2">
					<tr>
						<th><h2><?php echo $descriptor->niveau; ?></h2></th>
						<th class="listselect"><?php echo get_string('daskannich','block_desp'); ?></th>
						<th class="listselect"><?php echo get_string('einschaetzungvonanderen','block_desp'); ?></th>
						<th class="listselect"><?php echo get_string('meinelernziele','block_desp'); ?></th>
						
						<th class="listselect"><?php echo get_string('lernpartnerin','block_desp'); ?></th>
					</tr>
				<?php
			            

			}
			?>
                <tr>
                    <td>
                    	<?php 
                    		if($descriptor->parent_id != 0)
								echo '&nbsp;&nbsp;&nbsp;&nbsp;';
								
							$descriptor->title = str_ireplace('native speakers','<i>native speakers</i>',$descriptor->title);
							$descriptor->title = str_ireplace('multiple choice','<i>multiple choice</i>',$descriptor->title);
							echo $descriptor->title; 
							echo "<div class='sprachenchecklistdesp'>";
                    		echo block_desp_get_examplelink($descriptor->desid,$language->ulangid);
							echo "</div>";
                    	?>
                    	</td>
                    <td class="listselect">    
                    	<input type="hidden" name="descriptor[<?php echo $descriptor->desid; ?>][arrid]" value="<?php echo $i ?>" />
                    	<select size="1" name="descriptor[<?php echo $descriptor->desid; ?>][einschaetzung_selbst]">
                            <option></option>
                            <option value="2" <?php if(@$descriptor->einschaetzung_selbst==2) echo 'selected="selected"'; ?>>&#10003;&#10003;</option>
                            <option value="1" <?php if(@$descriptor->einschaetzung_selbst==1) echo 'selected="selected"'; ?>>&#10003;</option>
                        </select>
                    </td>
                    <td class="listselect"><?php
							if(@$descriptor->einschaetzung_fremd==2) echo '&#10003;&#10003;';
							elseif(@$descriptor->einschaetzung_fremd==1) echo '&#10003;'; ?>
                    </td>
                    <td class="listselect">
                        <input type="checkbox" name="descriptor[<?php echo $descriptor->desid; ?>][lernziel]" value="1" <?php if (@$descriptor->lernziel) echo 'checked="checked"'; ?> />
                    </td>
                    
                    <td class="listselect">    <select size="1" class="selectlernpartner" name="descriptor[<?php echo $descriptor->desid; ?>][lernpartnerid]">
                            <option value=""><?php echo get_string('keinlernpartner', 'block_desp');?></option>
							<option value="">----------------</option>
						<?php foreach ($possibleLernpartner as $user) {
							
								echo '<option value="'.$user->id.'"';
								if(@$descriptor->lernpartnerid == $user->id) echo ' selected="selected"';
								echo '>'.kuerzename($user->lastname,12).' '.kuerzename($user->firstname,1).'</option>';
							} ?>
                        </select>
                    </td>
                </tr>
		
		<?php
		$i++;
		 } 
		 ?>
			</table>
			
			<div><input type="submit" value="<?php echo get_string('save','block_desp'); ?>" /></div>
		</form>
            <p>&nbsp;</p>
			
            <p><b><?php echo block_desp_get_bottom_text_header($tskill,$tniveau);?></b><br />
                <?php echo block_desp_get_bottom_text($tskill,$tniveau); ?></p>




    </div>
</div>
</div>
<?php
	include_once ("despfooter.php");
?>
<?php

echo $OUTPUT->footer($course);
?>
