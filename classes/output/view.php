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

namespace mod_youtubewpt\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use renderer_base;

/**
 * View renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class view implements renderable, templatable {

    protected $youtubewpt;
    protected $context;
    protected $coursemodule;

    public function __construct($youtubewpt, $context, $coursemodule) {
        $this->youtubewpt = $youtubewpt;
        $this->context = $context;
        $this->coursemodule = $coursemodule;
    }

    /**
     * Export the data
     *
     * @param renderer_base $output
     *
     * @return array|\stdClass
     *
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        $hascompletion = false;
        if (isset($this->youtubewpt->completionprogress) && $this->youtubewpt->completionprogress > 0) {
            $hascompletion = true;
        }

        return [
            'videoid' => $this->youtubewpt->videoid,
            'intro' => format_module_intro('youtubewpt', $this->youtubewpt, $this->context->instanceid),
            'cmid' => $this->coursemodule->id,
            'hascompletionprogress' => $hascompletion,
        ];
    }
}
