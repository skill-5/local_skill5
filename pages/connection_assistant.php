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
 * Connection assistant page for Skill5 plugin.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/skill5/classes/api_manager.php');

admin_externalpage_setup('local_skill5_connection_assistant');


$context = context_system::instance();
require_capability('moodle/site:config', $context);

$PAGE->set_url('/local/skill5/connection_assistant.php');
$PAGE->set_title(get_string('connectionassistant', 'local_skill5'));
$PAGE->set_heading(get_string('connectionassistant', 'local_skill5'));

echo $OUTPUT->header();

// Check if an email is provided in the URL.
$email_from_url = optional_param('email', '', PARAM_EMAIL);

// Check if the tool is configured. If not, and no email is provided, redirect to the landing page.
$tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);
if (!$tool && empty($email_from_url)) {
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}

// Check if the tool is already created.
$tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);

if ($tool) {
    // --- STATE: CONNECTED ---
    // Display Skill5 User Info.
    $admin_email = get_config('local_skill5', 'admin_email');
    $admin_name = get_config('local_skill5', 'admin_name');
    $entityuser_id = get_config('local_skill5', 'entityuserid');

    // Render using template.
    $renderable = new \local_skill5\output\connection_assistant($admin_name, $admin_email, $entityuser_id);
    echo $OUTPUT->render($renderable);
} else if (!empty($email_from_url)) {
    // --- STATE: EMAIL RECEIVED, AUTO-CONNECTING ---
    // Save the email and proceed to the connection script.
    set_config('admin_email', $email_from_url, 'local_skill5');
    redirect(new moodle_url('/local/skill5/connect.php'));
} else {
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}

echo $OUTPUT->footer();
