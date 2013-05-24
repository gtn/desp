<?php

global $DB,$COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div.php';
require_once dirname(__FILE__) . '/lib/lib.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_SEQUENCE);
$es = optional_param('es', 0, PARAM_INT);
$est="";
$langcode=get_string("langcode","block_desp");
if (optional_param('sveeinstellung', "", PARAM_TEXT)!=""){
	if (function_exists("clean_param_array")) $USER->desp=clean_param_array($_POST["sprache"],PARAM_SEQUENCE,true);
	else $USER->desp=optional_param('sprache', '', PARAM_SEQUENCE);
}

require_login($courseid);

$course = $DB->get_record('course',array("id"=>$courseid));
if (optional_param('desp_a','', PARAM_ALPHANUM)=="pr") $printversion=true;
else $printversion=false;

$url = '/blocks/desp/sprachenpass.php';
$PAGE->set_url($url);
//$inhalt=block_desp_print_header("sprachenpass",true,false);
$inhalt="";

if(!block_desp_checkimport())
	header("Location: ".$CFG->wwwroot."/blocks/desp/index.php?courseid=".$courseid);

//$niveaus =  $DB->get_records('block_desp_niveaus', array('parent_niveau'=>0), 'sorting');
$niveaus = array();
$p_niveaus =  $DB->get_records('block_desp_niveaus', array('parent_niveau'=>0), 'sorting');
foreach($p_niveaus as $niveau) {
	$sql = "SELECT * FROM {block_desp_descriptors} d WHERE WHERE d.niveauid=".$niveau->id." OR d.niveauid IN (SELECT n.id FROM {block_desp_niveaus} n WHERE parent_niveau = ".$niveau->id.")";
	$exist = $DB->get_records_sql($sql);
	if($exist)
		$niveaus[] = $niveau;
}

$myLanguagesSql="SELECT lang.id,lang.".$langcode." FROM {block_desp_lang} lang WHERE id IN(SELECT langid FROM {block_desp_check_lang} WHERE userid=?) OR id IN (SELECT langid FROM {block_desp_lanhistories} WHERE userid=?) ORDER BY lang.".$langcode;
$myLanguages = $DB->get_records_sql($myLanguagesSql, array($USER->id,$USER->id));

$all_tables = $DB->get_tables();

//achtung dossier aus exaport derzeit nicht eingebunden, bei aktivierung $exaport=false 6 zeilen weiter unten löschen;
if (in_array("block_exaportview", $all_tables)) {
	$exaport=true;
}else{
	$exaport=false;
}
//dossiers einstweilen nicht einbinden:
//$exaport=false;


$filecontent = '';
if ($printversion==true){
	if(is_file($CFG->dirroot . '/blocks/desp/templates/sprachenpasspdf1.html')) {
		$template1 = file_get_contents ($CFG->dirroot . '/blocks/desp/templates/sprachenpasspdf1.html');
	}
	if(is_file($CFG->dirroot . '/blocks/desp/templates/sprachenpasspdf2.html')) {
		$template = file_get_contents ($CFG->dirroot . '/blocks/desp/templates/sprachenpasspdf2.html');
	}
	$template1 = str_replace ( '###firstname###',$USER->firstname,$template1);
	$template1 = str_replace ( '###lastname###',$USER->lastname,$template1);
	$template1 = str_replace ( '###city###',$USER->city,$template1);
	$template1 = str_replace ( '###wwwroot###',$CFG->wwwroot.'/blocks/desp',$template1);

}else if ($es==1){
	if(is_file($CFG->dirroot . '/blocks/desp/templates/sprachenpasses.html')) {
		$template = file_get_contents ($CFG->dirroot . '/blocks/desp/templates/sprachenpasses.html');
	}
}else{

	if(is_file($CFG->dirroot . '/blocks/desp/templates/sprachenpass.html')) {
		$template = file_get_contents ($CFG->dirroot . '/blocks/desp/templates/sprachenpass.html');
	}
	$template = str_replace ( '###HEADER###', get_string("sprachenpass3","block_desp"), $template);
	$template = str_replace ( '###sprachenpass_inhalt###',get_string("sprachenpass_inhalt","block_desp"),$template);
	$template = str_replace ( '###sprachenpass_inhalt2###',get_string("sprachenpass_inhalt2","block_desp"),$template);
	$template = str_replace ( '###europasssprachenpass###',get_string("europasssprachenpass","block_desp"),$template);
}
$template = str_replace ( '###wwwroot###',$CFG->wwwroot."/blocks/desp",$template);

