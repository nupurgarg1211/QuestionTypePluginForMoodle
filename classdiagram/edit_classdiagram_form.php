<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Defines the editing form for the classdiagram question type.
 *
 * @package    qtype
 * @subpackage classdiagram
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/question/type/edit_question_form.php');

/**
 * classdiagram question editing form definition.
 *
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_classdiagram_edit_form extends question_edit_form {

    protected function definition_inner($mform) {
       // $this->add_interactive_settings();
       $areaid = 1;
       $mform->addElement('filepicker','userfile','Upload XMI File');
       $mform->addRule('userfile', null, 'required', null, 'client');
         $mform->addElement('button', 'intro', get_string("buttonlabel"));
       $mform->addElement('text','class_marks','Percentage of mark for Class');
         $mform->addElement('text','class_name_marks','Percentage of marks to cut for wrong Class Name');
       $mform->addElement('text','class_attribute_marks','Percentage of marks to cut for wrong Attribute Name');
         $mform->addElement('text','class_extra_marks','Percentage of marks to cut for Extra Class');
         $mform->addElement('text','association_marks','Percentage of mark for Association');
         $mform->addElement('text','association_type_marks','Percentage of marks to cut for wrong Association Type');
       $mform->addElement('text','association_cardinality_marks','Percentage of marks to cut for wrong Cardinality');
        $mform->addElement('text','association_extra_marks','Percentage of marks to cut for Extra Association');
         $mform->addElement('text','generalization_marks','Percentage of mark for Generalization');
         $mform->addElement('text','generalization_type_marks','Percentage of marks to cut for wrong Generalization Type');
        $mform->addElement('text','generalization_extra_marks','Percentage of marks to cut for Extra Subclass');
       $mform->addRule('userfile', null, 'required', null, 'client');
       $mform->addRule('class_marks', null, 'required', null, 'client');
       $mform->addRule('association_marks', null, 'required', null, 'client');
       $mform->addRule('generalization_marks', null, 'required', null, 'client');
      $mform->setDefault('class_name_marks','50');
      $mform->setDefault('class_attribute_marks','50');
       $mform->setDefault('association_type_marks','50');
      $mform->setDefault('association_cardinality_marks','50');
      $mform->setDefault('class_extra_marks','0');
      $mform->setDefault('association_extra_marks','0');
      $mform->setDefault('generalization_type_marks','0');
      $mform->setDefault('generalization_extra_marks','0');
		
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        //$question = $this->data_preprocessing_hints($question);

        return $question;
    }

    public function qtype() {
        return 'classdiagram';
    }
}
