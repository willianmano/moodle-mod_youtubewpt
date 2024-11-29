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

namespace mod_youtubewpt\external;

use core_external\external_api;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_function_parameters;
use mod_youtubewpt\util\cuepoint;

/**
 * Completion external api class.
 *
 * @package     mod_youtubewpt
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class progress extends external_api {
    /**
     * Complete parameters
     *
     * @return external_function_parameters
     */
    public static function track_parameters() {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'The course module id'),
            'cuepoint' => new external_value(PARAM_INT, 'The cue point progress'),
        ]);
    }

    /**
     * Complete method
     *
     * @param int $cmid
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function track($cmid, $cuepoint) {
        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::track_parameters(), ['cmid' => $cmid, 'cuepoint' => $cuepoint]);

        list ($course, $cm) = get_course_and_cm_from_cmid($cmid, 'youtubewpt');

        $context = \core\context\course::instance($course->id);

        if (!is_enrolled($context)) {
            return ['status' => 'notenrolled'];
        }

        $cueutil = new cuepoint();

        $cueutil->addpoint($cm->instance, $cuepoint);

        $completion = new \completion_info($course);
        $completion->update_state($cm, COMPLETION_COMPLETE);

        return ['status' => 'ok'];
    }

    /**
     * Complete return fields
     *
     * @return external_single_structure
     */
    public static function track_returns() {
        return new external_single_structure(
            [
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
            ]
        );
    }
}
