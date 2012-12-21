<?php

function substituteSubpart($content, $marker, $subpartContent, $recursive = 0, $keepMarker = 0) {
		$start = strpos($content, $marker);

		if ($start === FALSE) {
			return $content;
		}

		$startAM = $start + strlen($marker);
		$stop = strpos($content, $marker, $startAM);

		if ($stop === FALSE) {
			return $content;
		}

		$stopAM = $stop + strlen($marker);
		$before = substr($content, 0, $start);
		$after = substr($content, $stopAM);
		$between = substr($content, $startAM, $stop - $startAM);


		if ($keepMarker) {
			$matches = array();
			if (preg_match('/^([^\<]*\-\-\>)(.*)(\<\!\-\-[^\>]*)$/s', $between, $matches) === 1) {
				$before .= $marker . $matches[1];
				$between = $matches[2];
				$after = $matches[3] . $marker . $after;
			} elseif (preg_match('/^(.*)(\<\!\-\-[^\>]*)$/s', $between, $matches) === 1) {
				$before .= $marker;
				$between = $matches[1];
				$after = $matches[2] . $marker . $after;
			} elseif (preg_match('/^([^\<]*\-\-\>)(.*)$/s', $between, $matches) === 1) {
				$before .= $marker . $matches[1];
				$between = $matches[2];
				$after = $marker . $after;
			} else {
				$before .= $marker;
				$after = $marker . $after;
			}

		} else {
			$matches = array();
			if (preg_match('/^(.*)\<\!\-\-[^\>]*$/s', $before, $matches) === 1) {
				$before = $matches[1];
			}

			if (is_array($subpartContent)) {
				$matches = array();
				if (preg_match('/^([^\<]*\-\-\>)(.*)(\<\!\-\-[^\>]*)$/s', $between, $matches) === 1) {
					$between = $matches[2];
				} elseif (preg_match('/^(.*)(\<\!\-\-[^\>]*)$/s', $between, $matches) === 1) {
					$between = $matches[1];
				} elseif (preg_match('/^([^\<]*\-\-\>)(.*)$/s', $between, $matches) === 1) {
					$between = $matches[2];
				}
			}

			$matches = array(); // resetting $matches
			if (preg_match('/^[^\<]*\-\-\>(.*)$/s', $after, $matches) === 1) {
				$after = $matches[1];
			}
		}

		if (is_array($subpartContent)) {
			$between = $subpartContent[0] . $between . $subpartContent[1];
		} else {
			$between = $subpartContent;
		}

		return $before . $between . $after;
	}
	
function getSubpart($content, $marker) {
		$start = strpos($content, $marker);

		if ($start === FALSE) {
			return '';
		}

		$start += strlen($marker);
		$stop = strpos($content, $marker, $start);

			// Q: What shall get returned if no stop marker is given
			// /*everything till the end*/ or nothing?
		if ($stop === FALSE) {
			return ''; /*substr($content, $start)*/
		}

		$content = substr($content, $start, $stop - $start);

		$matches = array();
		if (preg_match('/^([^\<]*\-\-\>)(.*)(\<\!\-\-[^\>]*)$/s', $content, $matches) === 1) {
			return $matches[2];
		}

		$matches = array(); // resetting $matches
		if (preg_match('/(.*)(\<\!\-\-[^\>]*)$/s', $content, $matches) === 1) {
			return $matches[1];
		}

		$matches = array(); // resetting $matches
		if (preg_match('/^([^\<]*\-\-\>)(.*)$/s', $content, $matches) === 1) {
			return $matches[2];
		}

		return $content;
}