$template = str_replace ( '###courseid###',$courseid,$template);
$template = str_replace ( '###sprachenpasseinstellungen###',get_string("sprachenpasseinstellungen","block_desp"),$template);

$imgdir = make_upload_directory("desp/temp/userpic/{$USER->id}");
$fs = get_file_storage();
$context = $DB->get_record("context",array("contextlevel"=>30,"instanceid"=>$USER->id));
$files = $fs->get_area_files($context->id, 'user', 'icon', 0, '', false);
$file = reset($files);
unset($files);
//copy file
if($file) {

	$newfile=$imgdir.$file->get_filename();
	 
	$file->copy_content_to($newfile);
	$filestream=($file->get_content($file));
	 
	$imginfo = $file->get_imageinfo();

}
	
//$OUTPUT->user_picture($USER, array('size'=>156,'class'=>'printpassbild'));


/*$avatar = new user_picture($USER);
 $avatar->courseid = $courseid;
$avatar->link = true;

$inhalt_head_print.= @$OUTPUT->render($avatar);*/
//$inhalt_head_print.=$OUTPUT->user_picture($USER, array('size'=>156,'class'=>'printpassbild'));
//print_user_picture($USER, $course->id, $USER->picture, true, false, false);

//print_r($USER);

foreach ($myLanguages as $language){
	if ($es==1){
		$est.='<h2 class="despsp2" style=""><input type="checkbox" value="1" name="sprache['.$language->id.'][show]"';
		if (!empty($USER->desp[$language->id]["show"]) && $USER->desp[$language->id]["show"]==1) $est.=' checked="checked"';
		$est.=' />'.$language->$langcode.'</h2>';
	}
	$showlang=false;
	if (!empty($USER->desp)){
		if(!empty($USER->desp[$language->id]) && !empty($USER->desp[$language->id]["show"]) && $USER->desp[$language->id]["show"]==1) $showlang=true;
		if ($es==1) $showlang=true;
	}else{
		$showlang=true;
	}
	if ($showlang==true)
	{
		$inhalt.= '<div class="spasslangblock"><div><h1 class="despsp1" style="">'.$language->$langcode.'</h1></div>';
		$checklangsql = 'SELECT check_lang.* FROM {block_desp_check_lang} AS check_lang	WHERE check_lang.userid = ? AND check_lang.langid=?';
		$mychecklang = $DB->get_record_sql($checklangsql, array($USER->id,$language->id));

		if (!empty($mychecklang)){

			$inhalt.= '<div><table cellpadding="3" style="width:540pt;" class="tableform1 overviewses">';
			$inhalt.= '	<tr>';
			$inhalt.= '	<th class="tableses1">';
			$inhalt.= '	</th>';
			if ($niveaus){
				$anzahl=count($niveaus);
				$twidth=ceil(340/$anzahl);
			}
			foreach ($niveaus as $niveau) {
				$inhalt.= '<th class="tableses2" style="width:60pt">'.$niveau->title.'</th>';
			}
			$inhalt.= '	</tr>';
			foreach ($DB->get_records('block_desp_skills', null, 'sorting') as $skill) {
				$inhalt.= '	<tr>';
				$inhalt.= '	<td class="tableses1">'.block_desp_skilltitle($skill->title).'</td>';
				foreach ($niveaus as $niveau) {
					$rs=get_score($skill->id,$niveau->id,$mychecklang->id,$USER->id);
					if (is_null($rs->anz)) $rs->anz=0;
					if (is_null($rs->anz2)) $rs->anz2=0;
					if ($rs->anz>0){
						$proz=($rs->anz2/$rs->anz);
					}else{
						$proz=0;
					}
					if ($proz>=0.8) $cssstyle=' style="background-color:#cccccc;width:60pt;text-align:center;"';
					else $cssstyle=' style="width:60pt;text-align:center;"';
					$inhalt.= '<td class="lanhist" '.$cssstyle.'>'.$rs->anz2.'/'.$rs->anz.'</td>';
				}
				$inhalt.= '</tr>';
			}
			$inhalt.= '</table></div>';
		}
		//sprachlerngeschichte ermitteln:

		$histories =  $DB->get_records('block_desp_lanhistories', array('langid'=>$language->id,'userid'=>$USER->id), 'scope');
		if (!empty($histories)){
			if(is_file($CFG->dirroot . '/blocks/desp/templates/sprachenpasshistory.html')) {
				$templhist = file_get_contents ($CFG->dirroot . '/blocks/desp/templates/sprachenpasshistory.html');
			}
			$inhalt.= '<div><h2 class="despsp2">'.get_string("sp_sprachlerngeschichte","block_desp").'</h2>';
		}
		$scopeold=0;
		$inhalt2="";
		$hist1="";$hist2="";$hist3="";$templhist1="";$templhist2="";$templhist3="";
		foreach ($histories as $history){
				
			if ($history->scope==1){
				if ($scopeold!=$history->scope){
					$templhist1=$templhist;
					$temphisttheader=getSubpart($templhist, "###stheader###");
					$temphisttheader = str_replace ( '###sh1###',get_string("sp_history_mitwem","block_desp"),$temphisttheader);
					$temphisttheader = str_replace ( '###sh2###',get_string("sp_history_gelegenheit","block_desp"),$temphisttheader);
					$temphisttheader = str_replace ( '###sh3###',get_string("sp_history_haefigkeit","block_desp"),$temphisttheader);
					$templhist1=str_replace ( '###sp_history_header###',get_string("sp_history_familieheader","block_desp"),$templhist1);
					$templhist1=substituteSubpart($templhist1,"###stheader###",$temphisttheader);
					$scopeold=$history->scope;
				}
				$temphisttzeile=getSubpart($templhist, "###stzeile###");
				$temphisttzeile = str_replace ( '###s1###',htmlentities($history->partner),$temphisttzeile);
				$temphisttzeile = str_replace ( '###s2###',htmlentities($history->reason),$temphisttzeile);
				$temphisttzeile = str_replace ( '###s3###',htmlentities($history->period),$temphisttzeile);
				$hist1.=$temphisttzeile;
			}

			if ($history->scope==2){
				if ($scopeold!=$history->scope){
					$templhist2=$templhist;
					$temphisttheader=getSubpart($templhist, "###stheader###");
					$temphisttheader = str_replace ( '###sh1###',get_string("sp_history_wo","block_desp"),$temphisttheader);
					$temphisttheader = str_replace ( '###sh2###',get_string("sp_history_wielange","block_desp"),$temphisttheader);
					$temphisttheader = str_replace ( '###sh3###',get_string("sp_history_gelegenheit","block_desp"),$temphisttheader);
					$templhist2=str_replace ( '###sp_history_header###',get_string("sp_history_bisherheader","block_desp"),$templhist2);
					$templhist2=substituteSubpart($templhist2,"###stheader###",$temphisttheader);
					$scopeold=$history->scope;
				}
				$temphisttzeile=getSubpart($templhist, "###stzeile###");
				$temphisttzeile = str_replace ( '###s1###',$history->partner,$temphisttzeile);
				$temphisttzeile = str_replace ( '###s2###',$history->period,$temphisttzeile);
				$temphisttzeile = str_replace ( '###s3###',$history->reason,$temphisttzeile);
				$hist2.=$temphisttzeile;
			}

				
			if ($history->scope==3){
				if ($scopeold!=$history->scope){
					$templhist3=$templhist;
					$temphisttheader=getSubpart($templhist, "###stheader###");
					$temphisttheader = str_replace ( '###sh1###',get_string("sp_history_thema","block_desp"),$temphisttheader);
					$temphisttheader = str_replace ( '###sh2###',get_string("sp_history_gegenst","block_desp"),$temphisttheader);
					$temphisttheader = str_replace ( '###sh3###',get_string("sp_history_zeitraum","block_desp"),$temphisttheader);
					$templhist3=str_replace ( '###sp_history_header###',get_string("sp_history_schuleheader","block_desp"),$templhist3);
					$templhist3=substituteSubpart($templhist3,"###stheader###",$temphisttheader);
					$scopeold=$history->scope;
				}
				$temphisttzeile=getSubpart($templhist, "###stzeile###");
				$temphisttzeile = str_replace ( '###s1###',$history->reason,$temphisttzeile);
				$temphisttzeile = str_replace ( '###s2###',$history->partner,$temphisttzeile);
				$temphisttzeile = str_replace ( '###s3###',$history->period,$temphisttzeile);
				$hist3.=$temphisttzeile;
			}
				
				
				
		}
		$templhist1=substituteSubpart($templhist1,"###stzeile###",$hist1);
		$templhist2=substituteSubpart($templhist2,"###stzeile###",$hist2);
		$templhist3=substituteSubpart($templhist3,"###stzeile###",$hist3);
		$inhalt.=$templhist1.$templhist2.$templhist3;
		//$inhalt.=$templhist2;
		if (!empty($histories)) $inhalt.="</div>";
		$e="";
		if ($exaport){
			try {
				$views =  $DB->get_records('block_exaportview', array('userid'=>$USER->id,'langid'=>$language->id), 'name');
				$vieworitem=false;
				if (!empty($views)){
					$vieworitem=true;
					//<input type="checkbox" name="sprache['.$language->id.'][show]">
					if ($es==1) {
						$est.='<h3 class="desp_sp_esh3">'.get_string("sp_dossierviews","block_desp").':</h3>';
						$est.='<div><select class="desp_sp_einstellungen_dossier_select" multiple="multiple" name="sprache['.$language->id.'][views][]">';
						$est.='<option value="-1"></option>';
					}
					$itemlog=array();
					$inhaltviews="";

					foreach ($views as $view){
						$showview=false;
						if (!empty($USER->desp)){
							if(!empty($USER->desp) && !empty($USER->desp[$language->id]["views"]) && in_array($view->id,$USER->desp[$language->id]["views"])) $showview=true;
						}else{
							$showview=true;
						}
						if ($es==1) {
							$est.='<option value="'.$view->id.'"';
							if (!empty($USER->desp[$language->id]["views"]) && in_array($view->id,$USER->desp[$language->id]["views"])) $est.=' selected="selected"';
							$est.='>'.$view->name.'</option>';
							/*}else if ($printversion==false){
							 if ($showview==true) $inhaltviews.= '<div class="despp"><a href="'.$CFG->wwwroot.'/blocks/exaport/shared_view.php?courseid='.$courseid.'&amp;access=id/'.$USER->id.'-'.$view->id.'">'.$view->name.'</a></div>';
							*/
						}else{
							if ($showview==true){
								/*get single items */
								$query = "select b.*".
										" FROM {block_exaportviewblock} b".
										" WHERE b.viewid = ".$view->id." ORDER BY b.positionx, b.positiony";
									
								$blocks = $DB->get_records_sql($query);
									
								// read columns
								$columns = array();
									
								foreach ($blocks as $block) {
									if (!isset($columns[$block->positionx]))
										$columns[$block->positionx] = array();
										
									if ($block->type == 'item') {
										$conditions = array("id" => $block->itemid);
										if ($item = $DB->get_record("block_exaportitem", $conditions)) {
											$block->item = $item;
										} else {
											$block->type = 'text';
										}
									}
									$columns[$block->positionx][] = $block;
								}
								$inhaltviews.= '<div>';
								$column_num = 0;
								for ($column_i = 1; $column_i<=2; $column_i++) {
									if (!isset($columns[$column_i]))
										continue;
									$column_num++;

										
									foreach ($columns[$column_i] as $block) {
										if ($block->type == 'item') {

											$item = $block->item;
											if (empty($itemlog[$item->id])){
												$itemlog[$item->id]=1;
												$inhaltviews.=get_item_for_sp($item,$language->id);
											}
										} elseif ($block->type == 'personal_information') {
											if(isset($portfolioUser->description)) $inhaltviews.= '<div class="view-personal-information">'.$portfolioUser->description.'</div>';
										} elseif ($block->type == 'headline') {
											$inhaltviews.= '<div class="header">'.nl2br($block->text).'</div>';
										} else {
											// text
											$inhaltviews.= '<div class="view-text">';
											$inhaltviews.= $block->text;
											$inhaltviews.= '</div>';
										}
									}
								}
								$inhaltviews.='</div>';
							}
						}
					}
					if ($inhaltviews!="") $inhalt.= '<div><h2 class="despsp2">'.get_string("dossier","block_desp").'</h2></div>'.$inhaltviews;
					if ($es==1) {
						$est.='</select></div>';
					}
				}//not empty views
				$items =  $DB->get_records('block_exaportitem', array('userid'=>$USER->id,'langid'=>$language->id), 'name');
				if (!empty($items)){
					$vieworitem=true;
					if ($es==1) {
						$est.='<h3 class="desp_sp_esh3">'.get_string("dossiers","block_desp").':</h3>';
						$est.='<select class="desp_sp_einstellungen_dossier_select" multiple="multiple" name="sprache['.$language->id.'][items][]">';
						$est.='<option value="-1"></option>';
					}
					//echo "select * from mdl20_block_exaportitem where userid='".$USER->id."' and langid='".$language->id."'";

					foreach($items as $sitem){
							
						if ($es==1) {
							$est.='<option value="'.$sitem->id.'"';
							if (!empty($USER->desp[$language->id]["items"]) && in_array($sitem->id,$USER->desp[$language->id]["items"])) $est.=' selected="selected"';
							$est.='>'.$sitem->name.'</option>';
								
						}else{
							$showitem=false;
							if (!empty($USER->desp)){
								if(!empty($USER->desp) && !empty($USER->desp[$language->id]["items"]) && in_array($sitem->id,$USER->desp[$language->id]["items"])) $showitem=true;
							}else{
								$showitem=true;
							}
							if ($showitem==true){
								$inhalt.=get_item_for_sp($sitem,$language->id);
							}
						}
					}
					if ($es==1){
						$est.='</select>';
					}
				}
					
				if ($es==1){
					if ($vieworitem==true){
						$est.='<br />'.get_string("sp_anzeigeoptionen","block_desp").': <select class="desp_sp_einstellungen_dossier_select2" name="sprache['.$language->id.'][showmode]">';
						$est.='<option value="1"';
						if (!empty($USER->desp[$language->id]["showmode"]) && $USER->desp[$language->id]["showmode"]==1) $est.=' selected="selected"';
						$est.='>Inhalt und Titel</option>';
						$est.='<option value="2"';
						if (!empty($USER->desp[$language->id]["showmode"]) && $USER->desp[$language->id]["showmode"]==2) $est.=' selected="selected"';
						$est.='>nur Titel</option>';
						$est.='<option value="3"';
						if (!empty($USER->desp[$language->id]["showmode"]) && $USER->desp[$language->id]["showmode"]==3) $est.=' selected="selected"';
						$est.='>nur Inhalt</option>';
						$est.='</select><br />';
					}
					$est.='<div class="desp_sp_einst_gers">'.get_string("sp_filelinks","block_desp").': <input type="checkbox" value="1" name="sprache['.$language->id.'][9998]"';
					if (!empty($USER->desp[$language->id]["9998"]) && $USER->desp[$language->id]["9998"]==1) $est.=' checked="checked"';
					if (empty($USER->desp)) $est.=' checked="checked"';
					$est.=' /></div>';
					//$est.='<select class="desp_sp_einstellungen_dossier2_select" name="sprache['.$language->id.'][views][]">';
				}
					
					
			}
			catch (Exception $error)
			{
				//echo 'Exception caught: ',  $e->getMessage(), "\n";
			}
				
				
				
		}


		$inhalt.= "</div>";
	}
}

