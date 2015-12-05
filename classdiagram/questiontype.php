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
 * Question type class for the classdiagram question type.
 *
 * @package    qtype
 * @subpackage classdiagram
 * @copyright  THEYEAR YOURNAME (YOURCONTACTINFO)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/classdiagram/question.php');


/**
 * The classdiagram question type.
 *
 * @copyright  2015 DHUVIK (8792797269)

 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_classdiagram extends question_type {

    public function move_files($questionid, $oldcontextid, $newcontextid) {
        parent::move_files($questionid, $oldcontextid, $newcontextid);
        $this->move_files_in_hints($questionid, $oldcontextid, $newcontextid);
    }

    protected function delete_files($questionid, $contextid) {
        parent::delete_files($questionid, $contextid);
        $this->delete_files_in_hints($questionid, $contextid);
    }

    public function save_question_options($question) {
        global $DB;
        $result = new stdClass();
        $context = $question->context;
          // Fetch old answer ids so that we can reuse them
        $oldanswers = $DB->get_records('question_classdiagram',
                array('question' => $question->id), 'id ASC');
        // Save the answer - update an existing answer if possible.
        $answer = array_shift($oldanswers);
        if (!$answer) {
            $answer = new stdClass();
            $answer->question = $question->id;
            $answer->answer = 0;
            $answer->class_marks = $question->class_marks;
            $answer->class_name_marks = $question->class_name_marks;
            $answer->class_attribute_marks = $question->class_attribute_marks;
            $answer->class_extra_marks = $question->class_extra_marks;
            $answer->association_marks = $question->association_marks;
            $answer->association_type_marks = $question->association_type_marks ;
             $answer->association_cardinality_marks = $question->association_cardinality_marks;
             $answer->association_extra_marks = $question->association_extra_marks;
              $answer->generalization_marks = $question->generalization_marks;
               $answer->generalization_type_marks = $question->generalization_type_marks;
            $answer->generalization_extra_marks = $question->generalization_extra_marks;
           
            $answer->id = $DB->insert_record('question_classdiagram', $answer);
        }

         $oldanswers = $DB->get_records('question_answers',
                array('question' => $question->id), 'id ASC');

        $answer = array_shift($oldanswers);
        if (!$answer) {
            $answer = new stdClass();
            $answer->question = $question->id;
            $answer->answer = '';
            $answer->feedback = '';
            $answer->id = $DB->insert_record('question_answers', $answer);
        }
        //update answer table
        $answer->answer   = $question->userfile;
        $answer->fraction = $question->defaultmark;
        $answer->feedbackformat = $question->generalfeedback['format'];
        $DB->update_record('question_answers', $answer);
        $trueid = $answer->id;
               
        
        // Delete any left over old answer records.
        $fs = get_file_storage();
        foreach ($oldanswers as $oldanswer) {
            $fs->delete_area_files($context->id, 'question', 'answerfeedback', $oldanswer->id);
            $DB->delete_records('question_answers', array('id' => $oldanswer->id));
        }

        // Save question options in question_truefalse table
        if ($options = $DB->get_record('question_classdiagram', array('question' => $question->id))) {
            // No need to do anything, since the answer IDs won't have changed
            // But we'll do it anyway, just for robustness
            $options->answer  = $trueid;
            $DB->update_record('question_classdiagram', $options);
        } else {
            $options = new stdClass();
            $options->question    = $question->id;
            $options->answer  = $trueid;
            $DB->insert_record('question_classdiagram', $options);
        }

        $this->save_hints($question);

        return true;
    }




    /*public function save_question_options($question) {
        $this->save_hints($question);
    }*/

    /**
     * Loads the question type specific options for the question.
     */
    public function get_question_options($question) {
        global $DB, $OUTPUT;
        // Get additional information from database
        // and attach it to the question object
        if (!$question->options = $DB->get_record('question_classdiagram',
                array('question' => $question->id))) {
            echo $OUTPUT->notification('Error: Missing question options!');
            return false;
        }
        // Load the answers
        if (!$question->options->answers = $DB->get_records('question_answers',
                array('question' =>  $question->id), 'id ASC')) {
            echo $OUTPUT->notification('Error: Missing question answers for classdiag question ' .
                    $question->id . '!');
            return false;
        }
        
          
        return true;
    }

	protected function initialise_question_instance(question_definition $question, $questiondata) {
		parent::initialise_question_instance($question, $questiondata);
		        global $DB;
		$answers = $questiondata->options->answers;
		$question->generalfeedback =  $answers[$questiondata->options->answer]->feedback;
		        $question->generalfeedbackformat =
		        $answers[$questiondata->options->answer]->feedbackformat;
		$question->answerid =  $questiondata->options->answer;
		//$question->rightanswer = 10;
		 $getanswers = $DB->get_records('question_answers',array('question' => $question->id), 'id ASC');
		 $answerval = array_shift($getanswers);
		 if($answerval)
		 $question->rightanswer = $answerval->answer;

		 $getanswers = $DB->get_records('question_classdiagram',array('question' => $question->id), 'id ASC');
		 $answerval = array_shift($getanswers);
		 if($answerval)
		 {
		 	$question->class_marks = $answerval->class_marks;
			$question->class_name_marks = $answerval->class_name_marks;
			$question->class_attribute_marks = $answerval->class_attribute_marks;
			$question->class_extra_marks = $answerval->class_extra_marks;
			$question->association_marks = $answerval->association_marks;
			$question->association_type_marks = $answerval->association_type_marks;
			$question->association_cardinality_marks = $answerval->association_cardinality_marks;
			$question->association_extra_marks = $answerval->association_extra_marks;
			$question->generalization_marks = $answerval->generalization_marks;
			$question->generalization_type_marks = $answerval->generalization_type_marks;
			$question->generalization_extra_marks = $answerval->generalization_extra_marks;
		 }
		 
	    }

	 public function delete_question($questionid, $contextid) {
		global $DB;
		$DB->delete_records('question_classdiagram', array('question' => $questionid));

		parent::delete_question($questionid, $contextid);
	    }
    /*protected function initialise_question_instance(question_definition $question, $questiondata) {
        // TODO.
        parent::initialise_question_instance($question, $questiondata);
    }*/
	
    public function get_random_guess_score($questiondata) {
        return 0.5;
    }
	
	
	
    /*public function get_random_guess_score($questiondata) {
        // TODO.
        return 0;
    }*/

    public function get_possible_responses($questiondata) {
        // TODO.
        return array();
    }
}
