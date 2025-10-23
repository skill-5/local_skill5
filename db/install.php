<?php
/**
 * Post installation hook for local_skill5 plugin.
 * 
 * This function is called immediately after the plugin is installed.
 *
 * @package    local_skill5
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Post installation procedure.
 * 
 * This function is executed right after the plugin installation is complete.
 * It sets a flag to redirect the user to the plugin's landing page.
 */
function xmldb_local_skill5_install() {
    // Set a config flag to indicate that we should redirect after installation
    set_config('redirect_after_install', 1, 'local_skill5');
    
    return true;
}
