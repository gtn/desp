<?php

function block_desp_xml_insert_edulevel($value) {
    global $DB;
    $sql="INSERT INTO {block_desp_edulevels} (id,sorting,title) VALUES(".$value->uid.",".$value->sorting.",'".$value->title."')";
    $DB->Execute($sql);
    //$DB->insert_record('block_desp_edulevels', $value);
}

function block_desp_xml_insert_schooltyp($value) {
    global $DB;
    $sql="INSERT INTO {block_desp_schooltypes} (id,sorting,title,elid) VALUES(".$value->uid.",".$value->sorting.",'".$value->title."',".$value->elid.")";
    $DB->Execute($sql);
    //$DB->insert_record('block_desp_schooltypes', $value);
}

function block_desp_xml_insert_subject($value) {
//    global $DB;
//    $DB->insert_record('block_desp_subjects', $value);

    global $DB;
    /*
     * ID aus XML mit sourceID der Datenbank vergleichen.
     * Falls gefunden, update
     * Falls nicht, insert
     *
     */
    $subject = $DB->get_record('block_desp_subjects', array("sourceid" => $value->uid));
    if ($subject) {
        //update
        $subject->title = $value->title;
        $subject->stid = $value->stid;

        $DB->update_record('block_desp_subjects', $subject);
    } else {
        //insert
        //$value->sourceid = $value->uid;
        //$DB->insert_record('block_desp_subjects', $value);
        $sql="INSERT INTO {block_desp_subjects} (id,sorting,title,stid,sourceid) VALUES(".$value->uid.",".$value->sorting.",'".$value->title."',".$value->stid.",".$value->uid.")";
    		$DB->Execute($sql);
    }
}

function block_desp_xml_insert_skill($value) {
    global $DB;
    $sql="INSERT INTO {block_desp_skills} (id,sorting,title) VALUES(".$value->uid.",".$value->sorting.",'".$value->title."')";
   	$DB->Execute($sql);
    //$DB->insert_record('block_desp_skills', $value);
}

function block_desp_xml_insert_niveaus($value) {
    global $DB;
    $sql="INSERT INTO {block_desp_niveaus} (id,title,sorting,parent_niveau) VALUES(".$value->uid.",'".$value->title."',".$value->sorting.",".$value->parent_niveau.")";
   	$DB->Execute($sql);
   	
    //$DB->insert_record('block_desp_niveaus', $value);
}
function block_desp_xml_insert_niveau_texte($value) {
    global $DB;
    $sql="INSERT INTO {block_desp_niveau_texte} (id,title,skillid,niveauid) VALUES(".$value->uid.",'".$value->title."',".$value->skillid.",".$value->niveauid.")";
   	$DB->Execute($sql);
    //$DB->insert_record('block_desp_niveau_texte', $value);
}

function block_desp_xml_insert_taxonomie($value) {
    global $DB;
    
	
	    /*
     * ID aus XML mit sourceID der Datenbank vergleichen.
     * Falls gefunden, update
     * Falls nicht, insert
     *
     */
     	$new_value = new stdClass();
			$new_value->uid = (int)$value->uid;
			$new_value->title = (string)$value->title;
			$new_value->sorting = (int)$value->sorting;
			$new_value->parent_tax = (int)$value->parent_tax;
			unset($value);
			$value = $new_value;
	
    $tax = $DB->get_record('block_desp_taxonomies', array("sourceid" => $value->uid));
    if ($tax) {
        //update
        $tax->title = $value->title;
				/*if($value->parent_tax != 0) {
				$parenttax = $DB->get_record('block_desp_taxonomies', array("sourceid" => $value->parent_tax));
		        $tax->parentid = $parenttax->id;
		
				}else
					$tax->parentid=0;
				*/
        $DB->update_record('block_desp_taxonomies', $tax);
    } else {
        //insert
        //$value->sourceid = $value->uid;
				//$value->parentid = $value->parent_tax;
				
				$sql="INSERT INTO {block_desp_taxonomies} (id,sorting,title,sourceid) VALUES(".$value->uid.",".$value->sorting.",'".$value->title."',".$value->uid.")";
    		$DB->Execute($sql);
        //$DB->insert_record('block_desp_taxonomies', $value);
    }
}

