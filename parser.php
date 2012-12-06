<?php

global $DB,$COURSE;
require_once dirname(__FILE__) . '/inc.php';

$data = optional_param('json','0',PARAM_ALPHANUM);
$DB->insert_record('block_exabcompskills',array("title"=>$data));

?>
