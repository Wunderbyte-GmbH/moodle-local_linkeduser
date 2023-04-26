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
 * class to do the work.
 *
 * @package local_linkeduser
 * @author Georg Maißer
 * @copyright 2023 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_linkeduser;

defined('MOODLE_INTERNAL') || die();

/**
 * Class updatelinkedlogin
 *
 * @author GHeorg Maißer
 * @copyright 2023 Wunderbyte GmbH
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class updatelinkedlogin {

    /**
     *
     * This function compares the entry for a given user with the oauth table.
     * If we find the email or username are not the same, update the oauth table.
     *
     * @param int $userid
     *
     * @return bool
     */
    public static function update_linkedlogin(int $userid):bool {

        global $DB, $USER;

        if (!$user = $DB->get_record('user', ['id' => $userid, 'auth' => 'oauth2'])) {
            return false;
        }
        if (!$loginuser = $DB->get_record('auth_oauth2_linked_login', ['userid' => $userid])) {

            // If we don't have a login yet, we create it.

            $now = time();

            $newuser = (object)[
                'userid' => $user->id,
                'email' => $user->email,
                'username' => $user->username,
                'issuerid' => 1,
                'timecreated' => $now,
                'timemodified' => $now,
                'confirmtoken' => '',
                'confirmtokenexpires' => 0,
                'usermodified' => $USER->id,
            ];

            $DB->insert_record('auth_oauth2_linked_login', $newuser);

            return false;
        }

        // If the new user e-mail is not the same as the old one...
        if ($user->email !== $loginuser->email
            || $user->username !== $loginuser->username) {
            // Update the email in the login table.

            $loginuser->email = $user->email;
            $loginuser->username = $user->username;

            $DB->update_record('auth_oauth2_linked_login', $loginuser);
        }

        return true;
    }
}
