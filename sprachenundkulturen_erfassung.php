<?php
global $DB, $COURSE,$CFG;
require_once dirname(__FILE__) . '/inc.php';
require_once dirname(__FILE__) . '/lib/div_sprachlerngeschichten.php';

$courseid = optional_param('courseid', $COURSE->id, PARAM_ALPHANUM);
$all_tables = $DB->get_tables();
	if (in_array("block_exaportview", $all_tables)) {
		$exaport=true;
	}else{
		$exaport=false;
	}
$catid = required_param('catid',PARAM_INT);
$category = $DB->get_record('block_desp_cultures_item_cat',array("id" => $catid));

$category->titleshort = str_replace(" ","",strtolower($category->title));
if($catid == 5)
	$category->titleshort = "arbeitenundoeffentlichesleben";

require_login($courseid);
$course = $DB->get_record('course', array("id" => $courseid));
$url = '/blocks/desp/sprachenundkulturen_erfassung.php';
$PAGE->set_url($url);

$data = optional_param('data', null, PARAM_RAW);
function block_desp_spkuerf_deletedata($id){
	global $DB,$USER;
	$DB->delete_records('block_desp_cultures_items',array("userid"=>$USER->id, "id"=>$id));
}

function block_desp_spkul_updateculture($id,$value,$value2){
	global $DB,$USER;
	$rs = $DB->get_record('block_desp_cultures',array("userid"=>$USER->id,"item"=>$id));
		$dins=array();
		$dins["userid"]=$USER->id;
		$dins["item"]=$id;
		$dins["experience"]=$value;
		$dins["dossier"]=$value2;
		if (empty($rs)){
			if($dins["experience"]!=""){
				$DB->insert_record('block_desp_cultures', $dins);
			//echo "<br>insert ".$key;
		}
		}else{
			$dins["id"]=$id;
			$sql='Update {block_desp_cultures} SET experience="'.$dins["experience"].'", dossier="'.$dins["dossier"].'" WHERE item='.$id.' AND userid='.$dins["userid"];
			
			$DB->Execute($sql);
			//$DB->update_record('block_desp_cultures', $dins);
			//echo "<br>update ".$key;
		}
}
if ($data){ 
	//block_desp_splg_savedata(1);
	//print_r($_POST);
	if (!empty($_POST["titlen"])){
		foreach($_POST["titlen"] as $key=>$value){
			if ($value!=""){
				$dins=array();
				$dins["userid"]=$USER->id;
				$dins["title"]=clean_param($value, PARAM_TEXT);
				$dins["cat"]=$catid;
				$id=$DB->insert_record('block_desp_cultures_items', $dins);
				block_desp_spkul_updateculture($id,clean_param($_POST["text1n"][$key], PARAM_TEXT),clean_param($_POST["text2n"][$key], PARAM_TEXT));
			}
		}
	}
	if (!empty($_POST["text1"])){
	foreach($_POST["text1"] as $key=>$value){
		block_desp_spkul_updateculture(intval($key),clean_param($value, PARAM_TEXT),clean_param($_POST["text2"][$key], PARAM_TEXT));
	}
	}//foreach text1
}
$delete_id = optional_param('did', null, PARAM_SEQUENCE);
if (!empty($delete_id)){
	block_desp_spkuerf_deletedata($delete_id);
}

