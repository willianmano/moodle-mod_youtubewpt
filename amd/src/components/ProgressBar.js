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
 * ProgressBar component — thin bar below the video player.
 *
 * @module    mod_youtubewpt/components/ProgressBar
 * @copyright 2026 Willian Mano {@link https://conecti.me}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {html} from 'mod_youtubewpt/lib/preact';

/**
 * Thin progress bar that reflects video watch progress.
 *
 * @param {object} props
 * @param {number} props.progress  0–100 percentage watched.
 * @returns {object} vnode
 */
export default function ProgressBar({progress}) {
    const pct = Math.min(100, Math.max(0, progress));

    return html`
        <div class="progress youtubewpt-progress" style="height: 12px;">
            <div
                class="progress-bar bg-primary"
                role="progressbar"
                style=${'width: ' + pct + '%;'}
                aria-valuenow=${pct}
                aria-valuemin="0"
                aria-valuemax="100">
            </div>
        </div>
    `;
}
