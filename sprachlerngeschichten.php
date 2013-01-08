<?php
global $DB, $COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_SEQUENCE);

require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/sprachencheckliste.php';
$PAGE->set_url($url);
block_desp_print_header("sprachlerngeschichten");
?>

<div id="desp">
<div id="page_margins">
    <div id="content">

        <h1><?php echo get_string('sprachlerngeschichte1', 'block_desp');?></h1>

        <hr />

        <br />


		 <h3><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichte.php?courseid=<?php echo $courseid ?>"><?php echo get_string('sprachenfamilieumgebung', 'block_desp');?></a></h3>
			<div id="messagebox" style="background: url('images/message.gif') no-repeat left top;">
					<div id="messagetxt">
						<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichte.php?courseid=<?php echo $courseid ?>">
						<?php echo get_string('sprachenfamilieumgebung_inhalt', 'block_desp');?>
						</a>
					</div>
			</div>
			
			<br /><br />
			
		 <h3><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichte_bisher.php?courseid=<?php echo $courseid ?>"><?php echo get_string('sprachenschulekurse', 'block_desp');?></a></h3>
			<div id="messageboxslp1" style="background: url('images/message_sp1.gif') no-repeat left top;margin-left: 30px;">
				<div id="messagetxtslp1" style="width: 455px;">
					<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichte_bisher.php?courseid=<?php echo $courseid ?>">
            	    <?php echo get_string('sprachenschulekurse_inhalt', 'block_desp');?>
               		</a>
            	</div>
      	  </div>

			<br /><br />
		<h3><a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichte_schule.php?courseid=<?php echo $courseid ?>"><?php echo get_string('sprachlicheschwerpunkt', 'block_desp');?></a></h3>
			<div id="messageboxslp3" style="background: url('images/message_lp.gif') no-repeat left top;margin-left: 20px;">
          		<div id="messagetxtslp3" style="width: 455px;">
          			<a href="<?php echo $CFG->wwwroot;?>/blocks/desp/sprachlerngeschichte_schule.php?courseid=<?php echo $courseid ?>">
            	    <?php echo get_string('sprachlicheschwerpunkt_inhalt', 'block_desp');?>
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