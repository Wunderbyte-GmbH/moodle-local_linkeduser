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
 * Form for bulk-adding linked logins by username regex.
 *
 * @package     local_linkeduser
 * @copyright   2024 Wunderbyte GmbH <info@wunderbyte.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_linkeduser\form;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Form to bulk-add linked logins for all users whose username matches a regex.
 *
 * @package     local_linkeduser
 * @copyright   2024 Wunderbyte GmbH <info@wunderbyte.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class bulkaddlinkedlogin_form extends \moodleform {

    /**
     * Define the form fields.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement(
            'text',
            'usernameregex',
            get_string('usernameregex', 'local_linkeduser'),
            ['size' => 80]
        );
        $mform->setType('usernameregex', PARAM_RAW);
        $mform->setDefault('usernameregex', '^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$');
        $mform->addRule('usernameregex', null, 'required', null, 'client');
        $mform->addHelpButton('usernameregex', 'usernameregex', 'local_linkeduser');

        $this->add_action_buttons(true, get_string('bulkaddlinkedlogin_submit', 'local_linkeduser'));
    }

    /**
     * Validate that the supplied regex is a valid PHP regular expression.
     *
     * @param array $data  submitted form data
     * @param array $files submitted files (unused)
     * @return array       keyed error messages
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!empty($data['usernameregex'])) {
            // Wrap in delimiters and test the pattern.
            if (@preg_match('~' . $data['usernameregex'] . '~', '') === false) {
                $errors['usernameregex'] = get_string('invalidregex', 'local_linkeduser');
            }
        }

        return $errors;
    }
}
