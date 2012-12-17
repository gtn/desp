<?php

function block_desp_pd_eportitems($wert=0,$exaport=false,$selname="dossier[]"){
	global $DB,$USER;
	
	if ($exaport==false){
		if ($wert==0) $wert="";
		$inhalt='<textarea name="dossier[]" cols="50" rows="1">'.$wert.'</textarea>';
	}else{
		$items = $DB->get_records('block_exaportitem', array("userid" => $USER->id));
		$inhalt='<select class="selbox_exaportitems" name="'.$selname.'">';
		$inhalt.='<option value="0"> </option>';
		
		foreach($items as $item){
			
			$inhalt.='<option value="'.$item->id.'"';
			if ($wert==$item->id) {
				$inhalt.=' selected="selected" ';
			}
			$inhalt.='>'.$item->name.'</option>';
		}
		$inhalt.='</select>';
	}
	return $inhalt;
}

function block_desp_splg_savedata($scope){
	global $DB,$USER;
	if (!empty($_POST["id"])){
		for ($i=0;$i<count($_POST["id"]);$i++){
			$dins=Array();
			$dins["langid"]=clean_param($_POST['langid'][$i], PARAM_SEQUENCE);
			$dins["partner"]=clean_param($_POST['partner'][$i], PARAM_TEXT);
			$dins["reason"]=clean_param($_POST['reason'][$i], PARAM_TEXT);
			$dins["period"]=clean_param($_POST['period'][$i], PARAM_TEXT);
			$dins["dossier"]=clean_param($_POST['dossier'][$i], PARAM_TEXT);
			$dins["userid"]=$USER->id;
			$dins["scope"]=$scope; //sprachlerngeschichte hat code 1

			if ($_POST['id'][$i]=="-1")	{
				//print_r($dins);
				$DB->insert_record('block_desp_lanhistories', $dins);
			}
			else if (intval($_POST['id'][$i])>0) {
				$dins["id"]=intval($_POST['id'][$i]);
				$DB->update_record('block_desp_lanhistories', $dins);
			}

		}
	}
}
function block_desp_splg_deletedata($id){
	global $DB,$USER;
	$DB->delete_records('block_desp_lanhistories',array("userid"=>$USER->id, "id"=>$id));
}
function block_desp_slg_getTrRows($scope=1,$felder,$courseid,$exaport=false){
	global $DB,$USER;
	$inhalt="";
	$lgeschichten = $DB->get_records('block_desp_lanhistories', array("userid" => $USER->id,"scope" => $scope));
	//print_r($lgeschichten);
	
	foreach($lgeschichten as $rs){
		$templ=block_desp_slg_getEmptyRowTemplate(true,$scope,$felder,$courseid,$exaport,$rs->dossier);
		$templ=str_replace("###id###",$rs->id,$templ);
		$seltemp=block_desp_createLanguageSelector($rs->langid,$scope);
		$templ=str_replace("###langid###",$seltemp,$templ);
		$templ=str_replace("###partner###",$rs->partner,$templ);
		$templ=str_replace("###reason###",$rs->reason,$templ);
		$templ=str_replace("###period###",$rs->period,$templ);
		$templ=str_replace("###dossier###",$rs->dossier,$templ);
		$inhalt.=$templ;
	}

	return $inhalt;
} 

function block_desp_createLanguageSelector($selectedval,$scope=1){
	global $DB, $USER;
	$langcode=get_string("langcode","block_desp");
	//if ($langcode=="de") $langcode="name";
	$inhalt='<select name="langid[]">';
	$allLanguages = $DB->get_records_select('block_desp_lang', 'userid=0 OR userid='.$USER->id, null,$langcode);
	$otherLanguages = $DB->get_records_sql('SELECT *
	FROM {block_desp_lang}
	WHERE (userid=0 OR userid=?)
		AND id NOT IN (SELECT langid
		FROM {block_desp_lanhistories}
		WHERE userid = ? AND scope='.$scope.' AND langid<>'.$selectedval.'
	)
	ORDER BY '.$langcode, array($USER->id, $USER->id));
	
	foreach ($allLanguages as $language) {
					$inhalt.='<option value="'.$language->id.'" ';
					if ($language->id==$selectedval) $inhalt.=' selected="selected"';
					$inhalt.='>'.$language->$langcode.'</option>';
	}
	$inhalt.='</select>';
	return $inhalt;
}
function block_desp_slg_getEmptyRowTemplate($writetr=true,$scope,$felder="partner,reason,period",$courseid,$exaport,$dossier){
	global $CFG;
	if ($writetr==true) $inhalt = '<tr class="row">';
	$inhalt.= '	<td class="tdlang"><input type="hidden" name="id[]" value="###id###" />###langid###</td>';

	$feldarr=explode(",",$felder);
	foreach ($feldarr as $feld){
		if ($exaport==true && $feld=="dossier"){
			$inhalt.= '<td>'.block_desp_pd_eportitems($dossier,$exaport).'</td>';
		}else{
			$inhalt.= '<td><textarea name="'.$feld.'[] cols="50" rows="1">###'.$feld.'###</textarea></td>';
		}
	}
	$dateiname="sprachlerngeschichte";
	if($scope==2) $dateiname="sprachlerngeschichte_bisher";
	else if($scope==3) $dateiname="sprachlerngeschichte_schule";
	
	$inhalt.='<td class="tddelete"><a href="'.$CFG->wwwroot.'/blocks/desp/'.$dateiname.'.php?courseid='.$courseid.'&amp;did=###id###"><img src="'.$CFG->wwwroot.'/pix/t/delete.gif" alt="delete" /></a></td>';
	if ($writetr==true) $inhalt.= '</tr>';
	return $inhalt;
}

function block_desp_slg_getEmptyRow($writetr=true,$courseid,$scope=1){
	$inhalt=block_desp_slg_getEmptyRowTemplate($writetr,1,"partner,reason,period",$courseid,$exaport,0);
	$inhalt=str_replace("###id###","-1",$inhalt);
	$lngt=block_desp_createLanguageSelector(-1);
	$inhalt=str_replace("###langid###",$lngt,$inhalt);
	$inhalt=str_replace("###partner###","",$inhalt);
	$inhalt=str_replace("###reason###","",$inhalt);
	$inhalt=str_replace("###period###","",$inhalt);
	$inhalt=str_replace("###dossier###","",$inhalt);
	
	return $inhalt;
}


?>