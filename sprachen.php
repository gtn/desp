<?php
global $COURSE, $CFG, $OUTPUT;
require_once dirname(__FILE__) . '/inc.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);
$do = optional_param('do', null, PARAM_ALPHANUMEXT);
	$langcode=get_string("langcode","block_desp");
require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/sprachen.php';
$PAGE->set_url($url);

if ($do == 'add-language') {
	$name = required_param('name', PARAM_TEXT);

	$newLanguage = new StdClass;
	$newLanguage->userid = $USER->id;
	$newLanguage->de = $name;
	$newLanguage->en = $name;

	$DB->insert_record('block_desp_lang', $newLanguage);

	// redirect($_SERVER['PHP_SELF'].'?courseid='.$courseid);
	// exit;
}

if ($do == 'delete-language') {
	$id = required_param('id', PARAM_INT);

	if (block_desp_lang_references($id)) die('still referenced');
	
	$conditions = array("id" => $id, 'userid' => $USER->id);
	$DB->delete_records('block_desp_lang', $conditions);

	redirect($_SERVER['PHP_SELF'].'?courseid='.$courseid);
	exit;
}

/*
if ($do == 'change-language') {
	$name = required_param('name', PARAM_TEXT);
	$id = required_param('id', PARAM_INT);

	$language = $DB->get_record('block_desp_check_lang', array('id'=>$id, 'userid'=>$USER->id));
	if ($language) {
		$language->name = $name;
		$DB->update_record('block_desp_check_lang', $language);
	}

	redirect($_SERVER['PHP_SELF'].'?courseid='.$courseid);
	exit;
}
*/

function block_desp_lang_references($id) {
	global $DB, $USER;
	
	$references = array();

	if ($DB->get_field_sql("SELECT COUNT(*) FROM {block_desp_check_lang} WHERE langid=?", array($id)))
		$references[] = 'sprachencheckliste';

	if ($DB->get_field_sql("SELECT COUNT(*) FROM {block_desp_learnplans_lang} WHERE langid=?", array($id)))
		$references[] = 'sprachlernplan';

	if ($DB->get_field_sql("SELECT COUNT(*) FROM {block_desp_lanhistories} WHERE langid=?", array($id)))
		$references[] = 'sprachlerngeschichte';

	
	return $references;
}

$myLanguages = $DB->get_records_sql('SELECT *
	FROM {block_desp_lang}
	WHERE userid = ?
	ORDER BY '.$langcode, array($USER->id));

$otherLanguages = $DB->get_records_sql('SELECT *
	FROM {block_desp_lang}
	WHERE userid = 0
	ORDER BY '.$langcode);



block_desp_print_header("sprachen");
?>
<div id="desp">
<div id="page_margins">
    <div id="content">

        <h1><?php echo get_string('hinzufuegenneuesprache','block_desp'); ?></h1>

        

        <div id="messageboxses1" style="background: url('images/message_ses1.gif') no-repeat left top; ">
            <div id="messagetxtses1">
               <?php echo get_string('hinzufuegenneuesprache_inhalt','block_desp'); ?>
            </div>
        </div>

	<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
		<div>
			<input type="hidden" name="do" value="add-language" />
			<input type="text" name="name" />
			<input type="submit" value="<?php echo get_string('sprachehinzu','block_desp'); ?>" />
		</div>
	</form>

	<?php 

		if ($myLanguages):
			?>
        <table class="tableform3 overviewses">
            <tr>
                <th class="tableses1" colspan="2"><?php echo get_string('sprache','block_desp'); ?></th>
            </tr>
       
			<?php foreach ($myLanguages as $language): ?>
            <tr>
                <td><?php echo $language->$langcode; ?></td>
                <td><?php 
					if ($references = block_desp_lang_references($language->id)) {
						echo join(', ', $references);
					} else {
						echo '<a href="sprachen.php?courseid='.$COURSE->id.'&amp;do=delete-language&amp;id='.$language->id.'">'.get_string('loeschen','block_desp').'</a>';
					}
				?></td>
            </tr>
			<?php endforeach; ?>
        </table>
	<?php 
		else:
			// no languages
			
			echo get_string('keinesprachen', 'block_desp');
			
		endif; ?>


    </div>
</div>
</div>
<?php
	include_once ("despfooter.php");
?>
<?php
echo $OUTPUT->footer($course);
?>