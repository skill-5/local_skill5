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
 * LTI management page for Skill5 plugin.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_skill5_lti_management');

$context = context_system::instance();
require_capability('moodle/site:config', $context);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('ltimanagement', 'local_skill5'));

// Fetch connection data.
$tool = $DB->get_record('lti_types', ['name' => 'Skill5 LTI Tool']);
$adminemail = get_config('local_skill5', 'admin_email');
$entityuserid = get_config('local_skill5', 'entityuserid');

if ($tool && $adminemail && $entityuserid) {
    // Render using template.
    $renderable = new \local_skill5\output\lti_management($tool, $adminemail, $entityuserid);
    echo $OUTPUT->render($renderable);
} else {
    // If the tool is not fully configured, redirect to the initial setup page.
    redirect(new moodle_url('/local/skill5/pages/landing.php'));
}

echo $OUTPUT->footer();