if (!empty($USER->desp["9999"]) && $USER->desp["9999"]==1) $showgers=true;
else if (!empty($USER->desp)) $showgers=false;
else $showgers=true;

if ($showgers==true){
	if ($printversion==false){


		$inhalt.='<div style="background-color:white">
		<table class="spgerstbl">
		<tr>
		<td class="spgers_h1"></td>
		<td class="spgers_h2"></td>
		<td class="spgers_h3" style="background-color:#6e6e70;">A1</td>
		<td class="spgers_h4" style="background-color:#6e6e70;">A2</td>
		<td class="spgers_h5" style="background-color:#6e6e70;">B1</td>
		</tr>

		<tr>
		<td class="spgers_s1" rowspan="2"><img src="images/gersverstehen_'.$langcode.'.jpg" alt="Gers verstehen" /></td>
		<td class="spgers_s2"><img src="images/gershoeren_'.$langcode.'.jpg" alt="'.get_string("hoehren","block_desp").'" /></td>
		<td class="spgers_s3">'.get_string('a1hoeren', 'block_desp').'</td>
		<td class="spgers_s4">'.get_string('a2hoeren', 'block_desp').'</td>
		<td class="spgers_s5">'.get_string('b1hoeren', 'block_desp').'</td>
		</tr>
		<tr>
		<td class="spgers_s2"><img src="images/gerslesen_'.$langcode.'.jpg" alt="'.get_string("lesen","block_desp").'" /></td>
		<td class="spgers_s3">'.get_string('a1lesen', 'block_desp').'</td>
		<td class="spgers_s4">'.get_string('a2lesen', 'block_desp').'</td>
		<td class="spgers_s5">'.get_string('b1lesen', 'block_desp').'</td>
		</tr>
		<tr>
		<td class="spgers_s1" rowspan="2"><img src="images/gerssprechen_'.$langcode.'.jpg" alt="'.get_string("sprechen","block_desp").'" /></td>
		<td class="spgers_s2"><img src="images/gersgespr_'.$langcode.'.jpg" alt="Gers sprechen" /></td>
		<td class="spgers_s3">'.get_string('a1angespraechenteilnehmen', 'block_desp').'</td>
		<td class="spgers_s4">'.get_string('a2angespraechenteilnehmen', 'block_desp').'</td>
		<td class="spgers_s5">'.get_string('b1angespraechenteilnehmen', 'block_desp').'</td>
		</tr>
		<tr>
		<td class="spgers_s2"><img src="images/zusammenhspr_'.$langcode.'.jpg" alt="'.get_string("zusammenhaengendsprechen","block_desp").'" /></td>
		<td class="spgers_s3">'.get_string('a1zusammenhaengendsprechen', 'block_desp').'</td>
		<td class="spgers_s4">'.get_string('a2zusammenhaengendsprechen', 'block_desp').'</td>
		<td class="spgers_s5">'.get_string('b1zusammenhaengendsprechen', 'block_desp').'</td>
		</tr>
		<tr>
		<td class="spgers_s1"><img src="images/gersschreiben1_'.$langcode.'.jpg" alt="'.get_string("schreiben","block_desp").'" /></td>
		<td class="spgers_s2"><img src="images/gersschreiben2_'.$langcode.'.jpg" alt="'.get_string("schreiben","block_desp").'" /></td>
		<td class="spgers_s3">'.get_string('a1schreiben', 'block_desp').'</td>
		<td class="spgers_s4">'.get_string('a2schreiben', 'block_desp').'</td>
		<td class="spgers_s5">'.get_string('b1schreiben', 'block_desp').'</td>
		</tr>
		</table>

		<table class="spgerstbl">
		<tr>
		<td class="spgers_h1"></td>
		<td class="spgers_h2"></td>
		<td class="spgers_h3">B2</td>
		<td class="spgers_h4">C1</td>
		<td class="spgers_h5">C2</td>
		</tr>
		<tr>
		<td class="spgers_s1" rowspan="2"><img src="images/gersverstehen_'.$langcode.'.jpg" alt="'.get_string("verstehen","block_desp").'" /></td>
		<td class="spgers_s2"><img src="images/gershoeren_'.$langcode.'.jpg" alt="'.get_string("hoehren","block_desp").'" /></td>
		<td class="spgers_s3">'.get_string('b2hoeren', 'block_desp').'</td>
		<td class="spgers_s4">'.get_string('c1hoeren', 'block_desp').'</td>
		<td class="spgers_s5">'.get_string('c2hoeren', 'block_desp').'</td>
		</tr>
		<tr>
		<td class="spgers_s2"><img src="images/gerslesen_'.$langcode.'.jpg" alt="'.get_string("lesen","block_desp").'" /></td>
		<td class="spgers_s3">'.get_string('b2lesen', 'block_desp').'</td>
		<td class="spgers_s4">'.get_string('c1lesen', 'block_desp').'</td>
		<td class="spgers_s5">'.get_string('c2lesen', 'block_desp').'</td>
		</tr>
		<tr>
		<td class="spgers_s1" rowspan="2"><img src="images/gerssprechen_'.$langcode.'.jpg" alt="'.get_string("sprechen","block_desp").'" /></td>
		<td class="spgers_s2"><img src="images/gersgespr_'.$langcode.'.jpg" alt="'.get_string("angespraechenteilnehmen","block_desp").'" /></td>
		<td class="spgers_s3">'.get_string('b2angespraechenteilnehmen', 'block_desp').'</td>
		<td class="spgers_s4">'.get_string('c1angespraechenteilnehmen', 'block_desp').'</td>
		<td class="spgers_s5">'.get_string('c2angespraechenteilnehmen', 'block_desp').'</td>
		</tr>
		<tr>
		<td class="spgers_s2"><img src="images/zusammenhspr_'.$langcode.'.jpg" alt="'.get_string("zusammenhaengendsprechen","block_desp").'" /></td>
		<td class="spgers_s3">'.get_string('b2zusammenhaengendsprechen', 'block_desp').'</td>
		<td class="spgers_s4">'.get_string('c1zusammenhaengendsprechen', 'block_desp').'</td>
		<td class="spgers_s5">'.get_string('c2zusammenhaengendsprechen', 'block_desp').'</td>
		</tr>
		<tr>
		<td class="spgers_s1"><img src="images/gersschreiben1_'.$langcode.'.jpg" alt="'.get_string("schreiben","block_desp").'" /></td>
		<td class="spgers_s2"><img src="images/gersschreiben2_'.$langcode.'.jpg" alt="'.get_string("schreiben","block_desp").'" /></td>
		<td class="spgers_s3">'.get_string('b2schreiben', 'block_desp').'</td>
		<td class="spgers_s4">'.get_string('c1schreiben', 'block_desp').'</td>
		<td class="spgers_s5">'.get_string('c2schreiben', 'block_desp').'</td>
		</tr>
		</table></div>
		';
	}else{
		if(current_language() == "de") {
			$inhalt.='<br /><div style="page-break-before:always"><img src="images/gers1.png" alt="Gers Tabelle 1" width="519" height="750" /><br />';
			$inhalt.='<img src="images/gers2.png" alt="Gers Tabelle 2" width="595" height="750" style="margin-top:10px;"></div>';
		} else {
			$inhalt.='<br /><div style="page-break-before:always"><img src="images/gers1_en.png" alt="Gers table 1" width="519" height="581" /><br />';
			$inhalt.='<img src="images/gers2_en.png" alt="Gers table 2" width="519" height="615" style="margin-top:10px;"></div>';
		}
	}
}

