<?php
global $DB, $COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);

require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/sprachenundkulturen.php';
$PAGE->set_url($url);

$data = optional_param('data', null, PARAM_RAW);
$recid = optional_param('recid', -1, PARAM_RAW);

$delete_id = optional_param('did', null, PARAM_SEQUENCE);
if (!empty($delete_id)){
	block_desp_delbegegnung($delete_id);
}

function block_desp_delbegegnung($id){
	global $DB,$USER;
	$DB->delete_records('block_desp_begegnung',array("userid"=>$USER->id, "id"=>$id));
}

if ($data){ 
//block_desp_splg_savedata(1);
//print_r($_POST);

	//$rs = $DB->get_record('block_desp_cultures',array("userid"=>$USER->id,"item"=>$key));
	$dins=array();
	$dins["userid"]=$USER->id;
	$title=clean_param($_POST["title"], PARAM_TEXT);$dins["title"]=$title;
	$datum=clean_param($_POST["datum"], PARAM_TEXT);$dins["datum"]=$datum;
	$shortdescription=clean_param($_POST["shortdescription"], PARAM_TEXT);$dins["shortdescription"]=$shortdescription;
	$reaction=clean_param($_POST["reaction"], PARAM_TEXT);$dins["reaction"]=$reaction;
	$reflection=clean_param($_POST["reflection"], PARAM_TEXT);$dins["reflection"]=$reflection;
	
	$knowledge=clean_param($_POST["knowledge"], PARAM_TEXT);$dins["knowledge"]=$knowledge;
	$learnd=clean_param($_POST["learnd"], PARAM_TEXT);$dins["learnd"]=$learnd;
	$later=clean_param($_POST["later"], PARAM_TEXT);$dins["later"]=$later;
	$country=clean_param($_POST["country"], PARAM_TEXT);$dins["country"]=$country;
	
	if ($_POST["recid"]=="-1"){
			$recid=$DB->insert_record('block_desp_begegnung', $dins);
	}else{
		$recid=intval($_POST["recid"]);
		$dins["id"]=$recid;
		$DB->update_record('block_desp_begegnung', $dins);
	}

}else if ($recid>0){
	$begegnung =  $DB->get_record('block_desp_begegnung', array('id'=>$recid));
	$title=$begegnung->title;
	$shortdescription=$begegnung->shortdescription;$datum=$begegnung->datum;$reaction=$begegnung->reaction;$reflection=$begegnung->reflection;$knowledge=$begegnung->knowledge;$learnd=$begegnung->learnd;$later=$begegnung->later;$country=$begegnung->country;
}else{
	$title="";$shortdescription="";$datum="";$reaction="";$reflection="";$knowledge="";$learnd="";$later="";$country="";
}

$hdrtmp=block_desp_print_header("kulturbegegnung",true,false);
$hdrers='
<link rel="stylesheet" type="text/css" media="all"
		href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/ui-darkness/jquery-ui.css"/>
	<script type="text/javascript"
		src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js">
	</script>
	<script type="text/javascript"
		src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/jquery-ui.min.js">
	</script>
'."
<script type=\"text/javascript\">
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

		$( '.date' ).datepicker({ dateFormat: 'dd.mm.yy' });
	});
	</script>
";
$hdrtmp=str_replace("</head>",$hdrers."</head>",$hdrtmp);
echo $hdrtmp;
?>


<div id="desp">
<div id="page_margins">
    <div id="content">

          <h1><?php echo get_string('begegnungen_mit_sprachen_kulturen','block_desp'); ?></h1>
              <div id="messageboxslp1" style="background: url('images/message_slp1.gif') no-repeat left top;">
            <div id="messagetxtslp1">
				<?php echo get_string('begegnungen_mit_sprachen_kulturen_inhalt','block_desp'); ?>
 			 </div>
      		</div>


      <br /><br /><br />
       <h2><?php echo get_string('bisherige_begegnungen','block_desp'); ?></h2>
      <br />
      <table class="tableform3">
      	<?php
      	$begegnungen =  $DB->get_records('block_desp_begegnung', array('userid'=>$USER->id), 'title');
      	foreach ($begegnungen as $begegnung){
      		echo '<tr><td style="background: url(\'images/collapsed.png\') no-repeat left center;clear:left;padding-left:20px;"><span class="kgbdate ">'.$begegnung->datum.'  </span></td><td class=""><a href="'.$CFG->wwwroot.'/blocks/desp/kulturbegegnung.php?courseid='.$courseid.'&amp;recid='.$begegnung->id.'">'.$begegnung->title.'</a></td>';
      		echo '<td style="padding-left:15px;" class=""><a href="'.$CFG->wwwroot.'/blocks/desp/kulturbegegnung.php?courseid='.$courseid.'&amp;did='.$begegnung->id.'"><img src="'.$CFG->wwwroot.'/pix/t/delete.gif" alt="delete" /></a></td>';
	
      		echo '</tr>';

      	}
      	?>
      </table>
      
      
      
