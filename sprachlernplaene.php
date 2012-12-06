<?php
global $DB, $COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);
$do = optional_param('do', null, PARAM_ALPHANUMEXT);

require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/sprachenbiografie.php';
$PAGE->set_url($url);
/*von sprachenchecklisten*/


if ($do == 'add-language') {
	$langid = required_param('langid', PARAM_INT);

	$newLanguage = new StdClass;
	$newLanguage->userid = $USER->id;
	$newLanguage->langid = $langid;
	$DB->insert_record('block_desp_learnplans_lang', $newLanguage);

	redirect($_SERVER['PHP_SELF'].'?courseid='.$courseid);
	exit;
}

if ($do == 'delete-language') {
	$id = required_param('id', PARAM_INT);
	$DB->delete_records('block_desp_learnplans',array('userid' => $USER->id,"langid" => $id));
	$conditions = array("id" => $id, 'userid' => $USER->id);
	$DB->delete_records('block_desp_learnplans_lang', $conditions);
	redirect($_SERVER['PHP_SELF'].'?courseid='.$courseid);
	exit;
}

if ($do == 'change-language') {
	$name = required_param('name', PARAM_TEXT);
	$id = required_param('id', PARAM_INT);

	$language = $DB->get_record('block_desp_learnplans_lang', array('id'=>$id, 'userid'=>$USER->id));
	if ($language) {
		$language->name = $name;
		$DB->update_record('block_desp_learnplans_lang', $language);
	}

	redirect($_SERVER['PHP_SELF'].'?courseid='.$courseid);
	exit;
}





$myLanguagesSql = 'SELECT check_lang.*, lang.name
	FROM {block_desp_learnplans_lang} AS check_lang
	JOIN {block_desp_lang} AS lang ON check_lang.langid=lang.id
	WHERE check_lang.userid = ?
	ORDER BY lang.name';

$myLanguages = $DB->get_records_sql($myLanguagesSql, array($USER->id));
/*
if (!$myLanguages) {
	// add first language
	$newLanguage = new StdClass;
	$newLanguage->userid = $USER->id;
	$newLanguage->langid = 1; // id = 1 => deutsch
	$DB->insert_record('block_desp_learnplans_lang', $newLanguage);

	$myLanguages = $DB->get_records_sql($myLanguagesSql, array($USER->id));
}
*/

