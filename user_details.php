<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

// Get the user ID from the URL.
$entity_user_id = required_param('id', PARAM_RAW);

// Ensure the user has the required capability.
admin_externalpage_setup('local_skill5_user_management'); // Re-using the same capability.

// Start page output.
echo $OUTPUT->header();

// Fetch user details from the Skill5 API.
try {
    $user_details = local_skill5\api_manager::get_user_details($entity_user_id);
} catch (Exception $e) {
    echo $OUTPUT->notification('Error fetching user details from Skill5: ' . $e->getMessage());
    echo $OUTPUT->footer();
    exit;
}

if (empty($user_details)) {
    echo $OUTPUT->notification('User not found.');
    echo $OUTPUT->footer();
    exit;
}

// Display user's basic info.
$heading = 'User Details: ' . htmlspecialchars($user_details->name);
echo $OUTPUT->heading($heading);

echo $OUTPUT->box_start();
echo '<p><strong>Email:</strong> ' . htmlspecialchars($user_details->email) . '</p>';
echo $OUTPUT->box_end();

// --- Course Progress Table ---
echo $OUTPUT->heading('Course Progress', 3);
$progress_table = new html_table();
$progress_table->head = ['Course', 'Progress', 'Completed At'];

if (!empty($user_details->courseProgress)) {
    foreach ($user_details->courseProgress as $enrollment) {
        $completed_at = $enrollment->completedAt ? userdate(strtotime($enrollment->completedAt)) : '-';
        $progress_table->data[] = new html_table_row([
            htmlspecialchars($enrollment->name),
            htmlspecialchars($enrollment->progress),
            $completed_at
        ]);
    }
} else {
    $cell = new html_table_cell('No course progress found.');
    $cell->colspan = 3;
    $row = new html_table_row([$cell]);
    $progress_table->data[] = $row;
}
echo html_writer::table($progress_table);

// --- Badges Table ---
echo $OUTPUT->heading('Badges', 3);
$badges_table = new html_table();
$badges_table->head = ['Badge', 'Issued At'];

if (!empty($user_details->badges)) {
    foreach ($user_details->badges as $badge) {
        $badges_table->data[] = new html_table_row([
            htmlspecialchars($badge->name),
            userdate(strtotime($badge->createdAt))
        ]);
    }
} else {
    $cell = new html_table_cell('No badges found.');
    $cell->colspan = 2;
    $row = new html_table_row([$cell]);
    $badges_table->data[] = $row;
}
echo html_writer::table($badges_table);

// --- Certificates Table ---
echo $OUTPUT->heading('Certificates', 3);
$certs_table = new html_table();
$certs_table->head = ['Certificate', 'Issued At'];

if (!empty($user_details->certificates)) {
    foreach ($user_details->certificates as $certificate) {
        $certs_table->data[] = new html_table_row([
            htmlspecialchars($certificate->name),
            userdate(strtotime($certificate->createdAt))
        ]);
    }
} else {
    $cell = new html_table_cell('No certificates found.');
    $cell->colspan = 2;
    $row = new html_table_row([$cell]);
    $certs_table->data[] = $row;
}
echo html_writer::table($certs_table);

// End page output.
echo $OUTPUT->footer();
