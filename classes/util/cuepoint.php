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

namespace mod_youtubewpt\util;

/**
 * Cue point utility classclass.
 *
 * @package     mod_youtubewpt
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class cuepoint {
    public function addpoint($youtubewptid, $cuepoint) {
        global $DB, $USER;

        $data = new \stdClass();
        $data->youtubewptid = $youtubewptid;
        $data->userid = $USER->id;
        $data->cuepoint = $cuepoint;
        $data->timecreated = time();

        $id = $DB->insert_record('youtubewpt_cuelogs', $data);

        return $id;
    }

    public function get_high_user_cuepoint($youtubewptid, $userid) {
        global $DB;

        $sql = 'SELECT * FROM {youtubewpt_cuelogs}
                WHERE youtubewptid = :youtubewptid AND userid = :userid 
                ORDER BY cuepoint DESC LIMIT 1';

        $record = $DB->get_record_sql($sql, ['youtubewptid' => $youtubewptid, 'userid' => $userid]);

        if (!$record) {
            return false;
        }

        return $record->cuepoint;
    }
}