<br /><br />
        <form id="formularhead" method="post" action="<?php echo $CFG->wwwroot;?>/blocks/desp/kulturbegegnung.php?courseid=<?php echo $courseid ?>">
						<div>
					
 		<table class="tableform3">
            <tr>
                <th class="kgb1" style="width:300px !important;"><?php echo get_string('titel','block_desp'); ?></th>
                <th class="kgb2"><?php echo get_string('datum','block_desp'); ?></th>
            </tr>
            <tr>
                <td class="kgb1" style="width:300px !important;"><input maxlength="120" style="min-width:440px;" class="value" value="<?php echo $title; ?>" name="title" /><input value="<?php echo $recid; ?>" name="recid" type="hidden" /></td>
                <td class="kgb2"><input value="<?php echo $datum; ?>" name="datum" type="text" size="7" maxlength="30" class="date" /><img src="images/calendar.gif" alt="calendar" /></td>
            </tr>
        </table>
        
        
        <br />
        
        
        <table class="tableform3">
            <tr>
                <th class="kgb25p"><?php echo get_string('kurzbeschreibung_begegnung','block_desp'); ?></th>
            </tr>
            <tr>
                <td class="kgb25p"><textarea cols="" rows="" name="shortdescription" class=""><?php echo $shortdescription; ?></textarea></td>
            </tr>
        </table>
        
        
        
        <br />
        
        
        
 		<table class="tableform3">
            <tr>
                <th class="kgb25p"><?php echo get_string('erstereaktion','block_desp'); ?><span class="grey">
<?php echo get_string('erstereaktion_inhalt','block_desp'); ?></span></th>
                <th class="kgb25p">
<?php echo get_string('darueber_gesprochen','block_desp'); ?><span class="grey">
<?php echo get_string('darueber_gesprochen_inhalt','block_desp'); ?></span></th>
                <th class="kgb25p">
				<?php echo get_string('mehrerfahren','block_desp'); ?><span class="grey">
<?php echo get_string('mehrerfahren_inhalt','block_desp'); ?></span></th>
            </tr>
            <tr>
                <td class="kgb25p"><textarea cols="" rows="" name="reaction" class=""><?php echo $reaction; ?></textarea></td>
                <td class="kgb25p"><textarea cols="" rows="" name="reflection" class=""><?php echo $reflection; ?></textarea></td>
                <td class="kgb25p"><textarea cols="" rows="" name="knowledge" class=""><?php echo $knowledge; ?></textarea></td>
            </tr>
        </table>


		<br />


        <table class="tableform3">
            <tr>
                <th class="kgb"><?php echo get_string('dieseerfahrung','block_desp'); ?><br />
				<?php echo get_string('inoesterreich','block_desp'); ?><input class="value headform" value="<?php echo $country; ?>" name="country" /> 
				<?php echo get_string('herausgefunden','block_desp'); ?></th> </tr>
            <tr>
                <td class="kgb"><textarea cols="" rows="" name="learnd" class=""><?php echo $learnd; ?></textarea></td>
            </tr>
        </table>


		<br />



        <table class="tableform3">
            <tr>
				<?php echo get_string('einigezeitspaeter','block_desp'); ?></th>
            </tr>
            <tr>
                <td class="kgb"><textarea cols="" rows="" name="later" class=""><?php echo $later; ?></textarea></td>
            </tr>
        </table>


<input type="hidden" name="data" value="gesendet" /></div>
          
						<div><input type="submit" id="save-button" value="<?php echo get_string('save','block_desp'); ?>" /></div>


        </form>
        <form method="post" action="<?php echo $CFG->wwwroot;?>/blocks/desp/kulturbegegnung.php?courseid=<?php echo $courseid ?>">
        	<p><input type="submit" value="<?php echo get_string('neuebegegnung','block_desp'); ?>" /></p>
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
