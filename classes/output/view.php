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

    /** @var \stdClass The youtubewpt instance record. */
    protected $youtubewpt;

    /** @var \context_module The module context. */
    protected $context;

    /** @var \cm_info The course module. */
    protected $coursemodule;

    /**
     * Constructor.
     *
     * @param \stdClass $youtubewpt
     * @param \context_module $context
     * @param \cm_info $coursemodule
     */
    public function __construct($youtubewpt, $context, $coursemodule) {
        $this->youtubewpt = $youtubewpt;
        $this->context = $context;
        $this->coursemodule = $coursemodule;
    }

    /**
     * Export the data for the Mustache template.
     *
     * @param renderer_base $output
     * @return array
     * @throws \dml_exception
     */
    public function export_for_template(renderer_base $output): array {
        global $USER;

        $completionprogress = 0;
        if (!empty($this->youtubewpt->completionprogress)) {
            $completionprogress = (int) $this->youtubewpt->completionprogress;
        }

        $startprogress = 0;
        if ($completionprogress > 0) {
            $cueutil = new \mod_youtubewpt\util\cuepoint();
            $startprogress = (int) $cueutil->get_high_user_cuepoint($this->youtubewpt->id, $USER->id);
        }

        $payload = [
            'cmid'               => (int) $this->coursemodule->id,
            'videoid'            => (string) $this->youtubewpt->videoid,
            'completionenabled'  => $completionprogress > 0,
            'completionprogress' => $completionprogress,
            'startprogress'      => $startprogress,
        ];

        $payloadjson = json_encode($payload, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return [
            'intro'       => format_module_intro('youtubewpt', $this->youtubewpt, $this->context->instanceid),
            'payloadjson' => $payloadjson !== false ? $payloadjson : '{}',
        ];
    }
}
