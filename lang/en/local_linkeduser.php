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
 * Web service template plugin related strings
 * @package   local_linkeduser
 * @copyright 2019 David Bogner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Create OAuth2 linked users';
$string['useidpemail'] = 'Use Identity Provider email';
$string['useidpemail_desc'] = 'When enabled, the email address from the OAuth2 Identity Provider will be written to the Moodle user table. When disabled (default), the existing local email address in the user table is kept and the linked login record is updated to match it.';
$string['idpusernameprefix'] = 'Identity Provider username prefix';
$string['idpusernameprefix_desc'] = 'If your Identity Provider adds a prefix to usernames (e.g. "tinit_"), enter it here. The plugin will prepend this prefix to the local Moodle username (lowercased) when creating or updating the OAuth2 linked login record. For example, if the local username is "rssmra98d08h501h" and the prefix is "tinit_", the linked login username will be stored as "tinit_rssmra98d08h501h". Leave empty if usernames match exactly.';
$string['bulkaddlinkedlogin'] = 'Bulk-add linked logins by username pattern';
$string['bulkaddlinkedlogin_link'] = 'Open bulk-add linked logins page';
$string['bulkaddlinkedlogin_submit'] = 'Add linked logins';
$string['bulkaddlinkedlogin_result'] = 'Done. Linked logins added: {$a->added}. Users already linked (skipped): {$a->skipped}.';
$string['usernameregex'] = 'Username regular expression';
$string['usernameregex_help'] = 'Enter a PHP regular expression (without delimiters) to match usernames. All non-deleted, non-suspended users whose username matches this pattern will have a linked login record created if they do not already have one. Example default pattern matches the Italian fiscal code format.';
$string['invalidregex'] = 'The regular expression is not valid.';
