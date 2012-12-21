<?php
global $DB,$COURSE;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div.php';
require_once dirname(__FILE__) . '/lib/div_lernplan.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_SEQUENCE);

require_login($courseid);

$course = $DB->get_record('course',array("id"=>$courseid));
$url = '/blocks/desp/sprachenbiografie.php';
$PAGE->set_url($url);

if(!block_desp_checkimport())
	header("Location: ".$CFG->wwwroot."/blocks/desp/index.php?courseid=".$courseid);

block_desp_print_header("sprachenbiografie");

$new_comments = block_desp_check_new_learnplan_comments();
?>
<div id="desp">
<div id="page_margins">
    <div id="content">

        <h1><?php echo get_string('sprachenbiografie','block_desp'); ?></h1>



        <div id="messageboxslp1" style="background: url('images/message_slp1.gif') no-repeat left top;">
            <div id="messagetxtslp1">
               	<?php echo get_string('sprachenbiografie2','block_desp'); ?>
				<span class="mehrtext"><?php echo get_string('ausklappen','block_desp'); ?></span>
				<div class="messagetxthide">
                <br />      
				<?php echo get_string('sprachenbiografie3','block_desp'); ?>
                </div>
			
			
			 </div>
        </div>


			<br />			
			<?php
echo block_desp_lernplanalarm();
?>
<br />

        <h2><?php echo get_string('deinesprachenbiografie','block_desp'); ?></h2>
		
        <br />


        
        <div id="startcol" class="clearfix">
        	
        	
        	<div id="startcols1">
				<div class="clearfix">
					<div class="startsymbhide startsymbhidebio">
						
        					<h4><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichten.php?courseid=<?php echo $courseid ?>"><?php echo get_string('meine','block_desp'); ?><br /><?php echo get_string('sprachlerngeschichte','block_desp'); ?></a></h4>
							<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichten.php?courseid=<?php echo $courseid ?>"><img src="images/symlerngeschichte.gif" alt="<?php echo get_string('sprachenpass','block_desp'); ?>" /></a>
				
						<div class="messagetxthide hidebio" style="position:absolute;z-index:599;">
							<h5><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichte.php?courseid=<?php echo $courseid ?>"><?php echo get_string('sprachen_familieundumgebung','block_desp'); ?></a></h5>
							<h5><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichte_bisher.php?courseid=<?php echo $courseid ?>"><?php echo get_string('sprachen_schuleundkurse','block_desp'); ?></a></h5>
							<h5 style="margin-bottom: 0;"><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichte_schule.php?courseid=<?php echo $courseid ?>"><?php echo get_string('sprachlicheschwerpunkte','block_desp'); ?></a></h5>
				
				 		 </div>
					</div>
				</div>
			</div>
					
					
        	<div id="startcols2">
				<div class="clearfix">
					<div class="startsymbhide startsymbhidebio">
					
							<?php
								if($new_comments) $url = "images/symlernplaene_plus.gif";
								else $url = "images/symlernplaene.gif";
							?>
							<h4><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlernplaene.php?courseid=<?php echo $courseid ?>"><?php echo get_string('meine','block_desp'); ?><br /><?php echo get_string('sprachlernplaene','block_desp'); ?></a></h4>
							<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlernplaene.php?courseid=<?php echo $courseid ?>"><img src="<?php echo $url; ?>" alt="Meine Sprachlern-Pl&auml;ne" /></a>
							
						<div class="messagetxthide hidebio" style="position:absolute;z-index:599;">
							<h5><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlernplan_lernpartner_einschaetzung.php?courseid=<?php echo $courseid ?>"><?php echo get_string('lerpartnerunterstuetzen','block_desp'); ?></a></h5>
							<h5><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlernplan_fremdeinschaetzung.php?courseid=<?php echo $courseid ?>"><?php if($new_comments) echo "!!! "; ?><?php echo get_string('einschaetzunglernpartner','block_desp'); ?><?php if($new_comments) echo " !!!"; ?></a></h5>
				 		 </div>
					</div>
				</div>
			</div>
			
					
					
        	<div id="startcols3">
				<div class="clearfix">
					<div class="startsymbhide startsymbhidebio">
					
							<h4><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachenchecklisten.php?courseid=<?php echo $courseid ?>"><?php echo get_string('meine','block_desp'); ?><br /><?php echo get_string('sprachencheckliste','block_desp'); ?></a></h4>
							<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachenchecklisten.php?courseid=<?php echo $courseid ?>"><img src="images/symsprachenlisten.gif" alt="Meine Sprachen-Checklisten" /></a>
					
						<div class="messagetxthide hidebio" style="position:absolute;z-index:599;">
							<h5><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/lernpartner_einschaetzung.php?courseid=<?php echo $courseid ?>"><?php echo get_string('lernpartnereinschaetzen','block_desp'); ?></a></h5>

				 		 </div>
					</div>
				</div>
			</div>
			
			
		</div>
	
	<br /><br />
	
        <div id="startcolx2" class="clearfix" style="margin-bottom:235px;">
			
					
        <div id="startcols21">
					<div class="clearfix">
						<div class="startsymbhide startsymbhidebio">
								<h4><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/lerntips.php?courseid=<?php echo $courseid ?>"><?php echo get_string('aufgaben','block_desp'); ?><br /><?php echo get_string('zuchecklisten','block_desp'); ?></a></h4>
								<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/lerntips.php?courseid=<?php echo $courseid ?>"><img src="images/symaufgaben.gif" alt="Aufgaben zu Checklisten" /></a>
						</div>
					</div>
				</div>
			
			<div id="startcols22">
					<div class="clearfix">
						<div class="startsymbhide startsymbhidebio">
								<h4><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/center.php?courseid=<?php echo $courseid ?>"><?php echo get_string('mitteilungs','block_desp'); ?><br /><?php echo get_string('zentrale','block_desp'); ?></a></h4>
								<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/center.php?courseid=<?php echo $courseid ?>"><img src="images/mitteilungscenter.gif" alt="Mitteilungszentrale Lernpartner" /></a>
						</div>
					</div>
				</div>
					
        	<div id="startcols23">
				<div class="clearfix">
					<div class="startsymbhide startsymbhidebio">
					
							<h4><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachenundkulturen_items.php?courseid=<?php echo $courseid ?>"><?php echo get_string('sprachenundkulturen','block_desp'); ?><br /></a></h4>
							<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachenundkulturen_items.php?courseid=<?php echo $courseid ?>"><img src="images/symkulturen.gif" alt="Sprachen und Kulturen erforschen" /></a>
				
						<div class="messagetxthide hidebio" style="position:absolute;z-index:599;">
							<h5><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachenundkulturen_items.php?courseid=<?php echo $courseid ?>"><?php echo get_string('verschiedene_kulturenundsprachen','block_desp'); ?></a></h5>
							<h5><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/kulturbegegnung.php?courseid=<?php echo $courseid ?>"><?php echo get_string('begegnungen_sprachenundkulturen','block_desp'); ?></a></h5>
							<h5><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachenkulturenprojektidee.php?courseid=<?php echo $courseid ?>"><?php echo get_string('sprachenkulturen_projektideen','block_desp'); ?></a></h5>
		        			
				<!--h5><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/verzeichnissarbeiten.php?courseid=<?php echo $courseid ?>">Verzeichnis der Arbeiten</a></h5-->

				 		 </div>
					</div>
				</div>
			</div>
				

				
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