if ($printversion==false){
	$inhalt.='<div><a target="_blank" href="'.$CFG->wwwroot.'/blocks/desp/sprachenpass.php?courseid='.$courseid.'&amp;desp_a=pr">'.get_string('pdferstellen', 'block_desp').'</a></div>';
}
//$inhalt.= $OUTPUT->footer($course);

if ($es==1){
	$est.='<div class="desp_sp_einst_gers">'.get_string('gerstabelle', 'block_desp').'<input type="checkbox" value="1" name="sprache[9999]"';
	if (!empty($USER->desp["9999"]) && $USER->desp["9999"]==1) $est.=' checked="checked"';
	if (empty($USER->desp)) $est.=' checked="checked"';
	$est.=' /></div>';
	$est.='<div><input type="submit" name="sveeinstellung" value="'.get_string('einstellungenmerken', 'block_desp').'" /></div>';
	$est.='</form>';
}

if ($printversion==true){
	$template = str_replace ( '###inhalt###',$inhalt,$template);

	 
	// convert in PDF
	require_once($CFG->dirroot.'/lib/tcpdf/tcpdf.php');
	try
	{
		$pdf = new TCPDF("P", "pt", "A4", true, 'UTF-8', false);
		$pdf->SetTitle('sprachenpass');
		$pdf->AddPage();
		if($file) $pdf->Image($newfile,510,122, 40, 40);
		//if($file) $pdf->Image('@'.base64_encode($file->get_content()), $imginfo['width'], $imginfo['height']);
		$pdf->writeHTML($template1, true, false, true, false, '');
		$pdf->AddPage();
		$pdf->writeHTML($template, true, false, true, false, '');
		$pdf->Output('sprachenpass.pdf', 'I');
		unlink($newfile);
	}
	catch(tcpdf_exception $e) {
		echo $e;
		exit;
	}

}else if ($es==1) {
	$template = str_replace ( '###outputheader###',block_desp_print_header("sprachenpass",true,false),$template);
	$template = str_replace ( '###outputfooter###',$OUTPUT->footer($course),$template);
	$template = str_replace ( '###inhalt###',$est,$template);
}else{
	$template = str_replace ( '###outputheader###',block_desp_print_header("sprachenpass",true,false),$template);
	$template = str_replace ( '###outputfooter###',$OUTPUT->footer($course),$template);
	$template = str_replace ( '###inhalt###',$inhalt,$template);
}
echo $template;

