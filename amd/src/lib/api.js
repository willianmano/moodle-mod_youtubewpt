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
 * Web service wrappers for mod_youtubewpt.
 *
 * @module    mod_youtubewpt/lib/api
 * @copyright 2026 Willian Mano {@link https://conecti.me}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';

/**
 * Track a cuepoint for the current user.
 *
 * Returns a native Promise so callers can await completion when needed
 * (e.g. the final 100 % cuepoint before reloading the page). Errors are
 * not propagated — a failed log should never interrupt playback.
 *
 * @param {object} options
 * @param {number} options.cmid     Course module id.
 * @param {number} options.cuepoint Progress percentage reached (10, 20 … 100).
 * @returns {Promise<void>}
 */
export const trackProgress = ({cmid, cuepoint}) =>
    Promise.resolve(Ajax.call([{
        methodname: 'mod_youtubewpt_trackprogress',
        args: {cmid, cuepoint},
    }])[0]).catch(() => {
        // Swallow errors so a failed log never interrupts playback.
    });
