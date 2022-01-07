/* eslint-disable */

import jQuery from 'jquery';
import Ajax from 'core/ajax';
import Event from 'core/event';
import LocalStorage from 'core/localstorage';
import Notification from 'core/notification';

/** @var {bool} Whether this is the first load of videojs module */
let firstLoad;

/** @var {string} The language that is used in the player */
let language;

/** @var {object} List of languages and translations for the current page */
let langStringCache;

/** @var {integer} Next cue point to be listened */
let  nextcuepoint = 10;

/** @var {integer} The course module id */
let  cmid = 0;

/**
 * Set-up.
 *
 * Adds the listener for the event to then notify video.js.
 * @param {string} lang Language to be used in the player
 * @param {string} selector video selector id
 * @param {string} videoid video url
 * @param {integer} coursemoduleid course module id
 */
export const setUp = (lang, selector, videoid, coursemoduleid) => {
    language = lang;
    firstLoad = true;
    cmid = coursemoduleid;
    // Notify Video.js about the nodes already present on the page.
    notifyVideoJS(selector, videoid);
    // We need to call popover automatically if nodes are added to the page later.
    Event.getLegacyEvents().done((events) => {
        jQuery(document).on(events.FILTER_CONTENT_UPDATED, notifyVideoJS);
    });
};

/**
 * Notify video.js of new nodes.
 * @param {string} selector video selector id
 * @param {string} selector video url
 */
const notifyVideoJS = (selector, videoid) => {
    const config = {
        controlBar: {
            playToggle: true,
            captionsButton: false,
            chaptersButton: false,
            subtitlesButton: false,
            remainingTimeDisplay: true,
            progressControl: {
                seekBar: false
            },
            fullscreenToggle: true,
            playbackRateMenuButton: false,
        },
        autoplay: true,
        youtube: {
            showinfo: 0,
            modestbranding: 1,
            rel: 0
        }
    };
    const langStrings = getLanguageJson();

    const modulePromises = [import('media_videojs/video-lazy'), import('media_videojs/Youtube-lazy')];

    Promise.all([langStrings, ...modulePromises])
        .then(([langJson, videojs]) => {
            if (firstLoad) {
                videojs.options.playbackRates = [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2];
                videojs.options.userActions = {
                    hotkeys: true,
                };
                videojs.addLanguage(language, langJson);

                firstLoad = false;
            }

            videojs(selector, config).ready(function() {
                var myPlayer = this;

                myPlayer.src({ type: 'video/youtube', src: 'https://www.youtube.com/watch?v=' + videoid});

                checkTimerProgress(myPlayer);
            });

            return;
        })
        .catch(Notification.exception);
};

const checkTimerProgress = (player) => {
    let progressinterval = setInterval(() => {
        var currentTime = player.currentTime();
        var videoDuration = player.duration();

        var progress = (currentTime * 100) / videoDuration;

        var progressElement = document.getElementById('progress-bar');
        progressElement.style.width = progress + '%';

        checkCuePointToLog(progress);

        if (progress >= 100) {
            clearInterval(progressinterval);
        }
    }, 500);
}

const checkCuePointToLog = (progress) => {
    if (progress >= nextcuepoint) {
        let currentcuepoint = nextcuepoint;

        Ajax.call([{
            methodname: 'mod_youtubewpt_trackprogress',
            args: {
                cmid: cmid,
                cuepoint: currentcuepoint
            }
        }]);

        // Increase next cue point to be saved in log.
        nextcuepoint += 10;
    }
}

/**
 * Returns the json object of the language strings to be used in the player.
 *
 * @returns {Promise}
 */
const getLanguageJson = () => {
    if (langStringCache) {
        return Promise.resolve(langStringCache);
    }

    const cacheKey = `media_videojs/${language}`;

    const rawCacheContent = LocalStorage.get(cacheKey);
    if (rawCacheContent) {
        const cacheContent = JSON.parse(rawCacheContent);

        langStringCache = cacheContent;

        return Promise.resolve(langStringCache);
    }

    const request = {
        methodname: 'media_videojs_get_language',
        args: {
            lang: language,
        },
    };

    return Ajax.call([request])[0]
        .then(langStringData => {
            LocalStorage.set(cacheKey, langStringData);

            return langStringData;
        })
        .then(result => JSON.parse(result))
        .then(langStrings => {
            langStringCache = langStrings;

            return langStrings;
        });
};