<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Hook called after require_login is executed.
 * Used to redirect user to plugin landing page after installation.
 */
function local_skill5_after_require_login() {
    global $CFG;
    
    // Check if we should redirect after installation
    $redirect_flag = get_config('local_skill5', 'redirect_after_install');
    
    if ($redirect_flag) {
        // Clear the flag so we don't redirect again
        unset_config('redirect_after_install', 'local_skill5');
        
        // Check if we're not already on the landing page using SCRIPT_NAME
        $current_script = $_SERVER['SCRIPT_NAME'] ?? '';
        if (strpos($current_script, '/local/skill5/pages/landing.php') === false) {
            redirect(new moodle_url('/local/skill5/pages/landing.php'));
        }
    }
}