function block_desp_ist_lernplanueberschreitung($art=1,$rs1=NULL){
	/* bei art=1 bis art=3 lernplanüberschreitung, bei art=4 gerade bearbeitete lernpläne*/
	global $USER,$DB;
	
	if ($art==3){
		$wert=array();
		$rs[0]=$rs1;
	}else if ($art==4){
			$wert="-1";
		$sql="SELECT * FROM {block_desp_learnplans} WHERE userid=? AND (endtime<>'' OR starttime<>'' OR donetime<>'')";
		//echo $sql.$USER->id;
		$rs = $DB->get_records_sql($sql,array("userid"=>$USER->id));
	}else{
		$wert=array();
		$sql="SELECT * FROM {block_desp_learnplans} WHERE userid=? AND endtime<>''";
		$rs = $DB->get_records_sql($sql,array("userid"=>$USER->id));
	}
	foreach ($rs as $lernplan){
		//$datum=strtotime($lernplan->endtime);
		//echo $lernplan->endtime."++".$datum."++".date("d.m.y",$datum)."<br>";
		
		$ds=block_desp_splitdatum($lernplan->endtime);
		$ds2=block_desp_splitdatum($lernplan->donetime);
		$ds3=block_desp_splitdatum($lernplan->starttime);
		$datum=time()+1000000;
		$datum2=time()+1000005;
		$datum3=time()+1000000;
		if (checkdate($ds->monat,$ds->tag,$ds->jahr)){
			$datum=strtotime($ds->monat."/".$ds->tag."/".$ds->jahr);
		}else if (checkdate($ds->tag,$ds->monat,$ds->jahr)){
			$datum=strtotime($ds->tag."/".$ds->monat."/".$ds->jahr);
		}
		if (checkdate($ds2->monat,$ds2->tag,$ds2->jahr)){
			$datum2=strtotime($ds2->monat."/".$ds2->tag."/".$ds2->jahr);
		}else if (checkdate($ds2->tag,$ds2->monat,$ds2->jahr)){
			$datum2=strtotime($ds2->tag."/".$ds2->monat."/".$ds2->jahr);
		}
		if (checkdate($ds3->monat,$ds3->tag,$ds3->jahr)){
			$datum3=strtotime($ds3->monat."/".$ds3->tag."/".$ds3->jahr);
		}else if (checkdate($ds3->tag,$ds3->monat,$ds3->jahr)){
			$datum3=strtotime($ds3->tag."/".$ds3->monat."/".$ds3->jahr);
		}
		if ($art==4){//rückgabe der gerade bearbeiteten lernpläne
			if ($datum3<time() && $datum2>time()) $wert.=",".$lernplan->id; 
			else if ($datum>time() && $datum2>time()) $wert.=",".$lernplan->id; 
		}else if ($datum<time() && !($datum2<time())){
			
			if($art==1)	$wert[$lernplan->langid]=$lernplan->langid;
			if($art==2)	$wert[$lernplan->langid][$lernplan->skillid]=$lernplan->skillid;
			if($art==3) $wert[0]=1;
		}else $wert=array();
		//echo $lernplan->endtime."++".date("d.m.y",$datum);
		//echo "<br>";
	}
	return $wert;
}
function get_username($userid){
	global $DB;
	$userid=intval($userid);
	if ($userid>0){
		if ($usr = $DB->get_record('user',array("id"=>$userid))){
			return $usr->firstname." ".$usr->lastname;
		}else return "";
	}else return "";
}
function block_desp_splitdatum($datumstr){
	$ds=new stdClass();
	$ds->tag="99";$ds->monat="99";$ds->jahr="9999";
		if (substr_count($datumstr,".")==2){
			$datumarr = explode(".", $datumstr);
			$ds->tag=$datumarr[0];$ds->monat=$datumarr[1];$ds->jahr=$datumarr[2];
		}else if (substr_count($datumstr,"/")==2){
			$datumarr = explode("/", $datumstr);
			$ds->tag=$datumarr[1];$ds->monat=$datumarr[0];$ds->jahr=$datumarr[2];
		}else if (substr_count($datumstr,"-")==2){
			$datumarr = explode("-", $datumstr);
			$ds->tag=$datumarr[1];$ds->monat=$datumarr[0];$ds->jahr=$datumarr[2];
		}
		return $ds;
}
function block_desp_checkimport() {
	global $DB;
	return ($DB->get_records('block_desp_niveaus')) ? true : false;
}
function block_desp_lernplanalarm(){
	global $USER;
	$lernplansalarm=block_desp_ist_lernplanueberschreitung(1);
	$sprtxt="";
	$i=0;
	if (!empty($lernplansalarm)){
		foreach($lernplansalarm as $sprache){
			if ($i==0) $sprtxt=block_desp_get_lang_title($sprache);
			else $sprtxt.=", ".block_desp_get_lang_title($sprache);
			$i++;
		}
	if ($i==1) $sprhead="bei der Sprache";
	else $sprhead="bei den Sprachen";
	$inhalt= '
	<div class="messageimportantinfo" style="display:;">
		<div style="font-size:35px;font-weight: bold; float:left;font-family: Times, Verdana, Arial, sans-serif; padding-right:20px;">!</div>
		<div>'.get_string('vorgegebenesziel', 'block_desp').$sprhead.' '.$sprtxt.get_string('lernplaeneueberpruefen', 'block_desp').'</div> 
	</div>
	';
	return $inhalt;
	}else{
		return "";
	}
}
function block_desp_check_new_learnplan_comments() {
	global $USER, $DB;
	
	$check = $DB->get_records("block_desp_learnplans",array("userid"=>$USER->id,"kommentar_gelesen"=>0));
	if($check)
		return true;
	else
		return false;
}
function block_desp_get_examplelink($descrid,$lang){
	global $DB,$CFG;
	  $sql = "SELECT mm.* ";
    $sql.= "FROM {block_desp_descrexamp_mm} mm INNER JOIN {block_desp_examples} ex ON ex.id=mm.exampid ";
    $sql.= " WHERE mm.descrid=".$descrid." AND ex.lang=".$lang;

    $examples = $DB->get_records_sql($sql);
	
		//$examples=$DB->get_records('block_desp_descrexamp_mm', array("descrid" => $descrid));
	
	$returntext = "";
	if($examples){
		//$returntext = "Aufgabenbeispiel";
		$returntext = get_string('Aufgabenbeispiel', 'block_desp')." ";
	}
	foreach($examples as $example) {
		$e = $DB->get_record('block_desp_examples',array("id"=>$example->exampid));
		if($e->task) {
			$img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/pdf.gif" height="16" width="16" alt="Aufgabenstellung" />';
			$returntext .= '<a target="_blank" href="' . $e->task . '" onmouseover="Tip(\''.$e->title.'\')" onmouseout="UnTip()">'.$img.'</a> -';
		}
		if($e->solution) {
			$img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/pdf_solution.gif" height="16" width="16" alt="Lösung" />';
			$returntext .= '<a target="_blank" href="' . $e->solution . '" onmouseover="Tip(\''.$e->title.'\')" onmouseout="UnTip()">'.$img.'</a> -';
		}
		if($e->attachement) {
			$img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/attach_2.png" height="16" width="16" alt="Anhang" />';
			$returntext .= '<a target="_blank" href="' . $e->attachement . '" onmouseover="Tip(\''.$e->title.'\')" onmouseout="UnTip()">'.$img.'</a> -';
		}
		if($e->completefile) {
			$img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/folder.png" height="16" width="16" alt="Gesamt-Datei" />';
			$returntext .= '<a target="_blank" href="' . $e->completefile . '" onmouseover="Tip(\''.$e->title.'\')" onmouseout="UnTip()">'.$img.'</a> -';
		}
		if($e->externaltask) {
			$img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/pdf.gif" height="16" width="16" alt="Externe Aufgabe" />';
			$returntext .= '<a target="_blank" href="' . $e->externaltask . '" onmouseover="Tip(\''.$e->title.'\')" onmouseout="UnTip()">'.$img.'</a> -';
		}if($e->externalurl) {
			$img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/link.png" height="16" width="16" alt="Externer Link" />';
			$returntext .= '<a target="_blank" href="' . $e->externalurl . '" onmouseover="Tip(\''.$e->title.'\')" onmouseout="UnTip()">'.$img.'</a> -';
		}
		
	}
	
	if (!empty($returntext))
		return substr($returntext,0,-2);
	else
		return "";
	
	if (!empty($rs)){
		return '<br /><a href="'.$CFG->wwwroot.'/blocks/desp/lerntips.php"><img src="'.$CFG->wwwroot.'/blocks/desp/images/lernplaene.gif" style="margin-right:7px;"></a><a style="line-height:16px;" href="'.$CFG->wwwroot.'/blocks/desp/lerntips.php">Aufgaben</a>';
	}else return "";
}


