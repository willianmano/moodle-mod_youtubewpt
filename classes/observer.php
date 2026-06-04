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

namespace mod_youtubewpt;

/**
 * Event observers for mod_youtubewpt.
 *
 * @package     mod_youtubewpt
 * @copyright   2026 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {
    /**
     * Delete cue-log records for a user once their activity is marked complete.
     *
     * Fires on \core\event\course_module_completion_updated. Only acts when the
     * new state is COMPLETION_COMPLETE and the module is a youtubewpt instance.
     *
     * @param \core\event\course_module_completion_updated $event
     * @return void
     */
    public static function course_module_completion_updated(
        \core\event\course_module_completion_updated $event
    ): void {
        global $DB;

        if (!self::is_completion_completed($event->objectid)) {
            return;
        }

        $cm = get_coursemodule_from_id('youtubewpt', $event->contextinstanceid);
        if (!$cm) {
            return;
        }

        $DB->delete_records('youtubewpt_cuelogs', [
            'youtubewptid' => (int) $cm->instance,
            'userid' => (int) $event->relateduserid,
        ]);
    }

    /**
     * Verify if the completion is completed
     *
     * @param int $cmcid
     *
     * @return boolean
     */
    protected static function is_completion_completed($cmcid) {
        global $DB;

        $cmc = $DB->get_record('course_modules_completion', ['id' => $cmcid], 'id, completionstate');

        if ($cmc) {
            return (bool) $cmc->completionstate;
        }

        return false;
    }
}
