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
 * LTI Management renderable class.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_skill5\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * LTI Management page renderable.
 */
class lti_management implements renderable, templatable {
    /** @var stdClass LTI tool record. */
    private $tool;

    /** @var string Admin email. */
    private $adminemail;

    /** @var string Entity user ID. */
    private $entityuserid;

    /**
     * Constructor.
     *
     * @param stdClass $tool LTI tool record.
     * @param string $adminemail Admin email.
     * @param string $entityuserid Entity user ID.
     */
    public function __construct($tool, $adminemail, $entityuserid) {
        $this->tool = $tool;
        $this->adminemail = $adminemail;
        $this->entityuserid = $entityuserid;
    }

    /**
     * Export data for template.
     *
     * @param renderer_base $output The renderer.
     * @return stdClass Data for template.
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;

        $data = new stdClass();

        // Connection details section.
        $data->connectiondetails = new stdClass();
        $data->connectiondetails->ltitoolinfo = new stdClass();
        $data->connectiondetails->ltitoolinfo->clientid = $this->tool->clientid;

        $data->connectiondetails->skill5userinfo = new stdClass();
        $data->connectiondetails->skill5userinfo->adminemail = $this->adminemail;
        $data->connectiondetails->skill5userinfo->entityuserid = $this->entityuserid;

        // Next steps section.
        $data->nextsteps = new stdClass();

        // Step 1.
        $data->nextsteps->step1 = new stdClass();
        $managetoolsurl = new \moodle_url('/mod/lti/toolconfigure.php');
        $managetoolslink = \html_writer::link($managetoolsurl, get_string('managetools_link_text', 'local_skill5'));
        $data->nextsteps->step1->instruction = get_string('step1_instruction', 'local_skill5', $managetoolslink);

        // Step 2.
        $data->nextsteps->step2 = new stdClass();
        $data->nextsteps->step2->instructions = [
            get_string('step2_instruction_1', 'local_skill5'),
            get_string('step2_instruction_2', 'local_skill5'),
            get_string('step2_instruction_3', 'local_skill5'),
            get_string('step2_instruction_4', 'local_skill5'),
        ];

        return $data;
    }
}
