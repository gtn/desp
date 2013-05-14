 <?php
function get_skilltitle($skill){

	try {
    $ret=get_string(strtolower(str_replace(' ', '', str_replace('ä', 'ae', str_replace('ö', 'oe', $skill)))), 'block_desp');
	} catch (Exception $e) {
    $ret=$skill;
	}
	return $ret;
}
?>