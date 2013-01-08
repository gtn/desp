<?php
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div.php';
require_once dirname(__FILE__) . '/lib/div_lernplan.php';

global $COURSE, $CFG, $OUTPUT;

$courseid = required_param('courseid', PARAM_INT);
if (!isset($courseid)

    )$courseid = $COURSE->id;

$course = $DB->get_record('course', array("id" => $courseid));
$action = optional_param('action', "", PARAM_ALPHA);

require_login($courseid);
$url = '/blocks/desp/index.php';
$PAGE->set_url($url);
$hdrtmp=block_desp_print_header("index",true,false);
$hdrers='
<script type="text/javascript">
<!--
function toggleDiv(element){
 if(document.getElementById(element).style.display == "none")
  document.getElementById(element).style.display = "block";
 else
       document.getElementById(element).style.display = "none";
 if(document.getElementById(element).style.display == "block")
  document.getElementById(element).style.display = "block";
 else
       document.getElementById(element).style.display = "none";
}
function toggleDivOut(element){

       document.getElementById(element).style.display = "none";
}
//-->
</script>
';
$hdrtmp=str_replace("</head>",$hdrers.'</head>',$hdrtmp);
echo $hdrtmp;
?>


<div id="desp">
<div id="page_margins">
    <div id="content">
 <h1><?php echo get_string('liebeschueler','block_desp'); ?></h1>
        <br />    
        <p><?php echo get_string('esp1','block_desp'); ?></p>
        <br />
        <p><b><?php echo get_string('esp2','block_desp'); ?></b></p>
        <br />
<?php
if(!block_desp_checkimport()) {
	echo '
	<div class="messageimportantinfo">
		<div style="font-size:25px;font-weight: bold; float:left;font-family: Times, Verdana, Arial, sans-serif; padding-right:20px;">!</div>
		<div>'.get_string('noimport','block_desp').'</div> 
	</div>
	';
	?>
	<br />
	<div class="top"><p><a href="http://www.bmukk.gv.at/"><img src="images/bmukk.png" alt="bm:ukk" /></a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.oesz.at/"><img src="images/oesz.png" alt="Das Fachinstitut für Innovationen im Sprachenlernen und -lehren." /></a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.gtn-solutions.com"><img src="images/gtn.png" alt="gtn" /></a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"><img src="images/vph.png" alt="vph" /></a>
	</p></div>
	<br />
	<?php echo get_string('texte','block_desp'); ?><br />
	<?php echo get_string('koordination','block_desp'); ?><br />
	<?php echo get_string('programmierung','block_desp'); ?><a href="http://www.gtn-solutions.com">gtn GmbH</a></div>
	<?php
	echo $OUTPUT->footer($course);
	exit;
}
echo block_desp_lernplanalarm();
?>
<div id="startcol" class="clearfix">

		<div id="startcols1">
			<div class="clearfix">
				<div class="startsymbhide" onmouseover="toggleDiv('togglepass')" onmouseout="toggleDivOut('togglepass')">
				 <h3>
				 	<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachenpass.php?courseid=<?php echo $courseid ?>">
				 		<?php echo get_string('sprachenpass','block_desp'); ?>
				 	</a>
				 </h3>
				 
				<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachenpass.php?courseid=<?php echo $courseid ?>">
					<img src="images/sympassgr.jpg" alt="Sprachenpass" /></a>
					
				
				
				</div>
			</div>
		</div>
		
		
		<div id="startcols2">
			<div class="clearfix">
				<div class="startsymbhide" onmouseover="toggleDiv('togglebio')" onmouseout="toggleDivOut('togglebio')">
				<h3>
					<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachenbiografie.php?courseid=<?php echo $courseid ?>">
							<?php echo get_string('sprachenbiografie','block_desp'); ?>
					</a>
				</h3>
				 
				<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachenbiografie.php?courseid=<?php echo $courseid ?>">
					<img src="images/symbiogr.jpg" alt="Sprachenbiografie" /></a>
	
				</div>
			</div>
		</div>
		
		
		
		<div id="startcols3">
			<div class="clearfix">
				<div class="startsymbhide" onmouseover="toggleDiv('toggledossier')" onmouseout="toggleDivOut('toggledossier')">
				<?php if($DB->get_record('block',array("name"=>"exaport"))) {?>
				<h3>
					<a href="<?php echo $CFG->wwwroot;?>/blocks/exaport/view_items.php?courseid=<?php echo $courseid ?>">
						<?php echo get_string('dossier','block_desp'); ?>	
					</a>
				</h3>
				
					<a href="<?php echo $CFG->wwwroot;?>/blocks/exaport/view_items.php?courseid=<?php echo $courseid ?>">
						<img src="images/symdossgr.jpg" alt="Dossier" /></a>
				<?php } else { ?>
				<h3>
						<?php echo get_string('dossier','block_desp'); ?>	
				</h3>
						<img src="images/symdossgr.jpg" alt="Dossier" />
				<?php } ?>
				</div>
			</div>
		</div>
		
