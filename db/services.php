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
 * Youtube services definition
 *
 * @package     mod_youtubewpt
 * @copyright   2022 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'mod_youtubewpt_completecompletion' => [
        'classname' => 'mod_youtubewpt\external\completion',
        'classpath' => 'mod/youtubewpt/classes/external/completion.php',
        'methodname' => 'complete',
        'description' => 'Mark activity as completed',
        'type' => 'write',
        'ajax' => true
    ],
    'mod_youtubewpt_trackprogress' => [
        'classname' => 'mod_youtubewpt\external\progress',
        'classpath' => 'mod/youtubewpt/classes/external/progress.php',
        'methodname' => 'track',
        'description' => 'Saves the tracked video cue points',
        'type' => 'write',
        'ajax' => true
    ]
];