$otherLanguages = $DB->get_records_sql('SELECT *
	FROM {block_desp_lang}
	WHERE (userid=0 OR userid=?)
		AND id NOT IN (SELECT langid
		FROM {block_desp_learnplans_lang}
		WHERE userid = ?
	)
	ORDER BY name', array($USER->id, $USER->id));
	
	/*von sprachenchecklisten ende*/
block_desp_print_header("sprachlernplaene");
?>



<script type="text/javascript">
    /*<![CDATA[*/

	function changeLanguage(id) {
		var name = prompt("<?php echo get_string('neuernamesprache', 'block_desp');?>");
		
		if (name === null) return;
		if (!name) { alert("<?php echo get_string('namenichtleer', 'block_desp');?>"); return; }

		document.location.href = '<?php echo $_SERVER['PHP_SELF'].'?courseid='.$courseid.'&do=change-language&id='; ?>' + encodeURIComponent(id)
			+ '&name=' + encodeURIComponent(name);
	}

	function deleteLanguage(id) {
		if (confirm("<?php echo get_string('sprachewirklichloeschen', 'block_desp');?>")) {
			if (confirm("<?php echo get_string('sprachewirklichloeschen2', 'block_desp');?>")) {
			document.location.href = '<?php echo $_SERVER['PHP_SELF'].'?courseid='.$courseid.'&do=delete-language&id='; ?>' + encodeURIComponent(id);
			}
		}
	}
    /*]]>*/
</script>

<div id="page_margins">
    <div id="content">

        <h1><?php echo get_string('meinesprachlernplaene','block_desp'); ?></h1>

        <h2><?php echo get_string('meinesprachlernplaene2','block_desp'); ?></h2>


        <div id="messageboxslp1" style="background: url('images/message_slp1.gif') no-repeat left top;">
            <div id="messagetxtslp1">
                <?php echo get_string('meinesprachlernplaene_inhalt','block_desp'); ?>
            </div>
        </div>



        <div id="messageboxslp2" style="background: url('images/message_slp2.gif') no-repeat left top;">
            <div id="messagetxtslp2">
				<?php echo get_string('meinesprachlernplaene_inhalt2','block_desp'); ?>
            </div>
        </div>



        <div id="messageboxslp3" style="background: url('images/message_slp3.gif') no-repeat left top;">
            <div id="messagetxtslp3">
                <?php echo get_string('meinesprachlernplaene_inhalt3','block_desp'); ?>
            </div>
        </div>



        <br /><br />
        
<?php
$lernplansalarm=block_desp_ist_lernplanueberschreitung(2);
//print_r($lernplansalarm);
if (!empty($lernplansalarm)){
	
	$inhalt= '
	<div class="messageimportantinfo" style="display:;">
		<div style="font-size:35px;font-weight: bold; float:left;font-family: Times, Verdana, Arial, sans-serif; padding-right:20px;">!</div>
		<div>Du hast dein vorgegebenes Ziel in den Sprachlern-Pl&auml;nen &uuml;berschritten.<br />Bereiche mit &uuml;berschritten Sprachlern-Pl&auml;nen werden in roter Farbe dargestellt.</div> 
	</div>
	';
	echo $inhalt;
}

?>        
<?php foreach ($myLanguages as $language): ?>
        <table class="tableform3 overviewses overview_sesyell">
            <tr>
                <th class="tableses1"><?php echo $language->name; ?>
					<!-- a href="javascript:changeLanguage(<?php echo $language->id; ?>);">Ã¤ndern</a -->
					<a href="javascript:deleteLanguage(<?php echo $language->id; ?>);"><?php echo get_string('loeschen','block_desp'); ?></a></th>
				
            </tr>
			<?php 
						foreach ($DB->get_records('block_desp_skills', null, 'sorting') as $skill) {
							if (!empty($lernplansalarm[$language->id][$skill->id])) $warnung="color:red;";
							else $warnung="";
            	echo '<tr>';
              echo '<td><a href="'.$CFG->wwwroot.'/blocks/desp/sprachlernplan.php?skid='.$skill->id.'&amp;courseid='.$courseid.'&amp;langid='.$language->id.'" class="overview ov_skill'.$skill->id.'" style="background: #ffefd3 url(\'images/ov_skill'.$skill->id.'.gif\') no-repeat left top;'.$warnung.'">'.get_string(strtolower(str_replace(' ', '', str_replace('ä', 'ae', str_replace('ö', 'oe', $skill->title)))), 'block_desp').'</a></td>';
            	echo '</tr>';
							}
			        echo '</table>';
endforeach; ?>

	<?php if ($otherLanguages): // nur anzeigen, falls es noch sprachen gibt ?>
	<form method="post" action="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlernplaene.php?courseid=<?php echo $courseid; ?>&amp;do=add-language&amp;name='; ?>">
		<div><select name="langid">
			<?php
			foreach ($otherLanguages as $language) {
				echo '<option value="'.$language->id.'">'.$language->name.'</option>';
			}
			?>
		</select>
	<input type="submit" value="<?php echo get_string('sprachehinzu', 'block_desp');?>" /></div>
	</form>
<br/><br/>
						<a href="sprachen.php?courseid=<?php echo $COURSE->id; ?>&amp;return=sprachlernplaene"><?php echo get_string('sprachehinzufuegen','block_desp'); ?></a><br/>
					  <a href="mailto:portfolio@oesz.at?subject=Sprache%20in%20die%20Auswahlliste%20aufnehmen"><?php echo get_string('spracheaufnehmen','block_desp'); ?></a>
	<?php endif; ?>


        


    </div>
</div>

<?php
	include_once ("despfooter.php");
?>
<?php
echo $OUTPUT->footer($course);
?>