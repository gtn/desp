<?php
global $COURSE, $CFG, $OUTPUT;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/lib.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);
$do = optional_param('do', null, PARAM_ALPHANUMEXT);

require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/sprachenbiografie.php';
$PAGE->set_url($url);

$niveaus = array();
$p_niveaus =  $DB->get_records('block_desp_niveaus', array('parent_niveau'=>0), 'sorting');
foreach($p_niveaus as $niveau) {
	
	$sql = "SELECT * FROM {block_desp_descriptors} d WHERE d.niveauid=".$niveau->id." OR d.niveauid IN (SELECT n.id FROM {block_desp_niveaus} n WHERE parent_niveau = ".$niveau->id.")";

	$exist = $DB->get_records_sql($sql);
	if($exist)
		$niveaus[] = $niveau;
}

if ($do == 'add-language') {
	$langid = required_param('langid', PARAM_INT);

	$newLanguage = new StdClass;
	$newLanguage->userid = $USER->id;
	$newLanguage->langid = $langid;
	$DB->insert_record('block_desp_check_lang', $newLanguage);

	redirect($_SERVER['PHP_SELF'].'?courseid='.$courseid);
	exit;
}

if ($do == 'delete-language') {
	$id = required_param('id', PARAM_INT);

	$conditions = array("id" => $id, 'userid' => $USER->id);

	$DB->delete_records('block_desp_check_lang', $conditions);

	redirect($_SERVER['PHP_SELF'].'?courseid='.$courseid);
	exit;
}

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




$langcode=get_string("langcode","block_desp");
$myLanguagesSql = 'SELECT check_lang.*, lang.'.$langcode.'
	FROM {block_desp_check_lang} AS check_lang
	JOIN {block_desp_lang} AS lang ON check_lang.langid=lang.id
	WHERE check_lang.userid = ?
	ORDER BY lang.'.$langcode;

$myLanguages = $DB->get_records_sql($myLanguagesSql, array($USER->id));

/*
if (!$myLanguages) {
	// add first language
	$newLanguage = new StdClass;
	$newLanguage->userid = $USER->id;
	$newLanguage->langid = 1; // id = 1 => deutsch
	$DB->insert_record('block_desp_check_lang', $newLanguage);

	$myLanguages = $DB->get_records_sql($myLanguagesSql, array($USER->id));
}
*/