function get_score($skillid,$niveauid,$langid,$userid){
	global $DB;
	//count(main.id) as anz,count(u.einschaetzung_selbst) as anz2
	$sql = 'SELECT count(main.id) as anz,sum(IF(u.einschaetzung_selbst>0,1,0)) as anz2 FROM {block_desp_descriptors} AS des'.
			' JOIN {block_desp_niveaus} AS sub ON sub.id=des.niveauid'.
			' LEFT JOIN {block_desp_niveaus} AS main ON main.id=sub.parent_niveau'.
			' LEFT JOIN {block_desp_check_item} AS u ON des.id=u.descriptorid AND  u.languageid='.$langid.
			' WHERE des.skillid = ? AND (main.id=? OR sub.id=?) AND des.parent_id=0';
	//parent_id=0, weil unterdeskriptoren nicht in der grundgesamtheit zählen sollen
	//
	//echo $sql;
	$rs = $DB->get_record_sql($sql, array($skillid, $niveauid, $niveauid));
	 
	$sql='SELECT d.title FROM {block_desp_descriptors} d INNER JOIN {block_desp_niveaus} n ON d.niveauid=n.id WHERE n.parent_niveau='.$niveauid.' AND skillid='.$skillid.'';
	//echo $sql;
	//$rs = $DB->get_record_sql($sql);
	return $rs;
}

