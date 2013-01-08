<?php
global $DB, $COURSE;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div.php';
require_once dirname(__FILE__) . '/lib/div_lernplan.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);
$langid = optional_param('langid', 1, PARAM_INT);
$skid = optional_param('skid', 1, PARAM_INT);
require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/sprachlernplan.php';
$PAGE->set_url($url);

$hdrtmp=block_desp_print_header("sprachlernplan",true,false);
$hdrers='<link rel="stylesheet" type="text/css" media="all"
      href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/ui-darkness/jquery-ui.css"/>
<script type="text/javascript"
        src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js">
</script>

<script type="text/javascript"
        src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js">
</script>
'."
<script type='text/javascript'>
    $(function($) {

	$.datepicker.regional['de_AT'] = {
		monthNames: ['Jänner','Februar','März','April','Mai','Juni',
		'Juli','August','September','Oktober','November','Dezember'],
		monthNamesShort: ['Jän','Feb','Mär','Apr','Mai','Jun',
		'Jul','Aug','Sep','Okt','Nov','Dez'],
		dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
		dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
		dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
		dateFormat: 'dd.mm.yyyy', firstDay: 1,
		prevText: '&#x3c;zurück', prevStatus: 'letzten Monat zeigen',
		prevJumpText: '&#x3c;&#x3c;', prevJumpStatus: '',
		nextText: 'Vor&#x3e;', nextStatus: 'nächsten Monat zeigen',
		nextJumpText: '&#x3e;&#x3e;', nextJumpStatus: '',
		currentText: 'heute', currentStatus: '',
		todayText: 'heute', todayStatus: '',
		clearText: 'löschen', clearStatus: 'aktuelles Datum löschen',
		closeText: 'schließen', closeStatus: 'ohne Änderungen schließen',
		yearStatus: 'anderes Jahr anzeigen', monthStatus: 'anderen Monat anzeigen',
		weekText: 'Wo', weekStatus: 'Woche des Monats',
		dayStatus: 'Wähle D, M d', defaultStatus: 'Wähle ein Datum',
		isRTL: false
	};
	$.datepicker.setDefaults($.datepicker.regional['de_AT']);

	$('.date').datepicker({ dateFormat: 'dd.mm.yy' });
    });
</script>";
$hdrtmp=str_replace("</head>",$hdrers."</head>",$hdrtmp);
echo $hdrtmp;

/*echo "<pre>";
print_r($_POST);
echo "</pre>";*/

$possibleLernpartner = get_enrolled_users(get_context_instance(CONTEXT_COURSE, $COURSE->id));
// no sharing to myself
unset($possibleLernpartner[$USER->id]);

$delete_id = optional_param('did', null, PARAM_SEQUENCE);
if (!empty($delete_id)){
	block_desp_splp_deletedata($delete_id);
}

$data = optional_param('data', null, PARAM_TEXT);
if ($data){ 
	block_desp_splp_savedata();
}

//print_r($USER);
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
		    var i=tb.getElementsByTagName("tr").length;
		    

		    var newTr = document.createElement('tr');
		    var td = document.createElement('td');
		    td.innerHTML='<textarea rows="3" class="text sprachlernplantabth1input" name="title[]"></textarea><input type="hidden" name="id[]" value="-1" />';
				newTr.appendChild(td);
				
				td = document.createElement('td');
				td.className = "tddate";
				td.innerHTML='<input name="immer_wieder[' + i + ']" type="checkbox" value="1" />';
				newTr.appendChild(td);
				
				td = document.createElement('td');
				td.className = "tddate";

				td.innerHTML='<input type="text" class="date" name="starttime[]" maxlength="30" />';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.className = "tddate";
				td.innerHTML='<input type="text" class="date" name="endtime[]" maxlength="30" class="date" />';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.className = "tddate";
				td.innerHTML='<input type="text" class="date" name="donetime[]" maxlength="30" class="date" />';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.className = "tdpartner";
				td.innerHTML = '<select size="1" name="lernpartnerid[]">'
                        + '    <option value=""><?php echo get_string('keinlernpartner', 'block_desp');?></option>'
                        + '    <option value="">----------------</option>'
						+ '<?php foreach ($possibleLernpartner as $user) {
								echo '<option value="'.$user->id.'"';
								if(@$descriptor->lernpartnerid == $user->id) echo ' selected="selected"';
								echo '>'.kuerzename($user->lastname,12).' '.kuerzename($user->firstname,1).'</option>';
							} ?>'
                        + '</select>';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='&nbsp;';
				newTr.appendChild(td);
				//newTr.innerHTML=Trcont;
		    tb.appendChild(newTr);
		    //$(function();
        $( ".date" ).datepicker({ dateFormat: 'dd.mm.yy' });

		}
		// ]]>
</script>
<div id="desp">
<div id="page_margins">
    <div id="content">


        
            <div class="topinput"><?php echo block_desp_get_lang_title($langid) ?></div>
            <div class="topinput"><?php 
            	echo $USER->firstname." ";
            	echo $USER->lastname;
            	?></div>
        

        <br /><br />

        <form id="formular" method="post" action="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlernplan.php?courseid=<?php echo $courseid ?>">
						
            <table class="tableform3 sprachlernplantab" id="params">
                
                <thead>
                <tr>
                    <th rowspan="2"><?php echo get_string('meineplaenefuer','block_desp'); ?></th>
					<th class="slp"><?php echo get_string('dastueichimmerwieder','block_desp'); ?></th>
                    <th colspan="2" class="slp"><?php echo get_string('dasnehmeichmirvor','block_desp'); ?></th>
                    <th class="slp"><?php echo get_string('erledigtam','block_desp'); ?></th>
                    <th class="slp"><?php echo get_string('lernpartnerin','block_desp'); ?><input name="langid" value="<?php echo $langid; ?>" type="hidden" />
                    	
						<input name="skid" value="<?php echo $skid; ?>" type="hidden" />
						<input name="data" value="gesendet" type="hidden" /></th>
						<th class="slp"></th>
                </tr>
                <tr>
					<th class="slp"></th>
                    <th class="slp"><?php echo get_string('am','block_desp'); ?></th>
                    <th class="slp"><?php echo get_string('bis','block_desp'); ?></th>
                    <th class="slp"></th>
                    <th class="slp"></th>
                     <th class="slp"></th>
                </tr>
                <tr>
        			<th colspan="7"><h1 class="h1back h1lesen" style="padding-left: 50px; height:33px; background: #fdd68e url('images/ov_skill<?php echo $skid ?>.gif') no-repeat left top;"><?php echo get_string(strtolower(str_replace(' ', '', str_replace('ä', 'ae', str_replace('ö', 'oe', block_desp_get_skill_title($skid))))), 'block_desp'); ?></h1></th>                	
                </tr>
              </thead>
              <tbody>
              	<?php
							if(!block_desp_slp_check_lpitems($langid,$skid))
								block_desp_slp_import_lpitems($langid, $skid);
							
							echo block_desp_slp_getTrRows($langid, $skid, $courseid, $possibleLernpartner); 
							//echo block_desp_slg_getEmptyRow();
						?>
              </tbody>


            </table>
            <div>
<input type="submit" id="save-button" value="<?php echo get_string('save', 'block_desp');?>" />
<input type="button" id="add-param-button" value="<?php echo get_string('weiterenlernplanhinzu', 'block_desp');?>" onclick="addRow('params');" />
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