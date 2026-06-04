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
 * PlayerView — mounts the YouTube IFrame player and tracks progress.
 *
 * Controls are disabled so the student cannot seek forward. Progress is
 * tracked at every 10 % via the mod_youtubewpt_trackprogress web service.
 * When the video reaches 100 % the page reloads so Moodle's completion
 * criteria are re-evaluated.
 *
 * @module    mod_youtubewpt/views/PlayerView
 * @copyright 2026 Willian Mano {@link https://conecti.me}
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {html, useState, useEffect, useRef} from 'mod_youtubewpt/lib/preact';
import ProgressBar from 'mod_youtubewpt/components/ProgressBar';
import {trackProgress} from 'mod_youtubewpt/lib/api';

/**
 * Load the YouTube IFrame API script once and resolve when it is ready.
 *
 * Handles multiple simultaneous callers by chaining the global callback.
 *
 * @returns {Promise<void>}
 */
const loadYouTubeApi = () => {
    if (window.YT && typeof window.YT.Player === 'function') {
        return Promise.resolve();
    }

    return new Promise((resolve) => {
        const prev = window.onYouTubeIframeAPIReady;
        window.onYouTubeIframeAPIReady = () => {
            if (typeof prev === 'function') {
                prev();
            }
            resolve();
        };

        if (!document.querySelector('script[src*="youtube.com/iframe_api"]')) {
            const script = document.createElement('script');
            script.src = 'https://www.youtube.com/iframe_api';
            document.head.appendChild(script);
        }
    });
};

/**
 * Top-level player view.
 *
 * @param {object} props
 * @param {object} props.initial  Bootstrap payload from the PHP renderable.
 * @returns {object} vnode
 */
export default function PlayerView({initial}) {
    const cmid              = Number(initial.cmid) || 0;
    const videoid           = String(initial.videoid || '');
    const completionenabled = Boolean(initial.completionenabled);
    const startprogress     = Number(initial.startprogress) || 0;

    const [progress, setProgress] = useState(startprogress);

    const divref       = useRef(null);
    const intervalref  = useRef(null);
    const nextcuepoint = useRef(Math.floor(startprogress / 10) * 10 + 10);

    useEffect(() => {
        if (!videoid) {
            return undefined;
        }

        loadYouTubeApi().then(() => {
            if (!divref.current) {
                return;
            }

            /* global YT */
            new YT.Player(divref.current, { // eslint-disable-line no-undef
                videoId: videoid,
                playerVars: {
                    autoplay: 1,
                    controls: 0,
                    disablekb: 1,
                    modestbranding: 1,
                    rel: 0,
                    fs: 1,
                },
                events: {
                    onReady: (event) => {
                        event.target.playVideo();
                        startTracking(event.target);
                    },
                },
            });
        });

        return () => {
            if (intervalref.current) {
                clearInterval(intervalref.current);
            }
        };
    }, []);

    /**
     * Reload the page bypassing browser cache by appending a timestamp
     * query param, forcing Moodle to re-evaluate the completion state.
     */
    const reloadNocache = () => {
        const sep = window.location.href.includes('?') ? '&' : '?';
        window.location.href = window.location.href + sep + 't=' + Date.now();
    };

    /**
     * Start the 1-second polling interval that drives progress tracking.
     *
     * @param {object} player  YT.Player instance.
     */
    const startTracking = (player) => {
        intervalref.current = setInterval(() => {
            const currenttime = (player.getCurrentTime() || 0) + 1;
            const duration    = player.getDuration() || 0;

            if (!duration) {
                return;
            }

            const currentprogress = Math.min(100, Math.round((currenttime * 100) / duration));
            setProgress(currentprogress);

            if (currentprogress >= 100) {
                clearInterval(intervalref.current);

                if (completionenabled) {
                    // Await the final cuepoint being persisted before reloading
                    // so Moodle has time to mark the activity as complete.
                    trackProgress({cmid, cuepoint: 100}).finally(reloadNocache);
                } else {
                    reloadNocache();
                }
                return;
            }

            if (completionenabled && currentprogress >= nextcuepoint.current) {
                const cuepoint = nextcuepoint.current;
                nextcuepoint.current += 10;
                trackProgress({cmid, cuepoint});
            }
        }, 1000);
    };

    return html`
        <div class="youtubewpt-wrap">
            <div class="youtubewpt-player-container ratio ratio-16x9 mb-1">
                <div ref=${divref}></div>
            </div>
            <${ProgressBar} progress=${progress} />
        </div>
    `;
}