function block_desp_xml_insert_topic($value) {
    global $DB;
    /*
     * ID aus XML mit sourceID der Datenbank vergleichen.
     * Falls gefunden, update
     * Falls nicht, insert
     *
     */

    // Subject ID wird benötigt, durch sourceid holen
    	$new_value = new stdClass();
			$new_value->subjid = (int)$value->subjid;
			$new_value->uid = (int)$value->uid;
			$new_value->title = (string)$value->title;
			$new_value->sorting = (int)$value->sorting;
			$new_value->description = (string)$value->description;
			$new_value->sourceid = (int)$value->uid;
			unset($value);
			$value = $new_value;
    $subject = $DB->get_record('block_desp_subjects', array("sourceid"=> $value->subjid));

    $topic = $DB->get_record('block_desp_topics', array("sourceid" => $value->uid));
    if ($topic) {
        //update
        $topic->title = $value->title;
        $topic->subjid = $subject->id;
				$topic->sorting = $value->sorting;
				$topic->description = $value->description;
        $DB->update_record('block_desp_topics', $topic);
    } else {
        //insert
        
        $value->subjid = $subject->id;
        $DB->insert_record('block_desp_topics', $value);
    }
}

function block_desp_xml_insert_descriptor($value) {
    global $DB;
    /*
     * ID aus XML mit sourceID der Datenbank vergleichen.
     * Falls gefunden, update
     * Falls nicht, insert
     *
     */
		$new_value = new stdClass();
		$new_value->uid = (int)$value->uid;
		$new_value->title = (string)$value->title;
		$new_value->niveauid = (int)$value->niveauid;
		$new_value->sorting = (int)$value->sorting;
		$new_value->skillid = (int)$value->skillid;
		$new_value->parent_id = (int)$value->parent_id;
		$new_value->sourceid = (int)$value->uid;
		unset($value);
		$value = $new_value;
    $desc = $DB->get_record('block_desp_descriptors', array("sourceid" => $value->uid));
    if ($desc) {
        //update
        $desc->title = (string)$value->title;
				$desc->skillid = (int)$value->skillid;
				$desc->niveauid = (int)$value->niveauid;
				$desc->sorting = (int)$value->sorting;
				$desc->parent_id = (int)$value->parent_id;
        $DB->update_record('block_desp_descriptors', $desc);
    } else {
        //insert
        
        $DB->insert_record('block_desp_descriptors', $value);
    }
}

function block_desp_xml_insert_example($value) {
    global $DB;
    /*
     * ID aus XML mit sourceID der Datenbank vergleichen.
     * Falls gefunden, update
     * Falls nicht, insert
     *
     */
	 
		$new_value = new stdClass();
		$new_value->uid = (int)$value->uid;
		$new_value->title = (string)$value->title;
		$new_value->task = (string)$value->task;
		$new_value->solution = (string)$value->solution;
		$new_value->attachement = (string)$value->attachement;
		$new_value->completefile = (string)$value->completefile;
		$new_value->description = (string)$value->description;
		$new_value->taxid = (int)$value->taxid;
		$new_value->timeframe = (string)$value->timeframe;
		$new_value->ressources = (string)$value->ressources;
		$new_value->tips = (string)$value->tips;
		$new_value->externalurl = (string)$value->externalurl;
		$new_value->externalsolution = (string)$value->externalsolution;
		$new_value->externaltask = (string)$value->externaltask;
		$new_value->sorting = (int)$value->sorting;
		$new_value->lang = (int)$value->lang;
		$new_value->sourceid = (int)$value->uid;
		unset($value);
		$value = $new_value;
    $DB->insert_record('block_desp_examples', $value);
}

function block_desp_xml_truncate($tablename) {
    global $DB;
    //echo "<br>".$tablename;
    $DB->delete_records($tablename);
}

function block_desp_xml_get_topics() {
    global $DB;
    return $DB->get_records('block_desp_topics');
}

function block_desp_xml_get_descriptors() {
    global $DB;
    return $DB->get_records('block_desp_descriptors');
}

function block_desp_xml_get_examples() {
    global $DB;
    return $DB->get_records('block_desp_examples');
}

function block_desp_xml_find_unused($values, $xml, $tablename) {
    global $DB;
    $founds = array();
    foreach ($values as $value) {
        $occur = false;
        foreach ($xml->table as $table) {
            $name = $table->attributes()->name;
            if ($name == $tablename) {
                if ($table->uid == $value->sourceid)
                    $occur = true;
            }
        }
        // if !occur && source == zentraler Server
        if (!$occur)
            $founds[] = $value->sourceid;
    }
    return $founds;
}

function block_desp_xml_delete_unused_topics($founds) {
    global $DB;
    foreach ($founds as $found) {
        $query = "SELECT * FROM {block_desp_topics} t, {block_desp_descrtopic_mm} dt WHERE dt.topicid = t.id and t.sourceid = " . $found;
        $occur = $DB->get_records_sql($query);
        if (!$occur)
            $DB->delete_records('block_desp_topics', array("sourceid" => $found));
    }
}

function block_desp_xml_delete_unused_descriptors($founds) {
    global $DB;
    foreach ($founds as $found) {
        $query = "SELECT * FROM {block_desp_descriptors} d, {block_desp_descrtopic_mm} dt WHERE dt.descrid = d.id and d.sourceid = " . $found;
        $occur = $DB->get_records_sql($query);
        if (!$occur)
            $DB->delete_records('block_desp_descriptors', array("sourceid" => $found));
    }
}

