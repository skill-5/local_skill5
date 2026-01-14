/**
 * Gruntfile for local_skill5 plugin.
 *
 * This Gruntfile loads Moodle's core Gruntfile to use the standard AMD build process.
 * When run from within a Moodle installation, it will use Moodle's Grunt tasks.
 *
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* eslint-env node */

/**
 * Grunt configuration.
 *
 * @param {Grunt} grunt The Grunt instance.
 */
module.exports = function(grunt) {
    const path = require('path');
    const process = require('process');
    const fs = require('fs');

    // Store the plugin directory.
    const pluginDir = process.cwd();

    // Try to find Moodle root (go up from local/skill5 to moodle root).
    const moodleRoot = path.resolve(pluginDir, '..', '..');
    const moodleGruntfile = path.join(moodleRoot, 'Gruntfile.js');

    if (fs.existsSync(moodleGruntfile)) {
        // We are inside a Moodle installation - use Moodle's Gruntfile.
        grunt.log.ok('Loading Moodle Gruntfile from: ' + moodleRoot);

        // Change to Moodle root directory.
        process.chdir(moodleRoot);

        // Load Moodle's Gruntfile.
        require(moodleGruntfile)(grunt);

        // Change back to plugin directory.
        process.chdir(pluginDir);
    } else {
        // Standalone mode - register minimal tasks for local development.
        grunt.log.warn('Moodle Gruntfile not found. Running in standalone mode.');
        grunt.log.warn('For full AMD build, install the plugin in Moodle and run grunt from there.');

        // Register empty tasks to prevent errors.
        grunt.registerTask('amd', 'Build AMD modules (requires Moodle)', function() {
            grunt.log.error('AMD build requires Moodle installation.');
            grunt.log.error('Please install the plugin in Moodle and run:');
            grunt.log.error('  cd /path/to/moodle && npx grunt amd --root=local/skill5');
        });

        grunt.registerTask('default', ['amd']);
    }

    // Stylelint task (empty for plugins without SCSS).
    grunt.registerTask('stylelint', []);
};
