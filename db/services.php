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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    local_linkeduser
 * @copyright  2019 David Bogner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
        'local_linkeduser_create_linked_user' => array(
                'classname'   => 'local_linkeduser_external',
                'methodname'  => 'create_linked_user',
                'classpath'   => 'local/linkeduser/externallib.php',
                'description' => 'Create a linked user for an OAuth2 identity provider',
                'type'        => 'write',
                'capabilities' => 'moodle/user:create'
        )
);
