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

use core_user;

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
     * Return the configured OAuth2 issuer ID.
     *
     * Reads the 'issuerid' plugin setting and falls back to 1 when the setting
     * has not been saved yet (e.g. immediately after plugin installation), so
     * that existing behaviour is preserved.
     *
     * @return int
     */
    private static function get_configured_issuer_id(): int {
        $issuerid = (int)get_config('local_linkeduser', 'issuerid');
        return $issuerid > 0 ? $issuerid : 1;
    }

    /**
     *
     * This function compares the entry for a given user with the oauth table.
     * If the 'useidpemail' setting is enabled, the email from the Identity Provider
     * (auth_oauth2_linked_login) is written back to the user table.
     * Otherwise (default), the oauth table is updated to match the user table.
     *
     * When the 'idpusernameprefix' setting is configured, the Identity Provider
     * username is derived by prepending the prefix to the local username (lowercased).
     * For example, with prefix "tinit_" and local username "rssmra98d08h501h", the
     * linked login record will use the username "tinit_rssmra98d08h501h".
     *
     * @param int $userid
     *
     * @return bool
     */
    public static function update_linkedlogin(int $userid):bool {

        global $DB, $USER;

        if ($user = $DB->get_record('user', ['id' => $userid, 'auth' => 'oauth2'])) {
            return false;
        }

        $user = core_user::get_user($userid);

        // Determine the expected IdP username, applying any configured prefix.
        $idpusernameprefix = get_config('local_linkeduser', 'idpusernameprefix');
        $idpusernameprefix = !empty($idpusernameprefix) ? trim($idpusernameprefix) : '';
        $expectedidpusername = strtolower($idpusernameprefix . $user->username);

        if (!$loginuser = $DB->get_record('auth_oauth2_linked_login', ['userid' => $userid])) {

            // If we don't have a login yet, we create it.

            $now = time();
            $issuerid = self::get_configured_issuer_id();

            $newuser = (object)[
                'userid' => $user->id,
                'email' => $user->email,
                'username' => $expectedidpusername,
                'issuerid' => $issuerid,
                'timecreated' => $now,
                'timemodified' => $now,
                'confirmtoken' => '',
                'confirmtokenexpires' => 0,
                'usermodified' => $USER->id,
            ];

            $DB->insert_record('auth_oauth2_linked_login', $newuser);

            return false;
        }

        $useidpemail = get_config('local_linkeduser', 'useidpemail');

        if ((bool)$useidpemail) {
            // Use the Identity Provider email: update the user table from the linked login record.
            if ($user->email !== $loginuser->email) {
                $user->email = $loginuser->email;
                $DB->update_record('user', $user);
            }
        } else {
            // Default: keep local email and update the linked login record to match the user table.
            if ($user->email !== $loginuser->email
                || $loginuser->username !== $expectedidpusername) {
                $loginuser->email = $user->email;
                $loginuser->username = $expectedidpusername;

                $DB->update_record('auth_oauth2_linked_login', $loginuser);
            }
        }

        return true;
    }

    /**
     * Add a linked login record for every non-deleted user whose username
     * matches the given PHP regular expression.
     *
     * Already-linked users are left unchanged and counted as skipped.
     * The configured 'idpusernameprefix' is applied exactly as in
     * update_linkedlogin().
     *
     * @param string $regex  PHP regex pattern (without delimiters), e.g.
     *                       '^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$'
     * @return array         Associative array with keys 'added' and 'skipped'
     */
    public static function bulk_add_linkedlogin_for_matching_users(string $regex): array {
        global $DB, $USER;

        $idpusernameprefix = get_config('local_linkeduser', 'idpusernameprefix');
        $idpusernameprefix = !empty($idpusernameprefix) ? trim($idpusernameprefix) : '';
        $issuerid = self::get_configured_issuer_id();

        // Retrieve all active, non-deleted users.
        $users = $DB->get_records('user', ['deleted' => 0, 'suspended' => 0], '', 'id, username, email');

        $added   = 0;
        $skipped = 0;

        foreach ($users as $user) {
            // Skip guest and admin-type accounts that have no real username.
            if (isguestuser($user->id)) {
                continue;
            }

            if (@preg_match('~' . $regex . '~', $user->username) !== 1) {
                continue;
            }

            // Skip users that already have a linked login record.
            if ($DB->record_exists('auth_oauth2_linked_login', ['userid' => $user->id])) {
                $skipped++;
                continue;
            }

            $now = time();
            $expectedidpusername = strtolower($idpusernameprefix . $user->username);

            $newlogin = (object)[
                'userid'               => $user->id,
                'email'                => $user->email,
                'username'             => $expectedidpusername,
                'issuerid'             => $issuerid,
                'timecreated'          => $now,
                'timemodified'         => $now,
                'confirmtoken'         => '',
                'confirmtokenexpires'  => 0,
                'usermodified'         => $USER->id,
            ];

            $DB->insert_record('auth_oauth2_linked_login', $newlogin);
            $added++;
        }

        return ['added' => $added, 'skipped' => $skipped];
    }
}