</div>





<div id="togglepass" class="messagetxthidetwo" style="display:none;">
	<?php echo get_string('sprachenpass_inhalt','block_desp'); ?>
</div>

<div id="togglebio" class="messagetxthidetwo" style="display:none;">
	<?php echo get_string('sprachenbiografie_inhalt','block_desp'); ?>
</div>

<div id="toggledossier" class="messagetxthidetwo" style="display:none;">
	 <?php
	 if($DB->get_record('block',array("name"=>"exaport")))
	 	echo get_string('dossier_inhalt','block_desp');
	 else 
	 	echo get_string('dossier_fehlt','block_desp');
	 ?>
</div>
		
		 <br />




     <!--    <p>Diese drei Teile zusammen sind das Europ&auml;ische Sprachenportfolio, das schon viele Kinder und Jugendliche in ganz Europa verwenden. Du wirst mit dem dESP gemeinsam mit deinen Mitsch&uuml;lerinnen und Mitsch&uuml;lern &ouml;fters im Unterricht arbeiten. Dann kannst du sehen, wie du langsam, aber sicher als Sprachenlernerin oder Sprachenlerner Fortschritte machst, denn dein dESP wird sich in den n&auml;chsten Jahren f&uuml;llen - und du wirst sehr stolz sein k&ouml;nnen auf dich und dein Sprachenportfolio! </p>
        <br />
        <br />
        <img src="images/esp15.jpg" alt="" class="float_image_right" />
        <p>P. S. Wenn du in die Sekundarstufe II weiter gehst, wartet das <b>ESP 15+</b> auf dich. Eins noch zum Schluss: Sprachenlernen ist nicht nur Sache der Schule, es h&ouml;rt nicht auf, wenn du die Schule abschlie&szlig;t - Sprachenlernen soll deine eigene Sache werden, dein ganzes Leben lang!	</p>
        <div class="clearer"></div>-->


    </div>
</div>
<?php
	include_once ("despfooter.php");
?> 
<br />
<div class="top"><p><a href="http://www.bmukk.gv.at/"><img src="images/bmukk.png" alt="bm:ukk" /></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.oesz.at/"><img src="images/oesz.png" alt="Das Fachinstitut für Innovationen im Sprachenlernen und -lehren." /></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.gtn-solutions.com"><img src="images/gtn.png" alt="gtn" /></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#"><img src="images/vph.png" alt="vph" /></a>
</p></div>
<br />
<?php echo get_string('texte','block_desp'); ?><br />
<?php echo get_string('koordination','block_desp'); ?><br />
<?php echo get_string('programmierung','block_desp'); ?><a href="http://www.gtn-solutions.com">gtn GmbH</a></div>
<?php
echo $OUTPUT->footer($course);
?>