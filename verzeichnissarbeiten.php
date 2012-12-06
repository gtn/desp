<?php
global $DB, $COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);

require_login($courseid);

$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/verzeichnissarbeiten.php';
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
		}
	}else{
		$dins["id"]=$key;
		$DB->update_record('block_desp_cultures', $dins);
	}
}
}

$hdrtmp=block_desp_print_header("kulturbegegnung",true,false);
$hdrers='
<link href="http://fonts.googleapis.com/css?family=Coming+Soon" rel="stylesheet" type="text/css">';
$hdrtmp=str_replace("</head>",$hdrers."</head>",$hdrtmp);
echo $hdrtmp;
?>


<div id="page_margins">
    <div id="content">

         <h1><?php echo get_string('verzeichnisderarbeiten','block_desp'); ?></h1>


      <h2><?php echo get_string('artderarbeit','block_desp'); ?></h2>
   
   
   
   			<table class="vzartableleg vzartableleg1">
   				<tr>
   					<th colspan="2"><?php echo get_string('arbeitmitschwerpunktauf','block_desp'); ?></th>
   				</tr>
   				<tr>
   					<td>1</td>
   					<td><?php echo get_string('hoeren','block_desp'); ?></td>
   				</tr>
   				<tr>
   					<td>2</td>
   					<td><?php echo get_string('lesen','block_desp'); ?></td>
   				</tr>
   				<tr>
   					<td>3</td>
   					<td><?php echo get_string('angespraechenteilnehmen','block_desp'); ?></td>
   				</tr>
   				<tr>
   					<td>4</td>
   					<td><?php echo get_string('zusammenhaengendsprechen','block_desp'); ?></td>
   				</tr>
   				<tr>
   					<td>5<br/>5.1<br/>5.2</td>
   					<?php echo get_string('typischearbeit','block_desp'); ?>
   				</tr>
   			</table>
   			
   			 <table class="vzartableleg">
   				<tr>
   					<th colspan="2"><?php echo get_string('artderarbeit','block_desp'); ?></th>
   				</tr>
   				<tr>
   					<?php echo get_string('einzelarbeit','block_desp'); ?>
   				</tr>
   				<tr>
   					<?php echo get_string('partnerarbeit','block_desp'); ?>
   				</tr>
   				<tr>
   					<?php echo get_string('gruppenarbeit','block_desp'); ?>
   				</tr>
   				<tr>
   					<?php echo get_string('reflexion','block_desp'); ?>
   				</tr>
   				<tr>
   					<?php echo get_string('dokumentiertmit','block_desp'); ?></td>
   				</tr>
   			</table>
   
   			
					
					
			<br />
			<p><?php echo get_string('beispiel','block_desp'); ?></p>
			<br />
					
					
 		<table class="tableform3 vzartable">
            <tr>
                <?php echo get_string('beispiel_tabelle','block_desp'); ?>
            </tr>
            <tr>
                <?php echo get_string('beispiel_tabelle2','block_desp'); ?>
            </tr>
            <tr>
                <?php echo get_string('beispiel_tabelle3','block_desp'); ?>
            </tr>
            <tr>
                <?php echo get_string('beispiel_tabelle4','block_desp'); ?>
            </tr>
            <tr>
                <?php echo get_string('beispiel_tabelle5','block_desp'); ?>
            </tr>
        </table>
        
   
   
        <form name="formular" id="formularhead" method="post" action="<?php echo $CFG->wwwroot;?>/blocks/desp/kulturbegegnung.php?courseid=<?php echo $courseid ?>">
						<div>
		
        
        <br />
        
 		<table class="tableform3 vzartable">
            <tr>
                 <?php echo get_string('beispiel_tabelle6','block_desp'); ?>
            </tr>
            <tr>
                <td class="vzar1"><input class="value" value="" name="" size="3"></td>
                <td class="vzar2"><input class="value" value="" name=""></td>
                <td class="vzar3"><input class="value" value="" name=""></td>
                <td class="vzar1"><input class="value" value="" name="" size="3"></td>
                <td class="vzar3"><input class="value" value="" name=""></td>
                <td class="vzar4"><input name="" type="text" size="7" maxlength="30" class="date"></td>
                <td class="vzar4"><input name="" type="text" size="7" maxlength="30" class="date"></td>
            </tr>
            <tr>
                <td class="vzar1"><input class="value" value="" name="" size="3"></td>
                <td class="vzar2"><input class="value" value="" name=""></td>
                <td class="vzar3"><input class="value" value="" name=""></td>
                <td class="vzar1"><input class="value" value="" name="" size="3"></td>
                <td class="vzar3"><input class="value" value="" name=""></td>
                <td class="vzar4"><input name="" type="text" size="7" maxlength="30" class="date"></td>
                <td class="vzar4"><input name="" type="text" size="7" maxlength="30" class="date"></td>
            </tr>
            <tr>
                <td class="vzar1"><input class="value" value="" name="" size="3"></td>
                <td class="vzar2"><input class="value" value="" name=""></td>
                <td class="vzar3"><input class="value" value="" name=""></td>
                <td class="vzar1"><input class="value" value="" name="" size="3"></td>
                <td class="vzar3"><input class="value" value="" name=""></td>
                <td class="vzar4"><input name="" type="text" size="7" maxlength="30" class="date"></td>
                <td class="vzar4"><input name="" type="text" size="7" maxlength="30" class="date"></td>
            </tr>
            
        </table>
        








<input type="hidden" name="data" value="gesendet" /></div>
          
						<div><input type="submit" id="save-button" value="<?php echo get_string('save', 'block_desp');?>" /></div>


        </form>


    </div>
</div>
<?php
	include_once ("despfooter.php");
?>
<?php
echo $OUTPUT->footer($course);
?>
