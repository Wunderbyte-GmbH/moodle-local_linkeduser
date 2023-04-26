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
 * Event observers used in forum.
 *
 * @package    local_linkeduser
 * @copyright  2023 Gerog Maißer <info@wunderbyte.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_linkeduser\shopping_cart_history;
use local_linkeduser\updatelinkedlogin;

/**
 * Event observer for local_linkeduser.
 */
class local_linkeduser_observer {

    /**
     * Triggered via payment_error event from any payment provider
     * If we receive a payment error, check for the order id in our shopping cart history.
     * And set it to error, if it was pending.
     *
     * @param \core\event\base $event
     * @return bool
     */
    public static function user_updated(\core\event\base $event): string {
        $data = $event->get_data();

        if (!$userid = $data['relateduserid']) {
            return false;
        }

        updatelinkedlogin::update_linkedlogin($userid);

        return true;
    }
}
