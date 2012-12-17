<?php
function block_desp_splp_savedata(){
	global $DB, $USER, $possibleLernpartner;

	$notifyLernpartner = array();

	if (!empty($_POST["id"])){
		for ($i=0;$i<count($_POST["id"]);$i++){
			$dins=Array();
			$dins["langid"]=clean_param($_POST['langid'], PARAM_SEQUENCE);
			$dins["skillid"]=clean_param($_POST['skid'], PARAM_SEQUENCE);
			$dins["lernpartnerid"]=clean_param($_POST['lernpartnerid'][$i], PARAM_TEXT);
			$dins["title"]=clean_param($_POST['title'][$i], PARAM_TEXT);
			$dins["starttime"]=clean_param($_POST['starttime'][$i], PARAM_TEXT);
			$dins["endtime"]=clean_param($_POST['endtime'][$i], PARAM_TEXT);
			$dins["donetime"]=clean_param($_POST['donetime'][$i], PARAM_TEXT);
			$dins["userid"]=$USER->id;
			$dins["immer_wieder"]=clean_param(@$_POST['immer_wieder'][$i], PARAM_TEXT);

			$id = intval($_POST['id'][$i]);

			if (($id > 0) && ($dbTest = $DB->get_record('block_desp_learnplans', array('id'=>$id, 'userid'=>$USER->id))))	{
				$dins["id"] = $id;
				$DB->update_record('block_desp_learnplans', $dins);

				$notify = ($dins["lernpartnerid"] && $dins["lernpartnerid"] != $dbTest->lernpartnerid);
			} else {
				$dins["lernpartner_kommentar"] = '';
				$DB->insert_record('block_desp_learnplans', $dins);
				
				$notify = true;
			}

			if ($notify)
				$notifyLernpartner[$dins["lernpartnerid"]][] = $dins["title"];
		}
	}

	foreach ($notifyLernpartner as $userid => $changedDescriptors) {
		if (!empty($possibleLernpartner[$userid])){
			$user = $possibleLernpartner[$userid];
			// testen: email adresse ueberschreiben
			//$user->email = $USER->email;
	
			
			$text =
				get_string('lieber', 'block_desp').fullname($user)."\n\n".
				fullname($USER).get_string('alslernpartnerausgewaehlt', 'block_desp').
				join("\n", $changedDescriptors);
				
			// echo $text."\n\n\n";
	
			//directly email rather than using the messaging system to ensure its not routed to a popup or jabber
			email_to_user($user, $USER, 'Lernpartner', $text);
		}
	}
}

function block_desp_splp_deletedata($id){
	global $DB,$USER;
	$DB->delete_records('block_desp_learnplans',array("userid"=>$USER->id, "id"=>$id));
}
function block_desp_slp_check_lpitems($langid, $skid) {
	global $DB,$USER;
	
	$sql = "SELECT * FROM {block_desp_learnplan_items} lpi, {block_desp_learnplans} lp WHERE lp.title = lpi.title COLLATE utf8_general_ci and lpi.skillid = ".$skid." AND lp.langid=".$langid." AND lp.userid = ".$USER->id;
	$check = $DB->get_records_sql($sql);
	if($check)
		return true;
	else
		return false;
}
function block_desp_slp_import_lpitems($langid, $skid) {
	global $DB,$USER;
	$lp_items = $DB->get_records('block_desp_learnplan_items',array("skillid" => $skid));
	
	foreach($lp_items as $rs) {
		$data = new stdClass();
		$data->userid = $USER->id;
		$data->langid = $langid;
		$data->skillid = $skid;
		$data->title = $rs->title;
		$data->lernpartner_einschaetzung = 0;
		
		$DB->insert_record('block_desp_learnplans',$data);
	}
}
function block_desp_slp_getTrRows($langid, $skid, $courseid, $possibleLernpartner) {
	global $DB, $USER, $CFG;
	$inhalt = "";

	$lp_items = $DB->get_records('block_desp_learnplans', array("userid" => $USER->id, "langid" => $langid, "skillid" => $skid));
	$i=0;
	foreach($lp_items as $rs) {
		$lernplansalarm=block_desp_ist_lernplanueberschreitung(3,$rs);
		if (!empty($lernplansalarm[0])) $warnung=' style="color:red;"';
		else $warnung="";
		$inhalt.= '<tr>';
		$inhalt.= '<td><textarea rows="3" name="title[]" cols="" class="text sprachlernplantabth1input" '.$warnung.'>'.$rs->title.'</textarea><input name="id[]" value="'.$rs->id.'" type="hidden" /></td>';
		$inhalt.= '<td class="tddate"><input name="immer_wieder['.$i.']" type="checkbox" value="1" '; if ($rs->immer_wieder) $inhalt.='checked="checked"'; $inhalt.=' /></td>';
		$inhalt.= '<td class="tddate"><input name="starttime[]" value="'.$rs->starttime.'" type="text" maxlength="30" class="date" /></td>';
		$inhalt.= '<td class="tddate"><input name="endtime[]"  value="'.$rs->endtime.'" type="text" maxlength="30" class="date" /></td>';
		$inhalt.= '<td class="tddate"><input name="donetime[]"  value="'.$rs->donetime.'" type="text" maxlength="30" class="date" /></td>';
		$inhalt.= '<td class="tdpartner"><select size="1" name="lernpartnerid[]">
				<option value="">'.get_string('keinlernpartner', 'block_desp').'</option>
				<option value="">----------------</option>
		';
		foreach ($possibleLernpartner as $user) {
			$inhalt.= '<option value="'.$user->id.'"';
			if(@$rs->lernpartnerid == $user->id) $inhalt.= ' selected="selected"';
			$inhalt.= '>'.kuerzename($user->lastname,12).' '.kuerzename($user->firstname,1).'</option>';
		}
		$inhalt.= '</select><br />'.nl2br(trim($rs->lernpartner_kommentar)).'</td>';
		$inhalt.= '<td class="tddelete"><a href="'.$CFG->wwwroot.'/blocks/desp/sprachlernplan.php?courseid='.$courseid.'&amp;langid='.$langid.'&amp;skid='.$skid.'&amp;did='.$rs->id.'"><img src="'.$CFG->wwwroot.'/pix/t/delete.gif" alt="delete" /></a></td>';
		$inhalt.= '</tr>';
		
		$rs->kommentar_gelesen=1;
		$DB->update_record('block_desp_learnplans',$rs);
		$i++;
	}
	return $inhalt;
} 


function block_desp_get_skill_title($skid){
	global $DB;

	$lang = $DB->get_record('block_desp_skills', array("id" => $skid));
	if (!empty($lang)){
		return $lang->title;
	}else return "";
}
function block_desp_get_lang_title($langid){
	global $DB;
	$langcode=get_string("langcode","block_desp");
	$sql="SELECT l.".$langcode." FROM {block_desp_learnplans_lang} cl INNER JOIN {block_desp_lang} l ON l.id=cl.langid ";
	$sql.="WHERE cl.id=".$langid;

	$lang = $DB->get_record_sql($sql);
	//$lang = $DB->get_record('block_desp_lang', array("id" => $langid));
	if (!empty($lang)){
		return $lang->$langcode;
	}else return "";
}


?>