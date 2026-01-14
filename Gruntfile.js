/**
 * Gruntfile for local_skill5 plugin.
 *
 * This Gruntfile is intentionally minimal. The plugin uses Moodle's standard
 * AMD build process. When running from within a Moodle installation, use:
 *   cd /path/to/moodle && npx grunt amd --root=local/skill5
 *
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* eslint-env node */

module.exports = function(grunt) {
    // Stylelint task (empty for plugins without SCSS).
    grunt.registerTask('stylelint', []);
};