$hdrtmp=block_desp_print_header($category->titleshort,true,false);
$hdrers="<link href='http://fonts.googleapis.com/css?family=Coming+Soon' rel='stylesheet' type='text/css' />";
$hdrtmp=str_replace("</head>",$hdrers."</head>",$hdrtmp);
echo $hdrtmp;
if ($exaport==true){$tdossier=block_desp_pd_eportitems(0,true,"text2n[]");}
else{$tdossier='<textarea cols="" rows="5" name="text2n[]" class="sk3"></textarea>';}
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
				td.innerHTML='<textarea cols="" rows="5" name="titlen[]" class="sk2"></textarea>';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<textarea cols="" rows="5" name="text1n[]" class="sk2"></textarea>';
				newTr.appendChild(td);
				td = document.createElement('td');
				td.innerHTML='<?php echo $tdossier; ?>';
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

        <h1><?php echo get_string('wasichherausgefundenhabe','block_desp'); ?></h1>

		<?php
			$query = "SELECT i.id,i.userid,i.title,c.experience,c.dossier, cat.title as category FROM ";
			$query.="{block_desp_cultures_items} i INNER JOIN {block_desp_cultures_item_cat} cat ON cat.id=i.cat LEFT JOIN {block_desp_cultures} c ON c.item=i.id AND c.userid=".$USER->id."  WHERE cat.id = ".$catid." AND ((i.userid=0 OR ISNULL(i.userid)) OR i.userid=".$USER->id.")  ORDER BY cat.id,i.userid,i.id";
								
							
			$items = $DB->get_records_sql($query);
			
			if(!$items) {
		
		
		}
		

        $inhalt='
        <form id="formularhead" method="post" action="'.$CFG->wwwroot.'/blocks/desp/sprachenundkulturen_erfassung.php?courseid='.$courseid.'&amp;catid='.$catid.'">
						<div>
					<input type="hidden" name="data" value="gesendet" /></div>';
								$i=1;
								$kategoriename=get_string(strtolower(str_replace("ö", "oe", str_replace(" ", "", $category->title))), 'block_desp');
								$inhalt.='<h2 class="desp_sk_zwheader">'.$kategoriename.'</h2>';
								$inhalt.='		
										
										
										 <table class="tableform3 sk" id="params">
			            	<thead>
			                <tr>
			                    <th>'.get_string('habegesehen', 'block_desp').'</th>
			                    <th>'.get_string('wiewannwo', 'block_desp').'</th>
			                    <th>'.get_string('imdossierunter', 'block_desp').'</th>
			                    <th></th>
			                </tr>
			              </thead>
			              <tbody>
			              ';
								$keinedateneingabe=true;
								
								foreach($items as $item){
									if ($item->experience!="") $keinedateneingabe=false; 
									$inhalt.='<tr>';
									/*if ($i==1){
										 $inhalt.= '<td rowspan="'.count($items).'" class="sk1"><img src="images/sprachenkulturentxtside.gif" alt="" /></td>';
									}*/
									$inhalt.= '<td class="sk2">'.$item->title.'</td>';
									$inhalt.= '<td class="sk2"><textarea cols="" rows="5" name="text1['.$item->id.']" class="desp_cult_text1">'.$item->experience.'</textarea></td>';
									if ($exaport==true){
										$inhalt.= '<td class="sk3">'.block_desp_pd_eportitems($item->dossier,true,'text2['.$item->id.']').'</td>';
									}else{
										$inhalt.= '<td class="sk3"><textarea cols="" rows="5" name="text2['.$item->id.']" class="desp_cult_text2">'.$item->dossier.'</textarea></td>';
									}
									if ($item->userid>0){
										$inhalt.= '<td class="tddelete"><a href="'.$CFG->wwwroot.$url.'?courseid='.$courseid.'&amp;catid='.$catid.'&amp;did='.$item->id.'"><img src="'.$CFG->wwwroot.'/blocks/desp/images/delete.gif" alt="delete" /></a></td>';
									}else{
										$inhalt.= '<td>&nbsp;</td>';
									}
									
									$inhalt.= '</tr>';
									$i++;
								}

                if ($keinedateneingabe==true){
                	echo '
                	<br /><br />
        <p>'.get_string('beispiel', 'block_desp').'</p>
        <br />
        <table class="tableform3">
            <tr>
                <th colspan="2">'.get_string('habegesehen', 'block_desp').'</th>
                <th>'.get_string('wiewannwo', 'block_desp').'</th>
                <th>'.get_string('imdossierunter', 'block_desp').'</th>
            </tr>
            <tr>
                <td class="sk1 handwriting"></td>
                <td class="sk2 handwriting">'.get_string('schulalltaganders', 'block_desp').'</td>
                <td class="sk2 handwriting">'.get_string('ganztagsschule', 'block_desp').'</td>
                <td class="sk3 handwriting">'.get_string('alltagnummereins', 'block_desp').'</td>
            </tr>
        </table>

        <br /><br />
        ';
                }
                $inhalt.='</tbody></table>';
                echo $inhalt;
              ?>
              
						<div>
							<input type="button" id="add-param-button" value="<?php echo get_string('weitereserlebnishinzu', 'block_desp');?>" onclick="addRow('params');" />
							<input type="submit" id="save-button" value="<?php echo get_string('save', 'block_desp');?>" /></div>

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