function block_desp_xml_delete_unused_examples($founds) {
    global $DB;
    foreach ($founds as $found) {
        $query = "SELECT * FROM {block_desp_examples} e, {block_desp_descrexamp_mm} de WHERE de.exampid = e.id and e.sourceid = " . $found;
        $occur = $DB->get_records_sql($query);
        if (!$occur)
            $DB->delete_records('block_desp_examples', array("sourceid" => $found));
    }
}

function block_desp_xml_get_current_ids($table, $tablename) {
    global $DB;
    $value = array();
    $new_table=New stdClass();
    $new_table->topicid=(int)$table->topicid;
    $new_table->exampid=(int)$table->exampid;
    $new_table->descrid=(int)$table->descrid;
    unset($table);
			$table = $new_table;
    if ($tablename == "topic")
        $topic = $DB->get_record('block_desp_topics', array("sourceid" => $table->topicid), "id");
    else
        $example = $DB->get_record('block_desp_examples', array("sourceid" => $table->exampid), "id");

    $descr = $DB->get_record('block_desp_descriptors', array("sourceid" => $table->descrid), "id");

    if (!empty($topic) && !empty($descr)) {
        $value['topicid'] = $topic->id;
        $value['descrid'] = $descr->id;
        return $value;
    }
    if (!empty($example) && !empty($descr)) {
        $value['exampid'] = $example->id;
        $value['descrid'] = $descr->id;
        return $value;
    }
}

function block_desp_xml_delete_descrtopicmm() {
    global $DB;
    $query = "SELECT dt.id FROM {block_desp_topics} t, {block_desp_descriptors} d, {block_desp_descrtopic_mm} dt WHERE t.id=dt.topicid AND d.id=dt.descrid AND t.sourceid IS NOT NULL AND d.sourceid IS NOT NULL";
    $assigns = $DB->get_records_sql($query);

    foreach ($assigns as $assign) {
        $DB->delete_records('block_desp_descrtopic_mm', array("id" => $assign->id));
    }
}

function block_desp_xml_delete_descrexampmm() {
    global $DB;
    $query = "SELECT de.id FROM {block_desp_examples} e, {block_desp_descriptors} d, {block_desp_descrexamp_mm} de WHERE e.id=de.exampid AND d.id=de.descrid AND e.sourceid IS NOT NULL AND d.sourceid IS NOT NULL";
    $assigns = $DB->get_records_sql($query);

    foreach ($assigns as $assign) {
        $DB->delete_records('block_desp_descrexamp_mm', array("id" => $assign->id));
    }
}

function block_desp_xml_insert_descrexampmm($descrexamples) {
    global $DB;

    foreach ($descrexamples as $descrexample) {
        $data = new stdClass();

        $data->exampid = $descrexample['exampid'];
        $data->descrid = $descrexample['descrid'];
        $DB->insert_record('block_desp_descrexamp_mm', $data);
    }
}
function block_desp_xml_insert_descrtopicmm($descrtopics) {
    global $DB;

    foreach ($descrtopics as $descrtopic) {
        $data = new stdClass();

        $data->topicid = $descrtopic['topicid'];
        $data->descrid = $descrtopic['descrid'];
        $DB->insert_record('block_desp_descrtopic_mm', $data);
    }
}