function block_desp_print_header($item_identifier,$closehead=true,$printit=true) {

        global $COURSE;
        
        $strbookmarks = get_string($item_identifier, "block_desp");
        $adminbookmarks = get_string('blocktitle', "block_desp");

        // navigationspfad
        $navlinks = array();
        $navlinks[] = array('name' => $adminbookmarks, 'link' => "index.php?courseid=" . $COURSE->id, 'type' => 'title');

        $nav_item_identifier = $item_identifier;

        $icon = $item_identifier;
        $currenttab = $item_identifier;

        $item_name = get_string($nav_item_identifier, "block_desp");
        if ($item_name[0] == '[')
            $item_name = get_string($nav_item_identifier);
    if($item_identifier == "center" || $item_identifier == "sprachen" || $item_identifier == "sprachlerngeschichten" || $item_identifier == "lerntips" || $item_identifier == "sprachenchecklisten" || $item_identifier == "sprachlernplaene" || $item_identifier == "lernpartner_fremdeinschaetzung" || $item_identifier == "lernpartner_einschaetzung" || $item_identifier == "sprachenkulturenprojektidee" || $item_identifier == "sprachenundkulturen_items" || $item_identifier == "kulturbegegnung" || $item_identifier == "sprachenundkulturen")
            $navlinks[] = array('name' => get_string('sprachenbiografie', 'block_desp'), 'link' => 'sprachenbiografie.php?courseid='.$COURSE->id, 'type' => 'misc');
				
		if($item_identifier == "sprachlerngeschichte_familie" || $item_identifier == "sprachlerngeschichte_bisher" || $item_identifier == "sprachlerngeschichte_schule") {
			$navlinks[] = array('name' => get_string('sprachenbiografie', 'block_desp'), 'link' => 'sprachenbiografie.php?courseid='.$COURSE->id, 'type' => 'misc');
			$navlinks[] = array('name' => get_string('sprachlerngeschichte', 'block_desp'), 'link' => 'sprachlerngeschichten.php?courseid='.$COURSE->id, 'type' => 'misc');
		}
		if($item_identifier == "sprachlernplan") {
			$navlinks[] = array('name' => get_string('sprachenbiografie', 'block_desp'), 'link' => 'sprachenbiografie.php?courseid='.$COURSE->id, 'type' => 'misc');
			$navlinks[] = array('name' => get_string('sprachlernplaene', 'block_desp'), 'link' => 'sprachlernplaene.php?courseid='.$COURSE->id, 'type' => 'misc');
		}
		if($item_identifier == "sprachencheckliste") {
			$navlinks[] = array('name' => get_string('sprachenbiografie', 'block_desp'), 'link' => 'sprachenbiografie.php?courseid='.$COURSE->id, 'type' => 'misc');
			$navlinks[] = array('name' => get_string('sprachenchecklisten', 'block_desp'), 'link' => 'sprachenchecklisten.php?courseid='.$COURSE->id, 'type' => 'misc');
		}
		if($item_identifier == "miteinanderleben" || $item_identifier == "kleidungundwohnen" || $item_identifier == "schuleundfreizeit" || $item_identifier == "regelnundgesetze" || $item_identifier == "arbeitenundoeffentlichesleben" || $item_identifier == "wirerforschensprachenundschriften") {
			$navlinks[] = array('name' => get_string('sprachenbiografie', 'block_desp'), 'link' => 'sprachenbiografie.php?courseid='.$COURSE->id, 'type' => 'misc');
			$navlinks[] = array('name' => get_string("sprachenundkulturen_items", "block_desp"), 'link' => 'sprachenundkulturen_items.php?courseid='.$COURSE->id, 'type' => 'misc');
		}
        $navlinks[] = array('name' => $item_name, 'link' => null, 'type' => 'misc');

        $navigation = build_navigation($navlinks);
        $headertmp=print_header_simple($item_name, '', $navigation, "", "", true,'&nbsp;','',false,'',true);
        if ($closehead==false){
	        $headertmp=str_replace("</head>","",$headertmp);
	        $headertmp=str_replace("<body>","",$headertmp);
	      }
	      if ($printit==true)  echo $headertmp;
	      else return $headertmp;
}

