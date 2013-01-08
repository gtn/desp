<?php
global $DB, $COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);

require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/sprachenundkulturen_items.php';
$PAGE->set_url($url);


$hdrtmp=block_desp_print_header("sprachenundkulturen_items",true,false);
$hdrers="<link href='http://fonts.googleapis.com/css?family=Coming+Soon' rel='stylesheet' type='text/css' />";
$hdrtmp=str_replace("</head>",$hdrers."</head>",$hdrtmp);
echo $hdrtmp;
?>

<div id="desp">
<div id="page_margins">
    <div id="content">

        <h1><?php echo get_string('wasichherausgefundenhabe2','block_desp'); ?></h1>


        <div id="messageboxsk" style="background: url('images/message_sk.gif') no-repeat left top;">
            <div id="messagetxtsk">
                <?php echo get_string('wasichherausgefundenhabe_inhalt','block_desp'); ?>
            </div>
        </div>

        <br /><br />

        <h3><?php echo get_string('wiewirundandereleben','block_desp'); ?></h3>
        
         
        
		<br />
		<table class="tableform3 overviewses overview_sesyell">
		<tbody>
		<tr>
			<th class="tableses1"></th>
		</tr>
		<?php
			$url = $CFG->wwwroot."/blocks/desp/sprachenundkulturen_erfassung.php?courseid=".$courseid."&amp;catid=";
			$categories = $DB->get_records('block_desp_cultures_item_cat');
			foreach($categories as $category){
				echo "<tr><td style='background: #ffefd3'>";
				echo "<a class='overview ov_skill1' style='background: #ffefd3' href='".$url.$category->id."'>".get_string(strtolower(str_replace("ö", "oe", str_replace(" ", "", $category->title))), 'block_desp')."</a>";
				echo "</td></tt>";
			}
		?>
		</tbody>
		</table>
	
    </div>
</div>
</div>
<?php
	include_once ("despfooter.php");
?>
<?php
echo $OUTPUT->footer($course);
?>
