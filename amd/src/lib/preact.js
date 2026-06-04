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
 * Single import surface for the vendored Preact + htm bundle.
 *
 * Reads the globals exposed by block_feedback_tracker's vendor bundle
 * (bftPreact / bftPreactHooks / bftHtm). All components in this plugin
 * import from here — never read window.bft* directly.
 *
 * @module    mod_youtubewpt/lib/preact
 * @copyright 2026 Willian Mano {@link https://conecti.me}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Read a vendored global, throwing if the bundle was not loaded yet.
 *
 * @param {string} name  The bft-prefixed global name.
 * @returns {object}
 */
const need = (name) => {
    if (typeof window === 'undefined' || !window[name]) {
        throw new Error('mod_youtubewpt: ' + name + ' not loaded — '
            + 'is block_feedback_tracker\'s vendor bundle included before any AMD module?');
    }
    return window[name];
};

const preact = need('bftPreact');
const hooks  = need('bftPreactHooks');
const htm    = need('bftHtm');

export const h          = preact.h;
export const render     = preact.render;
export const Fragment   = preact.Fragment;
export const Component  = preact.Component;

export const useState    = hooks.useState;
export const useEffect   = hooks.useEffect;
export const useRef      = hooks.useRef;
export const useCallback = hooks.useCallback;

export const html = htm.bind(preact.h);
