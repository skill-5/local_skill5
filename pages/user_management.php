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
 * User management page for Skill5 plugin.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Ensure the user has the required capability.
admin_externalpage_setup('local_skill5_user_management');

// Check if the tool is configured. If not, redirect to the landing page.
$tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);
if (!$tool) {
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}

// Start page output.
echo $OUTPUT->header();

$heading = get_string('usermanagement', 'local_skill5');
echo $OUTPUT->heading($heading);

// Fetch users from the Skill5 API.
try {
    $users = local_skill5\api_manager::get_users();
} catch (Exception $e) {
    echo $OUTPUT->notification(get_string('error_fetch_users', 'local_skill5') . ': ' . $e->getMessage());
    echo $OUTPUT->footer();
    exit;
}

// Render using template.
$renderable = new \local_skill5\output\user_management($users);
echo $OUTPUT->render($renderable);

// End page output.
echo $OUTPUT->footer();
