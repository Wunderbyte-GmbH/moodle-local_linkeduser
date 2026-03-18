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
 * Page: bulk-add linked logins for users whose username matches a regex.
 *
 * @package     local_linkeduser
 * @copyright   2024 Wunderbyte GmbH <info@wunderbyte.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_linkeduser\form\bulkaddlinkedlogin_form;
use local_linkeduser\updatelinkedlogin;

// Sets up the page, checks login, checks the site:config capability.
admin_externalpage_setup('local_linkeduser_bulkaddlinkedlogin');

$form = new bulkaddlinkedlogin_form();

if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php', ['section' => 'local_linkeduser']));
} else if ($data = $form->get_data()) {
    $result = updatelinkedlogin::bulk_add_linkedlogin_for_matching_users($data->usernameregex);

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('bulkaddlinkedlogin', 'local_linkeduser'));

    $added   = $result['added'];
    $skipped = $result['skipped'];
    $message = get_string('bulkaddlinkedlogin_result', 'local_linkeduser', (object)[
        'added'   => $added,
        'skipped' => $skipped,
    ]);
    echo $OUTPUT->notification($message, \core\output\notification::NOTIFY_SUCCESS);

    $continueurl = new moodle_url('/local/linkeduser/bulkaddlinkedlogin.php');
    echo $OUTPUT->single_button($continueurl, get_string('back'), 'get');

    echo $OUTPUT->footer();
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('bulkaddlinkedlogin', 'local_linkeduser'));

    $form->display();

    echo $OUTPUT->footer();
}