function block_desp_get_nivaeu($skill, $niveau) {
	echo "$skill, $nivaeu"; exit;
}

function block_desp_get_data() {
	die ("block_desp_get_data outdated");
	require dirname(__FILE__) . '/SimpleXMLElement.php';

	$root = Pro_SimpleXMLElement::createRoot('desp');

	$filename = 'xml/desp_data.xml';
	$xml = Pro_SimpleXMLElement::loadFile($filename);

	$entriesByTable = array();

	foreach ($xml->table as $entry) {
		$name = $entry->attr('name');
		
		if (!isset($entriesByTable[$name])) $entriesByTable[$name] = array();
		
		$entriesByTable[$name][] = $entry;
	}

	foreach ($entriesByTable as &$entries) {
		usort($entries, create_function('$a,$b', "return ((int)\$a->sorting) > ((int)\$b->sorting);")); 
	}
	unset($entries);

	$desciptorTopic = array(); 	
	foreach ($entriesByTable['block_exabcompdescrtopic_mm'] as $entry) {
		//if (isset($desciptorTopic[$entry->val('descrid')])) { echo ('doppelte zuordnung descriptor: '.$desciptorTopic[$entry->val('descrid')].' '.print_r($entry, true)); }
		// test ignore 85
		if ($entry->val('topicid') == 85) continue;
		$desciptorTopic[$entry->val('descrid')] = $entry->val('topicid');
	}
	//print_r($desciptorTopic); exit;

	usort($entriesByTable['block_exabcomptopics'], create_function('$a,$b', "return strnatcmp(\$a->title, \$b->title);")); 
	usort($entriesByTable['block_exabcompdescriptors'], create_function('$a,$b', "return strnatcmp(\$a->title, \$b->title);")); 
	
	foreach ($entriesByTable['block_exabcompsubjects'] as $oldSubject) {
		if (!in_array($oldSubject->val('uid'), array(10,11,3))) continue;
		
		$subject = $root->addChild('subject');
		$subject->setAttributes(array(
			'uid' => $oldSubject->val('uid'),
			'title' => $oldSubject->val('title'),
			'stid' => $oldSubject->val('stid'),
			'sorting' => $oldSubject->val('sorting')
		));
		// $subject->title = $oldSubject->val('title');
		
		if (empty($entriesByTable['block_exabcomptopics'])) continue;
		
		foreach ($entriesByTable['block_exabcomptopics'] as $oldTopic) {
			if ($oldTopic->val('subjid') != $oldSubject->val('uid')) continue;
			
			preg_match('!^[a-z0-9\.]+!i', $oldTopic->val('title'), $matches);
			
			$topic = $subject->addChild('topic');
			$topic->setAttributes(array(
				'uid' => $oldTopic->val('uid'),
				'niveau' => $matches[0],
				'title' => $oldTopic->val('title'),
				'subjid' => $oldTopic->val('subjid'),
				'sorting' => $oldTopic->val('sorting')
			));
			//$topic->title = $oldTopic->val('title');

			foreach ($entriesByTable['block_exabcompdescriptors'] as $oldDescriptor) {
				if (!isset($desciptorTopic[$oldDescriptor->val('uid')]) || $oldTopic->val('uid') != $desciptorTopic[$oldDescriptor->val('uid')]) continue;
				
				preg_match('!^[a-z0-9\.]+!i', $oldDescriptor->val('title'), $matches);
				
				$descriptor = $topic->addChild('descriptor');
				$descriptor->setAttributes(array(
					'uid' => $oldDescriptor->val('uid'),
					'title' => $oldDescriptor->val('title'),
					'niveau' => $matches[0],
					'level' => substr_count($matches[0], '.'),
					'taxid' => $oldDescriptor->val('taxid'),
					'skillid' => $oldDescriptor->val('skillid'),
					'sorting' => $oldDescriptor->val('sorting')
				));
				//$descriptor->title = $oldDescriptor->val('title');

				$exampleIds = array();
				foreach ($entriesByTable['block_exabcompdescrexamp_mm'] as $descExamp) {
					if ($descExamp->val('descrid') == $oldDescriptor->val('uid')) { $exampleIds[] = $descExamp->val('exampid'); }
				}

				foreach ($entriesByTable['block_exabcompexamples'] as $oldExample) {
					if (!in_array($oldExample->val('uid'), $exampleIds)) continue;

					$example = $descriptor->addChild('example');
					$example->setAttributes(array(
						'uid' => $oldExample->val('uid'),
						'title' => $oldExample->val('title'),
						'task' => $oldExample->val('task'),
						'solution' => $oldDescriptor->val('solution'),
						'sorting' => $oldExample->val('sorting')
					));
				}
			}
		}
	}
	
	return $root;
}

