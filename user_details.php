<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * User details page for Skill5 plugin.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Get the user ID from the URL.
$entityuserid = required_param('id', PARAM_ALPHANUMEXT);

// Ensure the user has the required capability.
admin_externalpage_setup('local_skill5_user_management');

// Start page output.
echo $OUTPUT->header();

// Fetch user details from the Skill5 API.
try {
    $userdetails = local_skill5\api_manager::get_user_details($entityuserid);
} catch (Exception $e) {
    echo $OUTPUT->notification(get_string('error_fetch_user_details', 'local_skill5') . ': ' . $e->getMessage());
    echo $OUTPUT->footer();
    exit;
}

if (empty($userdetails)) {
    echo $OUTPUT->notification(get_string('error_user_not_found', 'local_skill5'));
    echo $OUTPUT->footer();
    exit;
}

// Display user's basic info.
$heading = get_string('user_details_heading', 'local_skill5', htmlspecialchars($userdetails->name));
echo $OUTPUT->heading($heading);

echo $OUTPUT->box_start();
echo '<p><strong>' . get_string('email') . ':</strong> ' . htmlspecialchars($userdetails->email) . '</p>';
echo $OUTPUT->box_end();

// Course Progress Table.
echo $OUTPUT->heading(get_string('course_progress', 'local_skill5'), 3);
$progresstable = new html_table();
$progresstable->head = [get_string('course', 'local_skill5'), get_string('progress'), get_string('completed_at', 'local_skill5')];

if (!empty($userdetails->courseProgress)) {
    foreach ($userdetails->courseProgress as $enrollment) {
        $completedat = $enrollment->completedAt ? userdate(strtotime($enrollment->completedAt)) : get_string('not_completed', 'local_skill5');
        $progresstable->data[] = new html_table_row([
            htmlspecialchars($enrollment->name),
            htmlspecialchars($enrollment->progress),
            $completedat,
        ]);
    }
} else {
    $cell = new html_table_cell(get_string('no_course_progress', 'local_skill5'));
    $cell->colspan = 3;
    $row = new html_table_row([$cell]);
    $progresstable->data[] = $row;
}
echo html_writer::table($progresstable);

// Badges Table.
echo $OUTPUT->heading(get_string('badges', 'local_skill5'), 3);
$badgestable = new html_table();
$badgestable->head = [get_string('badge', 'local_skill5'), get_string('issued_at', 'local_skill5')];

if (!empty($userdetails->badges)) {
    foreach ($userdetails->badges as $badge) {
        $badgestable->data[] = new html_table_row([
            htmlspecialchars($badge->name),
            userdate(strtotime($badge->createdAt)),
        ]);
    }
} else {
    $cell = new html_table_cell(get_string('no_badges', 'local_skill5'));
    $cell->colspan = 2;
    $row = new html_table_row([$cell]);
    $badgestable->data[] = $row;
}
echo html_writer::table($badgestable);

// Certificates Table.
echo $OUTPUT->heading(get_string('certificates', 'local_skill5'), 3);
$certstable = new html_table();
$certstable->head = [get_string('certificate', 'local_skill5'), get_string('issued_at', 'local_skill5')];

if (!empty($userdetails->certificates)) {
    foreach ($userdetails->certificates as $certificate) {
        $certstable->data[] = new html_table_row([
            htmlspecialchars($certificate->name),
            userdate(strtotime($certificate->createdAt)),
        ]);
    }
} else {
    $cell = new html_table_cell(get_string('no_certificates', 'local_skill5'));
    $cell->colspan = 2;
    $row = new html_table_row([$cell]);
    $certstable->data[] = $row;
}
echo html_writer::table($certstable);

// End page output.
echo $OUTPUT->footer();
