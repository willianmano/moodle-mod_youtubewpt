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
 * CLI script: delete youtubewpt_cuelogs for users who already completed the activity.
 *
 * Usage:
 *   php mod/youtubewpt/cli/purge_completed_cuelogs.php [--dry-run] [--help]
 *
 * Options:
 *   --dry-run   Report what would be deleted without making any changes.
 *   --help      Show this help text and exit.
 *
 * @package     mod_youtubewpt
 * @copyright   2026 Willian Mano <willianmanoaraujo@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

[$options, $unrecognised] = cli_get_params(
    ['dry-run' => false, 'help' => false],
    ['n' => 'dry-run', 'h' => 'help']
);

if ($unrecognised) {
    $unrecognised = implode("\n  ", $unrecognised);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
}

if ($options['help']) {
    cli_writeln("Delete youtubewpt_cuelogs for users who already completed the activity.

Usage:
    php mod/youtubewpt/cli/purge_completed_cuelogs.php [--dry-run]

Options:
    -n, --dry-run   Report what would be deleted without making any changes.
    -h, --help      Show this help text and exit.
");
    exit(0);
}

$dryrun = (bool) $options['dry-run'];

if ($dryrun) {
    cli_writeln('Running in DRY-RUN mode — no records will be deleted.');
}

cli_writeln('');

// Fetch all (youtubewptid, userid) pairs where the user has COMPLETION_COMPLETE.
$sql = "SELECT cmc.id, y.id AS youtubewptid,
               cmc.userid
          FROM {course_modules_completion} cmc
          JOIN {course_modules} cm  ON cm.id       = cmc.coursemoduleid
          JOIN {modules}        m   ON m.id         = cm.module
          JOIN {youtubewpt}     y   ON y.id          = cm.instance
         WHERE m.name             = 'youtubewpt'
           AND cmc.completionstate = :completionstate";

$completedrows = $DB->get_records_sql($sql, ['completionstate' => 1]);

if (empty($completedrows)) {
    cli_writeln('No completed youtubewpt activities found. Nothing to do.');
    exit(0);
}

cli_writeln(count($completedrows) . ' completed user/activity pair(s) found.');
cli_writeln('');

$totaldeleted = 0;

foreach ($completedrows as $row) {
    $count = $DB->count_records('youtubewpt_cuelogs', [
        'youtubewptid' => (int) $row->youtubewptid,
        'userid'       => (int) $row->userid,
    ]);

    if ($count === 0) {
        continue;
    }

    cli_writeln(sprintf(
        '  youtubewptid=%d  userid=%d  —  %d record(s) %s',
        $row->youtubewptid,
        $row->userid,
        $count,
        $dryrun ? 'would be deleted' : 'deleted'
    ));

    if (!$dryrun) {
        $DB->delete_records('youtubewpt_cuelogs', [
            'youtubewptid' => (int) $row->youtubewptid,
            'userid'       => (int) $row->userid,
        ]);
    }

    $totaldeleted += $count;
}

cli_writeln('');

if ($totaldeleted === 0) {
    cli_writeln('All cuelogs for completed users are already clean.');
} else if ($dryrun) {
    cli_writeln($totaldeleted . ' record(s) would be deleted. Run without --dry-run to apply.');
} else {
    cli_writeln($totaldeleted . ' record(s) deleted successfully.');
}

exit(0);