function block_desp_xml_do_import($file = null) {
    $filename = 'xml/desp_data.xml';

    
		
    $edulevel = 0;
    $schooltyp = 0;
    $subject = 0;
    $topic = 0;
    $skill = 0;
    $niveau = 0;
    $niveau_texte = 0;
    $tax = 0;
	$examples = 0;
    $descrtopic = array();
    $descrexamp = array();

    if (file_exists($filename)) {
        $xml = simplexml_load_file($filename);
        if ($xml) {
            foreach ($xml->table as $table) {
                $name = $table->attributes()->name;

                if ($name == "block_desp_edulevels") {
                    if ($edulevel == 0) {
                       // block_desp_xml_truncate($table->attributes()->name);
                        $edulevel = 1;
                    }
                    //block_desp_xml_insert_edulevel($table);
                }
                if ($name == "block_desp_schooltypes") {
                    if ($schooltyp == 0) {
                        //block_desp_xml_truncate($name);
                        $schooltyp = 1;
                    }
                    //block_desp_xml_insert_schooltyp($table);
                }
                if ($name == "block_desp_subjects") {
                    if ($subject == 0) {
                        //block_desp_xml_truncate($name);
                        $subject = 1;
                    }
                    //block_desp_xml_insert_subject($table);
                }
                if ($name == "block_desp_skills") {
                    if ($skill == 0) {
                        block_desp_xml_truncate($name);
                        $skill = 1;
                    }
                    block_desp_xml_insert_skill($table);
                }
                if ($name == "block_desp_niveaus") {
                    if ($niveau == 0) {
                        block_desp_xml_truncate($name);
                        $niveau = 1;
                    }
                    block_desp_xml_insert_niveaus($table);
                }
                if ($name == "block_desp_niveau_texte") {
                    if ($niveau_texte == 0) {
                        block_desp_xml_truncate($name);
                        $niveau_texte = 1;
                    }
                    block_desp_xml_insert_niveau_texte($table);
                }
                if ($name == "block_desp_taxonomies") {

                    block_desp_xml_insert_taxonomie($table);
                }
                if ($name == "block_desp_topics") {
                   // block_desp_xml_insert_topic($table);
                }
                if ($name == "block_desp_descriptors") {
                    block_desp_xml_insert_descriptor($table);
                }
                if ($name == "block_desp_examples") {
					if ($examples == 0) {
                        block_desp_xml_truncate($name);
                        $examples = 1;
                    }
                    block_desp_xml_insert_example($table);
                }
                if ($name == "block_desp_descrtopic_mm") {
                    $descrtopicmm = block_desp_xml_get_current_ids($table, "topic");
                    if (!empty($descrtopicmm['descrid']) && !empty($descrtopicmm['topicid']))
                        $descrtopic[] = $descrtopicmm;
                }
                if ($name == "block_desp_descrexamp_mm") {
                    $descrexampmm = block_desp_xml_get_current_ids($table, "example");
                    if (!empty($descrexampmm['descrid']) && !empty($descrexampmm['exampid']))
                        $descrexamp[] = $descrexampmm;
                }
            }
            /*
            $topics = block_desp_xml_get_topics();
            $founds = block_desp_xml_find_unused($topics, $xml, "block_desp_topics");
            block_desp_xml_delete_unused_topics($founds);
						*/
            $descs = block_desp_xml_get_descriptors();
            $founds = block_desp_xml_find_unused($descs, $xml, "block_desp_descriptors");
            block_desp_xml_delete_unused_descriptors($founds);
						block_desp_xml_update_descriptor_parent_id();
            $examples = block_desp_xml_get_examples();
            $founds = block_desp_xml_find_unused($examples, $xml, "block_desp_examples");
            block_desp_xml_delete_unused_examples($founds);
					  /*
            block_desp_xml_delete_descrtopicmm();
            block_desp_xml_insert_descrtopicmm($descrtopic);
						*/
            block_desp_xml_delete_descrexampmm();
            block_desp_xml_insert_descrexampmm($descrexamp);
            
        }
        return true;
    }
    return false;
}
function block_desp_xml_update_descriptor_parent_id(){
	global $DB;
	$sql="SELECT * FROM {block_desp_descriptors} WHERE parent_id>0 AND source=1";
	$descriptors=$DB->get_records_sql($sql);
	foreach($descriptors as $descriptor){
		
		$id=block_desp_xml_update_descriptor_parent_id_getid($descriptor->parent_id);
		$data["id"]=$descriptor->id;
		$data["parent_id"]=$id;
		$DB->update_record('block_desp_descriptors', $data);
	}
}
function block_desp_xml_update_descriptor_parent_id_getid($id){
		global $DB;
	$rec=$DB->get_record('block_desp_descriptors',array("sourceid"=>$id,"source"=>"1"));
	if ($rec) {
		return $rec->id;
	}else{
		return 0;
	}
}

function block_desp_xml_check_import() {
    global $DB;
    $check = $DB->get_records('block_desp_descriptors');
    if ($check)
        return true;
    else
        return false;
}
function block_desp_import_languages($filename) {
	global $DB;
	
	if (file_exists($filename)) {
	        $xml = simplexml_load_file($filename);
                if($xml) {
                    foreach ($xml->table as $item) {
						$sql = "SELECT * FROM {block_desp_lang} WHERE ".$DB->sql_compare_text("name") . " = '".$item->name."'";
                        $lang = $DB->get_record_sql($sql);
						
						if(!$lang) {
							$item->userid=0;
							block_desp_insert_item($item,"block_desp_lang");
						}
                    }
                }
	}
}

function block_desp_check_table($tablename){
    global $DB;
    $check = $DB->get_records($tablename);
    if ($check)
        return true;
    else
        return false;
}
function block_desp_importtable($filename,$tablename){

	if (file_exists($filename)) {

	        $xml = simplexml_load_file($filename);
                if($xml) {
                    foreach ($xml->table as $item) {
                            block_desp_insert_item($item,$tablename);
                    }
                }
	}
}
function block_desp_insert_item($item,$tablename){
	global $DB;
	$DB->insert_record($tablename, $item);
}



?>
