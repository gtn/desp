<?php

global $DB,$COURSE,$CFG;
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

$course = $DB->get_record('course',array("id"=>$courseid));
$url = '/blocks/desp/sprachlerngeschichte.php';
$PAGE->set_url($url);

$hdrtmp=block_desp_print_header("sprachlerngeschichte_familie",true,false);
$hdrers='<link href="http://fonts.googleapis.com/css?family=Coming+Soon" rel="stylesheet" type="text/css" />';
$hdrtmp=str_replace("</head>",$hdrers."</head>",$hdrtmp);
echo $hdrtmp;
//print_r($_POST);
$data = optional_param('data', null, PARAM_RAW);

$delete_id = optional_param('did', null, PARAM_SEQUENCE);
if (!empty($delete_id)){
	block_desp_splg_deletedata($delete_id);
}

if ($data){ 
block_desp_splg_savedata(1);
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
		    td.innerHTML='<?php echo block_desp_createLanguageSelector(-1,1); ?><input type="hidden" name="id[]" value="-1" />';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<textarea name="partner[]" cols="50" rows="1"></textarea>';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<textarea name="reason[]" cols="50" rows="1"></textarea>';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<textarea name="period[]" cols="50" rows="1"></textarea>';
				
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<?php echo str_replace("'",'"',$exaportitem); ?>';
				
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

			<h1><?php echo get_string('spracheninfamilieundumgebung','block_desp'); ?></h1>
			
			
		
			<div id="messagebox" style="background: url('images/message.gif') no-repeat left top;">
					<div id="messagetxt">
						<?php echo get_string('spracheninfamilieundumgebung_inhalt','block_desp'); ?>
					</div>
			</div>
		
			<?php
				$rows = block_desp_slg_getTrRows(1,"partner,reason,period,dossier",$courseid,$exaport);
				if(!$rows) {
			?>
		
			<br /><br />
			<p><?php echo get_string('beispiel','block_desp'); ?></p>
			<br />
			<table class="tableform1">
				<tr>
					<?php echo get_string('beispiel_liste','block_desp'); ?>
				</tr>
				<tr>
					<td class="handwriting">Hrvatski <sup>1</sup></td>
					<td class="handwriting">s mamom/tatom</td>
					<td class="handwriting">kad sam s njom</td>
					<td class="handwriting">čuda krat</td>
					<td class="handwriting"><select class="selbox_exaportitems" name="dossier[]">
<option value="837">Canzone.doc</option>
<option value="876">Concerto.jpg</option>

</select></td>
				</tr>
				<tr>
					<?php echo get_string('beispiel_liste2','block_desp'); ?>
					 <td class="handwriting"><select class="selbox_exaportitems" name="dossier[]">
<option value="837">Canzone.doc</option>
<option value="876">Concerto.jpg</option>

</select></td>
				</tr>
			</table>
			
			<br />
			<p class="txtsmall"><?php echo get_string('uebersetzung_beispiel','block_desp'); ?></p>
			
			<?php
				}
			?>
			<div id="params-container">
				<form method="post" action="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichte.php?courseid=<?php echo $courseid ?>">
					
					
				
				
					<table class="tableform1" id="params">
						<thead>
						<tr>
							<th class="name"><?php echo get_string('welchesprachedialekt','block_desp'); ?></th>
							<th class="name"><?php echo get_string('mitwem','block_desp'); ?></th>
							<th class="name"><?php echo get_string('beiwelchergelegenheit','block_desp'); ?></th>
							<th class="name"><?php echo get_string('wiehaeufig','block_desp'); ?><input type="hidden" name="data" value="gesendet" /></th>
							<th class="tb132"><?php echo get_string('dossiereintrag','block_desp'); ?><input type="hidden" name="data" value="gesendet" /></th>
							<th class="name"> </th>
						</tr>
					</thead>
						<tbody>
						<?php
							echo $rows;
							//echo block_desp_slg_getEmptyRow();
						?>
					</tbody>
					</table>
				
					<div>
					<input type="button" id="add-param-button" value="<?php echo get_string('weiteresprachehinzufuegen', 'block_desp')?>" onclick="addRow('params');" />
					<input type="submit" id="save-button" value="<?php echo get_string('save', 'block_desp')?>" /><br/><br/>
					<a href="sprachen.php?courseid=<?php echo $COURSE->id; ?>&amp;return=sprachlerngeschichte"><?php echo get_string('neuesprachehinzufuegen','block_desp'); ?></a><br/>
					<a href="mailto:portfolio@oesz.at?subject=Sprache%20in%20die%20Auswahlliste%20aufnehmen"><?php echo get_string('spracheaufnehmen','block_desp'); ?></a>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>
	<?php
	include_once ("despfooter.php");
?>
<?php
echo $OUTPUT->footer($course);
?>