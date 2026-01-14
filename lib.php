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
 * Library functions for Skill5 plugin.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Hook called after require_login is executed.
 * Used to redirect user to plugin landing page after installation.
 */
function local_skill5_after_require_login() {
    global $CFG;

    // Check if we should redirect after installation.
    $redirectflag = get_config('local_skill5', 'redirect_after_install');

    if ($redirectflag) {
        // Clear the flag so we don't redirect again.
        unset_config('redirect_after_install', 'local_skill5');

        // Check if we're not already on the landing page using SCRIPT_NAME.
        $currentscript = $_SERVER['SCRIPT_NAME'] ?? '';
        if (strpos($currentscript, '/local/skill5/pages/landing.php') === false) {
            redirect(new moodle_url('/local/skill5/pages/landing.php'));
        }
    }
}
