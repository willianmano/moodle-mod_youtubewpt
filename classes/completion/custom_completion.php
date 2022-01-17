<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

declare(strict_types=1);

namespace mod_youtubewpt\completion;

use core_completion\activity_custom_completion;
use mod_youtubewpt\util\cuepoint;

/**
 * Activity custom completion subclass for the Assign Tutor activity.
 *
 * Class for defining mod_youtubewpt's custom completion rules and fetching the completion statuses
 * of the custom completion rules for a given instance and a user.
 *
 * @package     mod_youtubewpt
 * @copyright   2022 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class custom_completion extends activity_custom_completion {

    /**
     * Fetches the completion state for a given completion rule.
     *
     * @param string $rule The completion rule.
     * @return int The completion state.
     */
    public function get_state(string $rule): int {
        global $DB;

        $this->validate_rule($rule);

        $youtubewptid = $this->cm->instance;

        if (!$youtubewpt = $DB->get_record('youtubewpt', ['id' => $youtubewptid])) {
            throw new \moodle_exception('Unable to find youtubewpt with id ' . $youtubewptid);
        }

        if ($rule == 'completionprogress') {
            $requiredprogress = $youtubewpt->completionprogress;

            $cueutil = new cuepoint();

            $userhighcuepoint = $cueutil->get_high_user_cuepoint($youtubewptid, $this->userid);

            if ($userhighcuepoint >= $requiredprogress) {
                return COMPLETION_COMPLETE;
            }
        }

        return COMPLETION_INCOMPLETE;
    }

    /**
     * Fetch the list of custom completion rules that this module defines.
     *
     * @return array
     */
    public static function get_defined_custom_rules(): array {
        return ['completionprogress'];
    }

    /**
     * Returns an associative array of the descriptions of custom completion rules.
     *
     * @return array
     */
    public function get_custom_rule_descriptions(): array {
        global $DB;

        $youtubewptid = $this->cm->instance;

        if (!$youtubewpt = $DB->get_record('youtubewpt', ['id' => $youtubewptid])) {
            throw new \moodle_exception('Unable to find youtubewpt with id ' . $youtubewptid);
        }

        return [
            'completionprogress' => get_string('completionprogress_ruledesc', 'mod_youtubewpt', $youtubewpt->completionprogress)
        ];
    }

    /**
     * Returns an array of all completion rules, in the order they should be displayed to users.
     *
     * @return array
     */
    public function get_sort_order(): array {
        return [
            'completionview',
            'completionprogress'
        ];
    }
}
