<?php

function get_lernpartner($userid){
	global $DB;
	$langcode=get_string("langcode","block_desp");
	$sql = 
	'SELECT lernplans.id, lernplans.title,lernplans.lernpartner_einschaetzung,lernplans.lernpartner_kommentar, u.id AS uid, u.firstname, u.lastname, lang.'.$langcode.' AS language, skill.title AS skill'.
	' FROM {block_desp_learnplans} AS lernplans'.
	' JOIN {block_desp_skills} AS skill ON lernplans.skillid=skill.id'.
	' JOIN {user} AS u ON u.id=lernplans.userid'.
	' JOIN {block_desp_learnplans_lang} AS check_lang ON check_lang.id=lernplans.langid AND check_lang.userid = u.id'.
	' JOIN {block_desp_lang} AS lang ON check_lang.langid=lang.id'.
	' WHERE lernplans.lernpartnerid=?'.
	' GROUP BY u.id, lang.id, skill.id'.
	' ORDER BY u.lastname, u.firstname, lang.'.$langcode.', skill.sorting, lernplans.title';
return $items = $DB->get_records_sql($sql, array($userid));
}

function get_lernpartner_check($userid){
	global $DB;
	$langcode=get_string("langcode","block_desp");
	$sql = 
	'SELECT check_item.id,check_item.einschaetzung_fremd, u.id AS uid, u.firstname, u.lastname, lang.'.$langcode.' AS language, skill.title AS skill, niveau.title AS niveau'.
	' FROM {block_desp_check_lang} AS check_lang'.
	' JOIN {user} AS u ON u.id=check_lang.userid'.
	' JOIN {block_desp_lang} AS lang ON check_lang.langid=lang.id'.
	' JOIN {block_desp_check_item} AS check_item ON check_item.languageid=check_lang.id AND check_item.lernpartnerid=?'.
	' JOIN {block_desp_descriptors} AS des ON check_item.descriptorid=des.id'.
	' JOIN {block_desp_skills} AS skill ON skill.id=des.skillid'.
	' JOIN {block_desp_niveaus} AS niveau_sub ON niveau_sub.id=des.niveauid'.
	' JOIN {block_desp_niveaus} AS niveau ON (niveau.id=niveau_sub.parent_niveau OR (niveau_sub.parent_niveau=0 AND niveau.id=niveau_sub.id))'.
//	' WHERE 1=0'. // testen wenn es keine lernpartner gibt
	' GROUP BY u.id, lang.id, skill.id, niveau.id'.
	' ORDER BY u.lastname, u.firstname, lang.'.$langcode.', skill.sorting, niveau.title';
return $items = $DB->get_records_sql($sql, array($userid));
}
function full_bewertung($wert,$bereich){
	if ($bereich=="check"){
		if ($wert==1) return "erreicht";
		elseif($wert==2) return "ausgezeichnet";
	}
	if ($bereich=="plan"){
		if ($wert==1) return "erreicht";
	}
	if ($wert==0) return "";
	return $wert;
}
?>
