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

/**
 * The task that provides all the steps to perform a complete backup is defined here.
 *
 * @package     mod_youtubewpt
 * @category    backup
 * @copyright   2022 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

require_once($CFG->dirroot.'//mod/youtubewpt/backup/moodle2/backup_youtubewpt_stepslib.php');
require_once($CFG->dirroot.'//mod/youtubewpt/backup/moodle2/backup_youtubewpt_settingslib.php');

/**
 * Provides all the settings and steps to perform a complete backup of mod_youtubewpt.
 */
class backup_youtubewpt_activity_task extends backup_activity_task {

    /**
     * Defines particular settings for the plugin.
     */
    protected function define_my_settings() {
        return;
    }

    /**
     * Defines particular steps for the backup process.
     */
    protected function define_my_steps() {
        $this->add_step(new backup_youtubewpt_activity_structure_step('youtubewpt_structure', 'youtubewpt.xml'));
    }

    /**
     * Codes the transformations to perform in the activity in order to get transportable (encoded) links.
     *
     * @param string $content
     * @return string
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Link to the list of choices.
        $search = "/(".$base."\//mod\/youtubewpt\/index.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@YOUTUBEWPTINDEX*$2@$', $content);

        // Link to choice view by moduleid.
        $search = "/(".$base."\//mod\/youtubewpt\/view.php\?id\=)([0-9]+)/";
        $content = preg_replace($search, '$@YOUTUBEWPTVIEWBYID*$2@$', $content);

        return $content;
    }
}
