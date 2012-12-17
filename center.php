<?php
global $DB, $COURSE,$CFG,$OUTPUT;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div_partner.php';
require_once dirname(__FILE__) . '/lib/div.php';
$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);
//login required
require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/center.php';
$PAGE->set_url($url);
block_desp_print_header("center");
?>
<div id="page_margins">
    <div id="content">

        <h1><?php echo get_string('mitteilungszentrale','block_desp'); ?></h1>
        		<p><?php echo get_string('mitteilungszentrale_inhalt','block_desp'); ?></p>
				<h2><?php echo get_string('lernpartner_anderer_schueler','block_desp'); ?></h2>
				
				
<?php
$items=get_lernpartner($USER->id);
if (!$items) {
	echo " <div id='messagetxtslp3'>".get_string('keinelernpartnerschaft','block_desp')."</div>";
} else {

echo '<table class="tableform3 sprachlernplantab"><tr><th colspan="4">'.get_string('lernplaene','block_desp').'</th></tr><tr><th>'.get_string('name','block_desp').'</th><th>'.get_string('sprache','block_desp').'</th><th>'.get_string('bereich','block_desp').'</th><!--th>'.get_string('einschaetzung','block_desp').'</th--><th></th></tr>';
	foreach ($items as $item) {
		echo '<tr><td>'.fullname($item).'</td><td>'.$item->language.'</td>';
		echo '<td>'.$item->skill.'</td><!--td style="cursor:help;" title="'.$item->lernpartner_kommentar.'">'.full_bewertung($item->lernpartner_einschaetzung,"plan").'</td-->';
		
		echo '<td><a href="'.$CFG->wwwroot.'/blocks/desp/sprachlernplan_lernpartner_einschaetzung.php?courseid='.$COURSE->id.'&amp;checkitemid='.$item->id.'&amp;bl=center" class="link_center" style="color: #F25F2C !important;">'.get_string('feedbackansehen', 'block_desp').'</a></td></tr>';
	}
	echo '<tr><th colspan="4">'.get_string('checklisten', 'block_desp').'</th>';
	$items =get_lernpartner_check($USER->id);
	foreach ($items as $item) {
		echo '<tr><td>'.fullname($item).'</td><td>'.$item->language.'</td>';
		echo '<td>'.$item->skill.' '.$item->niveau.'</td><!--td>'.full_bewertung($item->einschaetzung_fremd,"check").'</td-->';
		
		echo '<td><a  class="link_center" style="color: #F25F2C !important;" href="'.$CFG->wwwroot.'/blocks/desp/lernpartner_einschaetzung.php?courseid='.$COURSE->id.'&amp;checkitemid='.$item->id.'&amp;bl=center">'.get_string("feedbackansehen","block_desp").'</a></td></tr>';
	}
	echo "</table>";
}
?>				
				<h2><?php echo get_string('meinelernaktivitaeten','block_desp'); ?></h2>
				
<?php
$inhalt='<table class="tableform3 sprachlernplantab" id="params">
                
                <thead>
                <tr>
                <th></th>
                     <th rowspan="2">'.get_string("meineplaenefuer","block_desp").'</th>
					<th class="slp">'.get_string("dastueichimmerwieder","block_desp").'</th>
                    <th colspan="2" class="slp">'.get_string("dasnehmeichmirvor","block_desp").'</th>
                    <th class="slp">'.get_string("erledigtam","block_desp").'</th>
                    <th class="slp">'.get_string("lernpartnerin","block_desp").'<input name="langid" value="28" type="hidden" />
                    	
						<input name="skid" value="1" type="hidden" />
						<input name="data" value="gesendet" type="hidden" /></th>
						<th class="slp"></th>
                </tr>
                <tr>
                <th></th>
					<th class="slp"></th>
                    <th class="slp">'.get_string("von","block_desp").'</th>
                    <th class="slp">'.get_string("bis","block_desp").'</th>
                    <th class="slp"></th>
                    <th class="slp"></th>
                     <th class="slp"></th>
                </tr></thead>';
	$ids=block_desp_ist_lernplanueberschreitung(4);
	$langcode=get_string("langcode","block_desp");
	$sql = "SELECT lp.*,la.".$langcode." as langname FROM {block_desp_learnplans} lp INNER JOIN {block_desp_learnplans_lang} l ON l.id=lp.langid INNER JOIN {block_desp_lang} la ON la.id=l.langid WHERE lp.userid=".$USER->id." AND lp.id IN (".$ids.") ORDER BY lp.langid,skillid";
	//echo $sql;
	$lp_items = $DB->get_records_sql($sql);
	$i=0;
	$sprache="init";
	foreach($lp_items as $rs) {
		if($sprache!=$rs->langname){
			$sprache=$rs->langname;
			$inhalt.='<tr><td colspan="8">'.$rs->langname.'</td></tr>';
		}
		$lernplansalarm=block_desp_ist_lernplanueberschreitung(3,$rs);
		if (!empty($lernplansalarm[0])) $warnung=' style="color:red;"';
		else $warnung="";
		$inhalt.= '<tr>';
		$inhalt.= '<td><img src="images/ov_skill'.$rs->skillid.'.gif"></td>';
		$inhalt.= '<td'.$warnung.'>'.$rs->title.'</td>';
		$inhalt.= '<td class="tddate">'; if ($rs->immer_wieder) $inhalt.='ja'; $inhalt.='</td>';
		$inhalt.= '<td class="tddate">'.$rs->starttime.'</td>';
		$inhalt.= '<td class="tddate">'.$rs->endtime.'</td>';
		$inhalt.= '<td class="tddate">'.$rs->donetime.'</td>';
		$inhalt.= '<td class="tdpartner">'.get_username($rs->lernpartnerid).'</td>';
		$inhalt.= '<td class="tddelete"><a class="link_center" href="'.$CFG->wwwroot.'/blocks/desp/sprachlernplan.php?courseid='.$courseid.'&amp;langid='.$rs->langid.'&amp;skid='.$rs->skillid.'">'.get_string("zumlernplan","block_desp").'</a></td>';
		$inhalt.= '</tr>';
		
		$rs->kommentar_gelesen=1;
		$DB->update_record('block_desp_learnplans',$rs);
	}
	$inhalt.="</table>";
echo $inhalt;
				?>
    </div>
</div>
<?php
	include_once ("despfooter.php");
?>
<?php
echo $OUTPUT->footer($course);
?>