function get_item_for_sp($item,$langid){
	global $USER,$CFG;
	$inhalt= '<div class="sp_exaport">';
	if (!empty($USER->desp[$langid]["showmode"]) && ($USER->desp[$langid]["showmode"]==2 || $USER->desp[$langid]["showmode"]==1)) {
		$inhalt.= '<b>'.trim($item->name).'</b>';
		if ($USER->desp[$langid]["showmode"]==1) $inhalt.=": ";
	}
	$intro=$item->intro."<br />";
	$intro=str_replace("</p>","<br />",$intro);
	$intro=str_replace("<p>","",$intro);
	$intro=str_replace("<br /><br />","<br />",$intro);
	/*$pos = strpos($intro, "<span class='sp_exaporttxt'>- ");
	 if ($pos===FALSE || $pos>1) $intro="<span class='sp_exaporttxt'>- ".$intro."</span>";
	*/
	if (!empty($USER->desp[$langid]["showmode"]) && ($USER->desp[$langid]["showmode"]==3 || $USER->desp[$langid]["showmode"]==1)) {
		$inhalt.= ''.$intro.'';
		//print_r($item);
		/*if ($rsfiles = $DB->get_records("files", Array("itemid"=>$item->id))) {
		 foreach($rsfiles as $rsfile){
			
		if (!empty($rsfile->mimetype)){

		$webimages=array("image/png","image/gif","image/jpg","image/jpeg");
		if (in_array($rsfile->mimetype,$webimages)){*/
		if($item->type=="file"){
			if (empty($USER->desp) || (!empty($USER->desp[$langid]["9998"]) && $USER->desp[$langid]["9998"]==1)){
				$inhalt.='<a href="'.$CFG->wwwroot.'/blocks/exaport/portfoliofile.php?access=portfolio/id/' . $item->userid . '&amp;itemid='.$item->id.'">'.$CFG->wwwroot.'/blocks/exaport/portfoliofile.php?access=portfolio/id/' . $item->userid . '&amp;itemid='.$item->id.'</a>';
			}
		}
		/*}
		 break;
		}
		}
		}*/
		//$inhaltviews.=$CFG->wwwroot."/blocks/exaport/portfoliofile.php?access=portfolio/id/" . $item->userid . "&itemid=" . $item->id;
	}
	$inhalt.='<br /></div>';
	return $inhalt;
}
?>