function block_desp_build_comp_tree($lang=0) {
    global $DB;
    $sql = "SELECT e.id,s.title as skill, s.id as skillid, d.title, d.id as descrid, d.parent_id, e.title as example, e.task, e.externalurl, e.externalsolution, e.externaltask, e.solution, e.completefile, e.description, e.taxid, e.attachement,n.id as niveauid, n.title as niveau ";
    $sql.= "FROM {block_desp_descriptors} d INNER JOIN {block_desp_descrexamp_mm} mm ON d.id=mm.descrid INNER JOIN {block_desp_examples} e ON e.id=mm.exampid INNER JOIN {block_desp_skills} s ON s.id=d.skillid INNER JOIN {block_desp_niveaus} n ON n.id=d.niveauid";
    if ($lang!=0){
    		$sql.=" WHERE e.lang=".$lang;
    }
    $sql.= " GROUP BY e.id";
    $sql.= " ORDER BY s.sorting,n.sorting, d.sorting ";
    


   $examples = $DB->get_records_sql($sql);

    $tree='<form name="treeform"><ul id="comptree" class="treeview">';
    $skill="";
    $niveau="";
    $descriptor="";
    $newskill=true;
    $newniveau=true;
    $newdesc=true;
    $index=0;

    foreach($examples as $example) {
	
		/*if($example->parent_id != 0) {
			$sql = "SELECT s.title as skill, n.title as niveau FROM {block_desp_descriptors} d, {block_desp_skills} s, {block_desp_niveaus} n WHERE s.id = d.skillid AND n.id = d.niveauid AND d.id = ".$example->parent_id;
			$parent_descr = $DB->get_record_sql($sql);
			
			if($parent_descr) {
				$example->skill = $parent_descr->skill;
				$example->niveau = $parent_descr->niveau;
			}
		}*/
		if($example->parent_id == 0) {
			$child_descrs = $DB->get_records('block_desp_descriptors',array("parent_id"=>$example->descrid));
			if($child_descrs) {
				foreach($child_descrs as $child_descr) {
					$example->title .= ', '.$child_descr->title;
				}
			}
		}
		else {
			$parent_descr = $DB->get_record('block_desp_descriptors',array("id"=>$example->parent_id));
			if($parent_descr)
				$example->title = $parent_descr->title . ' ' . $example->title;
		}
        if($example->skill != $skill) {
            $skill = $example->skill;
            if(!$newskill)$tree.='</ul></ul></li></ul></li>';
            $tree.='<li>'.$skill;
            $tree.='<ul>';
            $newskill=false;
            $newniveau=true;
			$niveau="";
        }
        if($example->niveau != $niveau) {
            $niveau = $example->niveau;
            if(!$newniveau) $tree.='</ul></li></ul>';
            $tree.='<li>'.$niveau;
            $tree.='<ul>';
            $newniveau=false;
            $newdesc=true;
        }
        if($example->title != $descriptor) {
            $descriptor = $example->title;
            if(!$newdesc) $tree.='</ul></li>';
            $tree.='<li>'.$descriptor;
            $tree.='<ul>';
            $newdesc=false;
        }
        $text=$example->description;
        $text = str_replace("\"","",$text);
        $text = str_replace("\'","",$text);
        $text = str_replace("\n"," ",$text);
        $text = str_replace("\r"," ",$text);
        $text = str_replace(":","\:",$text);
        if($text)
            $tree.='<li><a onmouseover="Tip(\'' . $text . '\')" onmouseout="UnTip()">'.$example->example.'</a>';
        else
            $tree.='<li>'.$example->example;
        $tree.=block_desp_get_exampleicon($example);
        $tree.='</li>';
        $index++;
    }
    $tree.='</ul></form>';

    return $tree;
}
function block_desp_get_exampleicon($example) {
    global $DB, $CFG;
    $icon="";
    if($example->task) {
        $img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/pdf.gif" height="16" width="23" alt="Aufgabenstellung" />';
        $icon = '<a target="_blank" href="' . $example->task . '" onmouseover="Tip(\''.get_string("aufgabenstellung", "block_desp").'\')" onmouseout="UnTip()">'.$img.'</a>';
    } if($example->solution) {
        $img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/pdf_solution.gif" height="16" width="23" alt="'.get_string("assigned_example", "block_desp").'" />';
        $icon .= '<a target="_blank" href="' . $example->solution . '" onmouseover="Tip(\''.get_string("solution", "block_desp").'\')" onmouseout="UnTip()">'.$img.'</a>';
    }
    if($example->attachement) {
        $img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/attach_2.png" height="16" width="23" alt="'.get_string("aufgabenstellung", "block_desp").'" />';
        $icon .= '<a target="_blank" href="' . $example->attachement . '" onmouseover="Tip(\''.get_string("anhang", "block_desp").'\')" onmouseout="UnTip()">'.$img.'</a>';
    }if($example->externaltask) {
        $img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/link.png" height="16" width="23" alt="'.get_string("aufgabenstellung", "block_desp").'" />';
        $icon .= '<a target="_blank" href="' . $example->externaltask . '" onmouseover="Tip(\''.get_string("externe_aufgabenstellung", "block_desp").'\')" onmouseout="UnTip()">'.$img.'</a>';
    }
	if($example->externalurl) {
        $img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/link.png" height="16" width="23" alt="'.get_string("assigned_example", "block_desp").'" />';
        $icon .= '<a target="_blank" href="' . $example->externalurl . '" onmouseover="Tip(\''.get_string("externe_aufgabenstellung", "block_desp").'\')" onmouseout="UnTip()">'.$img.'</a>';
    }
    if($example->completefile) {
        $img = '<img src="' . $CFG->wwwroot . '/blocks/desp/images/folder.png" height="16" width="23" alt="'.get_string("assigned_example", "block_desp").'" />';
        $icon .= '<a target="_blank" href="' . $example->completefile . '" onmouseover="Tip(\''.get_string("gesamtbeispiel", "block_desp").'\')" onmouseout="UnTip()">'.$img.'</a>';
    }
    return $icon;
}
function kuerzename($wert,$lenge){
	if ($wert=="") return $wert;
	else{
		if (mb_strlen($wert,"utf-8")<=$lenge) return $wert;
		else{
			return (mb_substr($wert,0,$lenge,"utf-8").".");
		}
		
	}
}
function block_desp_get_bottom_text_header($skid,$nid) {
	global $DB;
	
	$skill = $DB->get_record('block_desp_skills',array("id"=>$skid));
	$niveau = $DB->get_record('block_desp_niveaus',array("id"=>$nid));
	return get_string('forskill','block_desp').get_string(strtolower(str_replace(' ', '', str_replace('ä', 'ae', str_replace('ö', 'oe', $skill->title)))), 'block_desp').get_string('giltaufniveau', 'block_desp').$niveau->title.":";
}
function block_desp_get_bottom_text($skid,$nid) {
	global $DB;
	
	if($skid == 1 && $nid == 1)
		echo get_string('a1hoerencheckliste', 'block_desp');
	if($skid == 1 && $nid == 2)
		echo get_string('a2hoerencheckliste', 'block_desp');
	if($skid == 1 && $nid == 3)
		echo get_string('b1hoerencheckliste', 'block_desp');
	
	if($skid == 2 && $nid == 1)
		echo get_string('a1schreibencheckliste', 'block_desp');
	if($skid == 2 && $nid == 2)
		echo get_string('a2schreibencheckliste', 'block_desp');
	if($skid == 2 && $nid == 3)
		echo get_string('b1schreibencheckliste', 'block_desp');	
	
	if($skid == 3 && $nid == 1)
		echo get_string('a1lesencheckliste', 'block_desp');
	if($skid == 3 && $nid == 2)
		echo get_string('a2lesencheckliste', 'block_desp');
	if($skid == 3 && $nid == 3)
		echo get_string('b1lesencheckliste', 'block_desp');
		
	if($skid == 4 && $nid == 1)
		echo get_string('a1sprechencheckliste', 'block_desp');
	if($skid == 4 && $nid == 2)
		echo get_string('a2sprechencheckliste', 'block_desp');
	if($skid == 4 && $nid == 3)
		echo get_string('b1sprechencheckliste', 'block_desp');
			
	if($skid == 5 && $nid == 1)
		echo get_string('a1zusammencheckliste', 'block_desp');
	if($skid == 5 && $nid == 2)
		echo get_string('a2zusammencheckliste', 'block_desp');
	if($skid == 5 && $nid == 3)
		echo get_string('b1zusammencheckliste', 'block_desp');
}
