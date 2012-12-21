<?php

require_once dirname(__FILE__) . '/lib/div.php';

class block_desp extends block_base {

	function init() {
		$this->title = get_string('blocktitle', 'block_desp');
		$this->version = 2012122100;
	}

	// The PHP tag and the curly bracket for the class definition
	// will only be closed after there is another function added in the next section.
	function has_config() {
		return false;
	}

	function get_content() {
		global $CFG, $COURSE, $USER;
		//        if ($this->content !== NULL) {
		//            return $this->content;
		//        }
		//
		//        //CHECK CAPABILITYS
		//$context = get_context_instance(CONTEXT_SYSTEM)
		//$context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
		$context = get_context_instance(CONTEXT_SYSTEM);
		if (!has_capability('block/desp:use', $context)) {
			$this->content = '';
			return $this->content;
		}
		//
		//        $courseid = intval($COURSE->id);
		//        $this->content = new stdClass;
		//        $this->content->items = array();
		//        $this->content->icons = array();
		//
		//        //Adminbereich
		//        if (has_capability('block/exabis_competences:admin', $context)) {
		//
		//            $this->content->text = '';
		//            $this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/exabis_competences/pix/config.gif" height="16" width="16" alt="' . get_string("adminnavconfig", "block_exabis_competences") . '" />';
		//            $this->content->text.='<a title="configuration" href="' . $CFG->wwwroot . '/blocks/exabis_competences/edit_config.php?courseid=' . $courseid . '">' . get_string('adminnavconfig', 'block_exabis_competences') . '</a>';
		//            //$this->content->text.='<br /><img src="' . $CFG->wwwroot . '/blocks/exabis_competences/pix/databases.png" height="16" width="16" alt="' . get_string("adminnavimport", "block_exabis_competences") . '" />';
		//            //$this->content->text.='<a title="import" href="' . $CFG->wwwroot . '/blocks/exabis_competences/import.php">' . get_string('link_import', 'block_exabis_competences') . '</a>';
		//            $this->content->footer = '';
		//        }
		//        //Lehrerbereich
		//        //if (has_capability('block/exabis_competences:teacher', $context) && has_capability('moodle/course:update', $context) && $courseid != 1) {
		//        if (has_capability('block/exabis_competences:teacher', $context) && $courseid != 1) {
		//
		//            if (!empty($this->content->text))
			//                $this->content->text .= '<br />';
		//            //Prüfen ob der Lehrer den Kurs bereits zugeordnet hat
		//            if (!block_exabis_competences_isactivated($courseid)) {
		//                //Kurs nicht zugeordnet
		//                $this->content->text .= "Der Kurs muss einmalig konfiguriert werden.<br/>";
		//            }
		//            $this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/exabis_competences/pix/cog.png" height="16" width="16" alt="' . get_string("teachernavconfig", "block_exabis_competences") . '" />';
		//            $this->content->text.='<a title="edit course" href="' . $CFG->wwwroot . '/blocks/exabis_competences/edit_course.php?courseid=' . $courseid . '">' . get_string('teachernavconfig', 'block_exabis_competences') . '</a>';
		//            if (block_exabis_competences_isactivated($courseid)) {
		//                //Kurs zugeordnet
		//                $this->content->text.='<br /><img src="' . $CFG->wwwroot . '/blocks/exabis_competences/pix/application_view_tile.png" height="16" width="16" alt="' . get_string("link_edit_activities", "block_exabis_competences") . '" />';
		//                $this->content->text.='<a title="edit" href="' . $CFG->wwwroot . '/blocks/exabis_competences/edit_activities.php?courseid=' . $courseid . '">' . get_string('teachernavactivities', 'block_exabis_competences') . '</a>';
		//                $this->content->text.='<br /><img src="' . $CFG->wwwroot . '/blocks/exabis_competences/pix/group.png" height="16" width="16" alt="' . get_string("teachernavstudents", "block_exabis_competences") . '" />';
		//                $this->content->text.='<a title="assign studetns" href="' . $CFG->wwwroot . '/blocks/exabis_competences/assign_competences.php?courseid=' . $courseid . '">' . get_string('teachernavstudents', 'block_exabis_competences') . '</a>';
		//                $this->content->text.='<br /><img src="' . $CFG->wwwroot . '/blocks/exabis_competences/pix/page_white_stack.png" height="16" width="16" alt="' . get_string("teachertabassigncompetencesdetail", "block_exabis_competences") . '" />';
		//                $this->content->text.='<a title="assign studetns" href="' . $CFG->wwwroot . '/blocks/exabis_competences/edit_students.php?courseid=' . $courseid . '">' . get_string('teachertabassigncompetencesdetail', 'block_exabis_competences') . '</a>';
		//                $this->content->text.='<br /><img src="' . $CFG->wwwroot . '/blocks/exabis_competences/pix/doc_offlice.png" height="16" width="16" alt="' . get_string("teachertabassigncompetenceexamples", "block_exabis_competences") . '" />';
		//                $this->content->text.='<a title="assign studetns" href="' . $CFG->wwwroot . '/blocks/exabis_competences/view_examples.php?courseid=' . $courseid . '">' . get_string('teachertabassigncompetenceexamples', 'block_exabis_competences') . '</a>';
		//            }
		//        }
		//        //Schülerbereich
		//        if (has_capability('block/exabis_competences:student', $context) && $courseid != 1) {
		//            if (!empty($this->content->text))
			//                $this->content->text .= '<br />';
		//            if (block_exabis_competences_isactivated($courseid)) {
		//                $this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/exabis_competences/pix/chart_bar.png" height="16" width="16" alt="' . get_string("studentnavcompetences", "block_exabis_competences") . '" />';
		//                $this->content->text.='<a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/exabis_competences/assign_competences.php?courseid=' . $courseid . '">' . get_string('studentnavcompetences', 'block_exabis_competences') . '</a>';
		//            }
		//        }
		//        return $this->content;
		$courseid = $COURSE->id;
		if (has_capability('block/desp:admin', $context)) {
			$this->content = new stdClass;
			$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/config.gif" height="16" width="16" alt="' . get_string("import", "block_desp") . '" />';
			$this->content->text.=' <a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/desp/import.php">' . get_string('import', 'block_desp') . '</a><br/>';
		}

		$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/homedesp.gif" height="16" width="16" alt="' . get_string("index", "block_desp") . '" />';
		$this->content->text.=' <a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/desp/index.php?courseid=' . $courseid . '">' . get_string('index', 'block_desp') . '</a><br/>';
		/*
		 $this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/sprachenbiografie.gif" height="16" width="16" alt="' . get_string("sprachenbiografie", "block_desp") . '" />';
		$this->content->text.=' <a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/desp/sprachenbiografie.php?courseid=' . $courseid . '">' . get_string('sprachenbiografie', 'block_desp') . '</a><br/>';
		//Biografie
		$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/blank.png"  alt="blank" />';
		$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/lerngeschichte.gif" height="16" width="16" alt="' . get_string("sprachlerngeschichte", "block_desp") . '" />';
		$this->content->text.=' <a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/desp/sprachlerngeschichten.php?courseid=' . $courseid . '">' . get_string('sprachlerngeschichte', 'block_desp') . '</a><br/>';

		$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/blank.png"  alt="blank" />';
		$this->content->text.='  <img src="' . $CFG->wwwroot . '/blocks/desp/images/lernplaene.gif" height="16" width="16" alt="' . get_string("sprachlernplaene", "block_desp") . '" />';
		$this->content->text.=' <a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/desp/sprachlernplaene.php?courseid=' . $courseid . '">' . get_string('sprachlernplaene', 'block_desp') . '</a><br/>';

		$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/blank.png"  alt="blank" />';
		$this->content->text.='  <img src="' . $CFG->wwwroot . '/blocks/desp/images/checklisten.gif" height="16" width="16" alt="' . get_string("sprachenchecklisten", "block_desp") . '" />';
		$this->content->text.=' <a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/desp/sprachenchecklisten.php?courseid=' . $courseid . '">' . get_string('sprachenchecklisten', 'block_desp') . '</a><br/>';

		$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/blank.png"  alt="blank" />';
		$this->content->text.='  <img src="' . $CFG->wwwroot . '/blocks/desp/images/checklisten.gif" height="16" width="16" alt="' . get_string("lernpartner_einschaetzung", "block_desp") . '" />';
		$this->content->text.=' <a title="Lernpartner einsch&auml;tzen" href="' . $CFG->wwwroot . '/blocks/desp/lernpartner_einschaetzung.php?courseid=' . $courseid . '">' . get_string('lernpartner_einschaetzung', 'block_desp') . '</a><br/>';

		$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/blank.png"  alt="blank" />';
		$this->content->text.='  <img src="' . $CFG->wwwroot . '/blocks/desp/images/erforschen.gif" height="16" width="16" alt="' . get_string("sprachenundkulturen", "block_desp") . '" />';
		$this->content->text.=' <a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/desp/sprachenundkulturen.php?courseid=' . $courseid . '">' . get_string('sprachenundkulturen', 'block_desp') . '</a><br/>';

		$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/blank.png"  alt="blank" />';
		$this->content->text.='  <img src="' . $CFG->wwwroot . '/blocks/desp/images/lerntipps.gif" height="16" width="16" alt="' . get_string("lerntips", "block_desp") . '" />';
		$this->content->text.=' <a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/desp/lerntips.php?courseid=' . $courseid . '">' . get_string('lerntips', 'block_desp') . '</a><br/>';
		*/
		global $DB;
		$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/sprachenpass.gif" height="16" width="16" alt="' . get_string("sprachenpass", "block_desp") . '" />';
		$this->content->text.=' <a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/desp/sprachenpass.php?courseid=' . $courseid . '">' . get_string('sprachenpass', 'block_desp') . '</a><br/>';
		$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/home.gif" height="16" width="16" alt="' . get_string("sprachenbiografie", "block_desp") . '" />';
		$this->content->text.=' <a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/desp/sprachenbiografie.php?courseid=' . $courseid . '">' . get_string('sprachenbiografie', 'block_desp') . '</a><br/>';
		if($DB->get_record('block',array("name"=>"exaport"))) {
			$this->content->text.='<img src="' . $CFG->wwwroot . '/blocks/desp/images/dossier.gif" height="16" width="16" alt="' . get_string("dossier", "block_desp") . '" />';
			$this->content->text.=' <a title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/exaport/view_items.php?courseid=' . $courseid . '">' . get_string('dossier', 'block_desp') . '</a>';
		}
		if (file_exists("pix/message_ses1.gif")){
			$this->content->text.='<br/><img class="lehrerbegleitheft" src="' . $CFG->wwwroot . '/blocks/desp/images/begleitheft.gif" height="16" width="16" alt="' . get_string("lehrerinnenbegleitheft", "block_desp") . '" />';
			$this->content->text.=' <a class="lehrerbegleitheft" title="evaluate competences" href="' . $CFG->wwwroot . '/blocks/desp/LLL_ESP_M_2012_komplett.pdf">' . get_string('lehrerinnenbegleitheft', 'block_desp') . '</a>';
		}
	}

}

// Here's the closing curly bracket for the class definition
// and here's the closing PHP tag from the section above.
?>