<?php

global $DB,$COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div_sprachlerngeschichten.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_SEQUENCE);

require_login($courseid);

$course = $DB->get_record('course',array("id"=>$courseid));
$url = '/blocks/desp/sprachlerngeschichte.php';
$PAGE->set_url($url);
block_desp_print_header("sprachenkulturenprojektidee");
//print_r($_POST);
$data = optional_param('data', null, PARAM_RAW);

$delete_id = optional_param('did', null, PARAM_SEQUENCE);
if (!empty($delete_id)){
	block_desp_splg_deletedata($delete_id);
}

if ($data){ 
block_desp_splg_savedata(1);
}

?>


	<script type="text/javascript">
		// <![CDATA[
		/**
		 * Appends a row to the table
		 */
		 
		function addRow(tableId)
		{
		    var tb    = document.getElementById(tableId)
		                        .getElementsByTagName('tbody')[0];
		    var newTr = document.createElement('tr');
		    var td = document.createElement('td');
		    td.innerHTML='<?php echo block_desp_createLanguageSelector(-1); ?><input type="hidden" name="id[]" value="-1" />';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<input type="text" class="value" name="partner[]" />';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<input type="text" class="value" name="reason[]" />';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<input type="text" class="value" name="period[]" />';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='&nbsp;';
				newTr.appendChild(td);
				//newTr.innerHTML=Trcont;
		    tb.appendChild(newTr);
		}
		// ]]>
</script>
<div id="desp">
	<div id="page_margins">
		<div id="content">

			<h1><?php echo get_string('sprachenkulturen_projektideen','block_desp'); ?></h1>
			
			
		
		
			  <div id="messageboxslp2" style="background: url('images/message_slp2.gif') no-repeat left top;">
            <div id="messagetxtslp2">
				
				<?php echo get_string('menschenverstehen','block_desp'); ?><span class="mehrtext"><?php echo get_string('ausklappen','block_desp'); ?></span>
                <div class="messagetxthide">
                <br />
<ul>
<?php echo get_string('menschenverstehenliste','block_desp'); ?>
</ul>
<?php echo get_string('interkulturelleforschungsprojekte','block_desp'); ?>
                </div>
                 
            </div>
        </div>
		
			<br /><br />
			<h2><?php echo get_string('allemeinesprachen','block_desp'); ?></h2>

<?php echo get_string('allemeinesprachen_inhalt','block_desp'); ?>


			<br /><br />
			<h2><?php echo get_string('festeundfeiern','block_desp'); ?></h2>

<?php echo get_string('festeundfeiern_inhalt','block_desp'); ?>


<br /><br />
<p>
	<?php echo get_string('weitere','block_desp'); ?><a href="http://www.oesz.at/download/esp_plattform/Schuelerbereich/A10_Projekte_Sprachen_Schriften.pdf"><span class="mehrtext"><?php echo get_string('vorschlaegefuerforschungsprojekte','block_desp'); ?></span></a><?php echo get_string('und','block_desp'); ?><a href="a11.pdf"><span class="mehrtext"><?php echo get_string('voxmiprojekte','block_desp'); ?></span></a><?php echo get_string('findestaufplattform','block_desp'); ?>
</p>


		</div>
	</div>
</div>
<?php
	include_once ("despfooter.php");
?>
<?php
echo $OUTPUT->footer($course);
?>