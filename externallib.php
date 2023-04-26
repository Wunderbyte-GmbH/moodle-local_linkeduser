<?php

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
 * Create a linked login in order to prevent automatic linked login creation
 * by Moodle
 *
 * @package    local_linkeduser
 * @copyright  2019 David Bogner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\oauth2;
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->libdir . "/classes/oauth2/api.php");

class local_linkeduser_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function create_linked_user_parameters() {
        return new external_function_parameters(
                array(
                    'userid' => new external_value(PARAM_INT, 'The user id of the user a linked user is created for', VALUE_DEFAULT, 0),
                    'clientid' => new external_value(PARAM_ALPHANUMEXT, 'The client id of the issuer (OAuth2 Identity provider)', VALUE_DEFAULT, '')
                )
        );
    }

    /**
     * Create a linked login for the user identiefied by $userid.
     * This is used to prevent automatic creation of linked logins for
     * OAuth2 identity providers, that use same email addresses for
     * multiple users.
     *
     * @param array $params params for the linked user
     * @param string $clientid The OAuth2 client id
     * @return bool success
     */
    public static function create_linked_user($userid = 0, $clientid) {
        global $USER, $DB;

        // Parameter validation.
        $params = self::validate_parameters(self::create_linked_user_parameters(),
            array(
                'userid' => $userid,
                'clientid' => $clientid
            ));

        // Context validation.
        $context = context_user::instance($USER->id);
        self::validate_context($context);

        // Capability checking.
        if (!has_capability('moodle/user:create', $context)) {
            throw new moodle_exception('cannotupdateprofile');
        }
        $issuerid = $DB->get_field_select('oauth2_issuer', 'id', 'clientid = :clientid', array('clientid' => $params['clientid']));
        $issuer = \core\oauth2\api::get_issuer($issuerid);
        $user = $DB->get_record_select('user', 'id = :userid', array('userid' => $params['userid']), 'username, email');
        $userinfo = [];
        $userinfo['email'] = $user->email;
        $userinfo['username'] = $user->username;
        \auth_oauth2\api::link_login($userinfo, $issuer, $userid, true);

        return true;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function create_linked_user_returns() {
        return new external_value(PARAM_BOOL, 'Success');
    }
}