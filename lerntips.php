<?php
require_once dirname(__FILE__) . '/inc.php';
require_once(dirname(__FILE__) . "/lib/div.php");

global $COURSE, $CFG, $OUTPUT, $USER;
$content = "";
$courseid = optional_param('courseid', 1, PARAM_INT);

require_login($courseid);

//$context = get_context_instance(CONTEXT_COURSE, $courseid);
//require_capability('block/exabis_competences:teacher', $context);

$url = '/blocks/desp/lerntips.php?courseid=' . $courseid;
$PAGE->set_url($url);
$url = $CFG->wwwroot . $url;
$identifier = "teachertabassigncompetenceexamples";
$langid = optional_param('langid', 0, PARAM_INT);

block_desp_print_header("lerntips",false);
echo '<script type="text/javascript" src="lib/wz_tooltip.js"></script>';
echo '<script type="text/javascript" src="lib/simpletreemenu.js"></script>
      <link rel="stylesheet" type="text/css" href="lib/simpletree.css" />';

?>
</head><body>
	<div id="desp">
<div id="content">

<h1><?php echo get_string("lerntips", "block_desp") ?></h1>


			<div id="messageboxslp3" style="background: url('images/message_lp.gif') no-repeat left top;margin-left: 20px;">
          		<div id="messagetxtslp3" style="width: 455px;">
            		<?php echo get_string('aufgabenchecklisten','block_desp'); ?></div>
        		</div>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?courseid='.$courseid; ?>">
<?php echo get_string("filter", "block_desp") ?>
<?php
$myLanguagesSql = 'SELECT lang.name,lang.id  
	FROM {block_desp_examples} AS ex
	INNER JOIN {block_desp_lang} AS lang ON ex.lang=lang.id
	GROUP BY lang.id, lang.name ORDER BY lang.name';
$myLanguages = $DB->get_records_sql($myLanguagesSql);
?>
<select name="langid">
			<?php
			foreach ($myLanguages as $language) {
				echo '<option value="'.$language->id.'"';
				if ($langid==$language->id) echo ' selected="selected"';
				echo '>'.$language->name.'</option>';
			}
			?>
			<input type="submit" value="<?php echo get_string("filteranwenden", "block_desp") ?>" />
		</select>
</form>
<br />
</div>
</div>
<a href="javascript:ddtreemenu.flatten('comptree', 'expand')"><?php echo get_string("expandcomps", "block_desp") ?></a> | <a href="javascript:ddtreemenu.flatten('comptree', 'contact')"><?php echo get_string("contactcomps", "block_desp") ?></a>

<?php echo block_desp_build_comp_tree($langid); ?>


<script type="text/javascript">
    ddtreemenu.createTree("comptree", true)
</script>
<?php
	include_once ("despfooter.php");
?>
<?php
echo $OUTPUT->footer();
?>

<?php
/*
  $content.='<div class="grade-report-grader">
  <table id="comps" class="compstable flexible boxaligncenter generaltable">
  <tr class="heading r0">
  <td class="category catlevel1" colspan="2" scope="col"><h2>' . $COURSE->fullname . '</h2></td></tr>';
  $descriptors = block_exabis_competences_get_examples($courseid);
  $trclass = "even";
  $topic = "";
  foreach ($descriptors as $descriptor) {
  if ($trclass == "even") {
  $trclass = "odd";
  $bgcolor = ' style="background-color:#efefef" ';
  } else {
  $trclass = "even";
  $bgcolor = ' style="background-color:#ffffff" ';
  }

  if ($topic !== $descriptor->topic) {
  $topic = $descriptor->topic;
  $content .= '<tr><td colspan="2"><b>' . $topic . '</b></tr>';
  }
  $content .= '<tr class="r2 ' . $trclass . '" ' . $bgcolor . '><td>'.$descriptor->title.'</td>';
  if(isset ($descriptor->examples))
  $examples = $descriptor->examples;
  if(isset($examples)) {
  $content .= '<td>';
  foreach($examples as $example) {
  $icon = block_exabis_competences_get_exampleicon($example);
  $content .= $icon;
  }
  $content .= '</td></tr>';
  unset($examples);
  }
  else {
  $content .= '<td></td></tr>';
  }
  }
  $content.="</table></div>";
  echo $content; */
?>