$otherLanguages = $DB->get_records_sql('SELECT *
	FROM {block_desp_lang}
	WHERE (userid=0 OR userid=?)
		AND id NOT IN (SELECT langid
		FROM {block_desp_check_lang}
		WHERE userid = ?
	)
	ORDER BY '.$langcode, array($USER->id, $USER->id));






block_desp_print_header("sprachenchecklisten");
?>


<script type="text/javascript">
    /*<![CDATA[*/

	function changeLanguage(id) {
		var name = prompt('Neuer Name der Sprache');
		
		if (name === null) return;
		if (!name) { alert('name darf nicht leer sein'); return; }

		document.location.href = '<?php echo $_SERVER['PHP_SELF'].'?courseid='.$courseid.'&do=change-language&id='; ?>' + encodeURIComponent(id)
			+ '&name=' + encodeURIComponent(name);
	}

	function deleteLanguage(id) {
		if (confirm("<?php echo get_string('sprachewirklichloeschen', 'block_desp');?>")) {
			if(confirm("<?php echo get_string('sprachewirklichloeschen_checkliste', 'block_desp');?>")) {
			document.location.href = '<?php echo $_SERVER['PHP_SELF'].'?courseid='.$courseid.'&do=delete-language&id='; ?>' + encodeURIComponent(id);
			}
		}
	}
    /*]]>*/
</script>
<div id="desp">
<div id="page_margins">
   
 <div id="content">

        <h1><?php echo get_string('meinesprachencheckliste','block_desp'); ?></h1>

        <h2><?php echo get_string('einfuehrungsprachencheckliste','block_desp'); ?></h2>


        <div id="messageboxses1" style="background: url('images/message_ses1.gif') no-repeat left top; ">
            <div id="messagetxtses1">
                <?php echo get_string('wasichalleskann','block_desp'); ?>
            </div>
        </div>



        <div id="messageboxses2" style="background: url('images/message_ses2.gif') no-repeat left top;">
            <div id="messagetxtses2">

					
                <?php echo get_string('hiereinschaetzen','block_desp'); ?><span class="mehrtext"><?php echo get_string('ausklappen','block_desp'); ?></span>
                
                <div class="messagetxthide">
                <br />
                <b><?php echo get_string('duverwendestdiechecklisten','block_desp'); ?></b>
                <ul>
                    <?php echo get_string('checklist','block_desp'); ?>
                </ul>
                <br />
                <?php echo get_string('verwendesymbole','block_desp'); ?>
                </div>
                
            </div>
        </div>

        <?php if(current_language() == "de" && file_exists("images/ses_grafik_kl.gif")) { ?>
        <br /><br /> 
        		<p style="text-align:center;">
					<a href="images/ses_grafik.gif"><img src="images/ses_grafik_kl.gif" alt="Sprachencheckliste Beispiel" /></a>
				</p>
        <br />
        <?php } ?>

       

	<?php foreach ($myLanguages as $language): ?>
        <table class="tableform3 overviewses">
            <tr>
                <th class="tableses1"><?php echo $language->$langcode; ?>
					<!-- a href="javascript:changeLanguage(<?php echo $language->id; ?>);">ändern</a -->
					<a href="javascript:deleteLanguage(<?php echo $language->id; ?>);"><?php echo get_string('loeschen','block_desp'); ?></a></th>
				<?php foreach ($niveaus as $niveau) {
					echo '<th class="tableses2">'.$niveau->title.'</th>';
				} ?>
            </tr>
			<?php foreach ($DB->get_records('block_desp_skills', null, 'sorting') as $skill) { ?>
            <tr>
            	<?php
            	if (!empty($skill->title)) echo '<td class="overview ov_skill'.$skill->id.'" style="background: #fff url(\'images/ov_skill'.$skill->id.'.gif\') no-repeat left top;padding-left:50px;">'.block_desp_skilltitle($skill->title).'</td>';
				
					foreach ($niveaus as $niveau) {
						$sql = 'SELECT sub.id FROM {block_desp_descriptors} AS des'.
						       ' JOIN {block_desp_niveaus} AS sub ON sub.id=des.niveauid'.
						       ' LEFT JOIN {block_desp_niveaus} AS main ON main.id=sub.parent_niveau'.
						       ' WHERE des.skillid = ? AND (main.id=? OR sub.id=?)'.
						       ' LIMIT 1';
						$skillNiveau = $DB->get_record_sql($sql, array($skill->id, $niveau->id, $niveau->id));
						if ($skillNiveau)
							echo '<td><a href="'.$CFG->wwwroot.'/blocks/desp/sprachencheckliste.php?courseid='.$courseid.'&amp;desplang='.$language->id.'&amp;skillid='.$skill->id.'&amp;niveauid='.$niveau->id.'" class="goto" style="background: #ffefd3 url(\'images/goto.gif\') no-repeat center;"></a></td>';
						else
							echo '<td></td>';
					}
				?>
            </tr>
			<?php } ?>
        </table>
	<?php endforeach; ?>

	<?php if ($otherLanguages): // nur anzeigen, falls es noch sprachen gibt ?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?courseid='.$courseid.'&amp;do=add-language&amp;name='; ?>">
		<div>
		<select name="langid">
			<?php
			foreach ($otherLanguages as $language) {
				echo '<option value="'.$language->id.'">'.$language->$langcode.'</option>';
			}
			?>
		</select>
		<input type="submit" value="<?php echo get_string('sprachehinzu','block_desp');?>"/>
	</div>
	</form>
<br/>
						<a href="sprachen.php?courseid=<?php echo $COURSE->id; ?>&amp;return=sprachenchecklisten"><?php echo get_string('neuesprachehinzufuegen','block_desp'); ?></a><br/>
					  <a href="mailto:portfolio@oesz.at?subject=Sprache%20in%20die%20Auswahlliste%20aufnehmen"><?php echo get_string('spracheaufnehmen','block_desp'); ?></a>
	<?php endif; ?>


    </div>
</div>

<?php
	include_once ("despfooter.php");
?>
</div>
<?php
echo $OUTPUT->footer($course);
?>