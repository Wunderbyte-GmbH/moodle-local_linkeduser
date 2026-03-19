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
 * Plugin administration settings are defined here.
 *
 * @package     local_linkeduser
 * @category    admin
 * @copyright   2024 Wunderbyte GmbH <info@wunderbyte.at>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_linkeduser', get_string('pluginname', 'local_linkeduser'));

    // Build the list of available OAuth2 issuers for the dropdown.
    $issueroptions = [];
    try {
        $issuers = $DB->get_records('oauth2_issuer', null, 'name ASC', 'id, name');
        foreach ($issuers as $issuer) {
            $issueroptions[$issuer->id] = $issuer->name;
        }
    } catch (\Exception $e) {
        // Table may not be available during initial install; leave options empty.
        $issueroptions = [];
    }
    if (empty($issueroptions)) {
        $issueroptions[0] = get_string('none');
    }

    $settings->add(new admin_setting_configselect(
        'local_linkeduser/issuerid',
        get_string('issuerid', 'local_linkeduser'),
        get_string('issuerid_desc', 'local_linkeduser'),
        array_key_first($issueroptions),
        $issueroptions
    ));

    $settings->add(new admin_setting_configcheckbox(
        'local_linkeduser/useidpemail',
        get_string('useidpemail', 'local_linkeduser'),
        get_string('useidpemail_desc', 'local_linkeduser'),
        0
    ));

    $settings->add(new admin_setting_configtext(
        'local_linkeduser/idpusernameprefix',
        get_string('idpusernameprefix', 'local_linkeduser'),
        get_string('idpusernameprefix_desc', 'local_linkeduser'),
        '',
        PARAM_TEXT
    ));

    // Link to the bulk-add page.
    $bulkaddurl = new moodle_url('/local/linkeduser/bulkaddlinkedlogin.php');
    $settings->add(new admin_setting_description(
        'local_linkeduser/bulkaddlinkedloginlink',
        get_string('bulkaddlinkedlogin', 'local_linkeduser'),
        html_writer::link($bulkaddurl, get_string('bulkaddlinkedlogin_link', 'local_linkeduser'),
            ['class' => 'btn btn-secondary'])
    ));

    $ADMIN->add('localplugins', $settings);

    // Register the bulk-add page as an external admin page so that
    // admin_externalpage_setup() can locate it and set up navigation.
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_linkeduser_bulkaddlinkedlogin',
        get_string('bulkaddlinkedlogin', 'local_linkeduser'),
        new moodle_url('/local/linkeduser/bulkaddlinkedlogin.php'),
        'moodle/site:config'
    ));
}
