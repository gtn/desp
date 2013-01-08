<?php
global $DB, $COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div_sprachlerngeschichten.php';
$courseid = optional_param('courseid', $COURSE->id, PARAM_SEQUENCE);
$all_tables = $DB->get_tables();
	if (in_array("block_exaportview", $all_tables)) {
		$exaport=true;

	}else{
		$exaport=false;

	}
require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/sprachencheckliste.php';
$PAGE->set_url($url);

$hdrtmp=block_desp_print_header("sprachlerngeschichte_bisher",true,false);
$hdrers='<link href="http://fonts.googleapis.com/css?family=Coming+Soon" rel="stylesheet" type="text/css" />';
$hdrtmp=str_replace("</head>",$hdrers."</head>",$hdrtmp);
echo $hdrtmp;

$data = optional_param('data', null, PARAM_RAW);
//print_r($_POST);

$delete_id = optional_param('did', null, PARAM_SEQUENCE);
if (!empty($delete_id)){
	block_desp_splg_deletedata($delete_id);
}

if ($data){ 
block_desp_splg_savedata(2);
}
$exaportitem=block_desp_pd_eportitems(0,$exaport);

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
		    td.className = "tdlang";
		    td.innerHTML='<?php echo block_desp_createLanguageSelector(-1,2); ?><input type="hidden" name="id[]" value="-1">';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<textarea name="partner[]" cols="50" rows="1"></textarea>';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<textarea name="period[]" cols="50" rows="1"></textarea>';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<textarea name="reason[]" cols="50" rows="1"></textarea>';
				
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<?php echo $exaportitem; ?>';
				
				newTr.appendChild(td);
				td = document.createElement('td');
				td.className = "tddelete";
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

         <h1><?php echo get_string('sprachendieichgelernthabe','block_desp'); ?></h1>

	<div id="messageboxslp1" style="background: url('images/message_sp1.gif') no-repeat left top;">
			<div id="messagetxtslp1">
                <?php echo get_string('sprachendieichgelernthabe_inhalt','block_desp'); ?>
            </div>
        </div>

        <div id="messageboxsg2" style="background: url('images/message_sp2.gif') no-repeat left top;">
            <div id="messagetxtsg2">
                <?php echo get_string('sprachendieichgelernthabe_inhalt2','block_desp'); ?>
            </div>
        </div>

		<?php
			$rows = block_desp_slg_getTrRows(2,"partner,period,reason,dossier",$courseid,$exaport);
			if(!$rows) {
		?>


        
        <p><?php echo get_string('beispiel','block_desp'); ?></p>
        <br />
        <table class="tableform3">
            <tr>
                <?php echo get_string('sprachendieichgelernthabe_tabelle','block_desp'); ?>
            </tr>
            <tr>
                </td><?php echo get_string('sprachendieichgelernthabe_tabelle2','block_desp'); ?>
                <td class="handwriting"><select class="selbox_exaportitems" name="dossier[]">
<option value="837">Canzone.doc</option>
<option value="876">Concerto.jpg</option>

</select></td>
            </tr>
        </table>

        <br /><br />

		<?php
			}
		?>

        <form method="post" action="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichte_bisher.php?courseid=<?php echo $courseid ?>">
					



            <table class="tableform3" id="params">
            	<thead>
                <tr>
                    <?php echo get_string('sprachendieichgelernthabe_tabelle3','block_desp'); ?>
               </tr>
              </thead>
              <tbody>
						<?php
							echo $rows; 
						?>
					</tbody>
					
                <!--tr>
                    <td><textarea cols="" rows="3" name="text" id="" class=""></textarea></td>
                    <td><textarea cols="" rows="3" name="text" id="" class=""></textarea></td>
                    <td><textarea cols="" rows="3" name="text" id="" class=""></textarea></td>
                    <td><textarea cols="" rows="3" name="text" id="" class=""></textarea></td>
                </tr-->
                

            </table>
            <div>
						<input type="button" id="add-param-button" value="<?php echo get_string('weiteresprachehinzufuegen', 'block_desp')?>" onclick="addRow('params');" />
						<input type="submit" id="save-button" value="<?php echo get_string('save', 'block_desp')?>" /><br/><br/>
						<a href="sprachen.php?courseid=<?php echo $COURSE->id; ?>&amp;return=sprachlerngeschichte_bisher"><?php echo get_string('neuesprachehinzufuegen','block_desp'); ?></a><br/>
					  <a href="mailto:portfolio@oesz.at?subject=Sprache%20in%20die%20Auswahlliste%20aufnehmen"><?php echo get_string('spracheaufnehmen','block_desp'); ?></a>

					</div>
        </form>


    </div>
</div>
</div>
<?php
	 include_once ("despfooter.php");
?>
<?php
echo $OUTPUT->footer($course);
?>