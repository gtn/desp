<?php
function xmldb_block_desp_upgrade($oldversion) {
    global $DB,$CFG;
    $dbman = $DB->get_manager();
		$result=true;
   

    /// Add a new column newcol to the mdl_question_myqtype
    if ($oldversion < 2012011800) {
    	
    	$table = new xmldb_table('block_desp_cultures_items');
    	$field_userid = new xmldb_field('userid');
			$field_userid->set_attributes(XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null, null); // [XMLDB_ENUM, null,] Moodle 2.x deprecated  

        // Conditionally launch add temporary fields
        if (!$dbman->field_exists($table, $field_userid)) {
            $dbman->add_field($table, $field_userid);
        }
        ////
        
	                
	      //upgrade_block_savepoint(true, 2009011700, 'block_desp');
    }

    if ($oldversion < 2012070301) {
    	
    	$table = new xmldb_table('block_desp_examples');
    	$field_userid = new xmldb_field('lang');
			$field_userid->set_attributes(XMLDB_TYPE_INTEGER, '20', XMLDB_UNSIGNED, null, null, null, null); // [XMLDB_ENUM, null,] Moodle 2.x deprecated  

        // Conditionally launch add temporary fields
        if (!$dbman->field_exists($table, $field_userid)) {
            $dbman->add_field($table, $field_userid);
        }
        ////
        
	                
	      //upgrade_block_savepoint(true, 2009011700, 'block_desp');
    }
    if ($oldversion < 2012160103) {
    	
    	$table = new xmldb_table('block_desp_lang');
    	$field = new xmldb_field('en');
			$field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null); // [XMLDB_ENUM, null,] Moodle 2.x deprec
        // Conditionally launch add temporary fields
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
      $field = new xmldb_field('de');
			$field->set_attributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null); // [XMLDB_ENUM, null,] Moodle 2.x deprec
        // Conditionally launch add temporary fields
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $sql = "UPDATE {block_desp_lang} SET de=name";
				
        try {
        	$DB->execute($sql);
				$field = new xmldb_field('name');
				$dbman->drop_field($table, $field);
        } catch (Exception $e) {
        	
        }
				
	      //upgrade_block_savepoint(true, 2009011700, 'block_desp');
    }
    
    return $result;
}

?>