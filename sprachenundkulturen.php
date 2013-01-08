<?php
global $DB, $COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);

require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/sprachenundkulturen.php';
$PAGE->set_url($url);

$data = optional_param('data', null, PARAM_RAW);

if ($data){ 
//block_desp_splg_savedata(1);
//print_r($_POST);
foreach($_POST["text1"] as $key=>$value){
	$rs = $DB->get_record('block_desp_cultures',array("userid"=>$USER->id,"item"=>$key));
	$dins=array();
	$dins["userid"]=$USER->id;
	$dins["item"]=$key;
	$dins["experience"]=clean_param($value, PARAM_TEXT);
	$dins["dossier"]=clean_param($_POST["text2"][$key], PARAM_TEXT);
	if (empty($rs)){
		if($dins["experience"]!=""){
			$DB->insert_record('block_desp_cultures', $dins);
			//echo "<br>insert ".$key;
		}
	}else{
		$dins["id"]=$key;
		$sql='Update {block_desp_cultures} SET experience="'.$dins["experience"].'", dossier="'.$dins["dossier"].'" WHERE item='.$key.' AND userid='.$dins["userid"];
		
		$DB->Execute($sql);
		//$DB->update_record('block_desp_cultures', $dins);
		//echo "<br>update ".$key;
	}
}
}

$hdrtmp=block_desp_print_header("sprachenundkulturen",true,false);
$hdrers="<link href='http://fonts.googleapis.com/css?family=Coming+Soon' rel='stylesheet' type='text/css' />";
$hdrtmp=str_replace("</head>",$hdrers."</head>",$hdrtmp);
echo $hdrtmp;
?>

<div id="desp">
<div id="page_margins">
    <div id="content">

         <h1><?php echo get_string('sprachenundkulturenerforschen','block_desp'); ?></h1>


        <div id="messageboxsk" style="background: url('images/message_sk.gif') no-repeat left top;">
            <div id="messagetxtsk">
                <?php echo get_string('sprachenundkulturenerforschen_inhalt','block_desp'); ?>
            </div>
        </div>

        <br /><br />

        <h3><?php echo get_string('wiewirundandereleben','block_desp'); ?></h3>

		<div id="messagetxthidesuk">
			
        <p>
            <?php echo get_string('wiewirundandereleben_inhalt','block_desp'); ?><span class="mehrtext"><?php echo get_string('ausklappen','block_desp'); ?></span></p>
        <br />
        
         	<div class="messagetxthide">
        <p><?php echo get_string('wiewirundandereleben_inhalt2','block_desp'); ?></p>
        <br /><br />
        <h3><?php echo get_string('anderesehen','block_desp'); ?></h3>

        <p>
            <?php echo get_string('typischer_','block_desp'); ?></p>
        <br />


        

        <p style="text-align:center;"><img src="images/sprachenkulturensprechblasen.gif" alt="stereotype aussagen in eine Sprechblase, der typische &Ouml;sterreicher ..." /></p>

		 </div>
		</div>
		 
        
		<br />
		<!--
		<table class="tableform3 overviewses overview_sesyell">
		<tbody>
		<tr>
			<th class="tableses1">Kategorien</th>
		</tr>
		<?php
			$url = $CFG->wwwroot."/blocks/desp/sprachenundkulturen_erfassung.php?courseid=".$courseid."&amp;catid=";
			$categories = $DB->get_records('block_desp_cultures_item_cat');
			foreach($categories as $category) {
				echo "<tr><td style='background: #ffefd3'>";
				echo "<a class='overview ov_skill1' style='background: #ffefd3' href='".$url.$category->id."'>".$category->title."</a>";
				echo "</td></tt>";
			}
		?>
		</tbody>
		</table>-->
	
    </div>
</div>
</div>
<?php
	include_once ("despfooter.php");
?>
<?php
echo $OUTPUT->footer($course);
?>
