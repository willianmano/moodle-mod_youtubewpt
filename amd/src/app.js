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
 * mod_youtubewpt AMD entry point.
 *
 * Finds every [data-youtubewpt-root] mount point, reads the bootstrap
 * payload from the embedded <script type="application/json"> tag and
 * mounts the PlayerView component. Idempotent — safe to call multiple times.
 *
 * @module    mod_youtubewpt/app
 * @copyright 2026 Willian Mano {@link https://conecti.me}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {render, html} from 'mod_youtubewpt/lib/preact';
import PlayerView from 'mod_youtubewpt/views/PlayerView';

/**
 * Parse the JSON payload embedded in a mount-point root element.
 *
 * @param {HTMLElement} root
 * @returns {object|null}
 */
const readPayload = (root) => {
    const tag = root.querySelector('script[type="application/json"][data-youtubewpt-init]');
    if (!tag) {
        return null;
    }
    try {
        return JSON.parse(tag.textContent || '{}');
    } catch (e) {
        // eslint-disable-next-line no-console
        console.error('mod_youtubewpt: payload JSON parse failed', e);
        return null;
    }
};

/**
 * Initialise every player on the page. Idempotent.
 */
export const init = () => {
    if (window.youtubewptAppInitDone) {
        return;
    }
    window.youtubewptAppInitDone = true;

    document.querySelectorAll('[data-youtubewpt-root]').forEach((root) => {
        const initial = readPayload(root);
        if (!initial) {
            return;
        }
        render(html`<${PlayerView} initial=${initial} />`, root);
    });
};
