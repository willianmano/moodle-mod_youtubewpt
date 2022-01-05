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

use context;
use external_api;
use external_value;
use external_single_structure;
use external_function_parameters;;

/**
 * Completion external api class.
 *
 * @package     mod_youtubewpt
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class completion extends external_api {
    /**
     * Complete parameters
     *
     * @return external_function_parameters
     */
    public static function complete_parameters() {
        return new external_function_parameters([
            'cmid' => new external_value(PARAM_INT, 'The course module id'),
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
    public static function complete($cmid) {
        global $DB;

        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(self::complete_parameters(), ['cmid' => $cmid]);

        $context = context::instance_by_id($params['contextid'], MUST_EXIST);

        // We always must call validate_context in a webservice.
        self::validate_context($context);


        return [
            'status' => 'ok',
            'message' => get_string('complete_success', 'mod_youtubewpt'),
            'data' => json_encode([])
        ];
    }

    /**
     * Complete return fields
     *
     * @return external_single_structure
     */
    public static function complete_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
                'message' => new external_value(PARAM_RAW, 'Return message'),
                'data' => new external_value(PARAM_RAW, 'Return data')
            )
        );
    }
}
