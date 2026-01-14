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
 * API manager class for Skill5 plugin.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_skill5;

/**
 * API manager class for Skill5 integration.
 *
 * Handles all communication with the Skill5 API.
 *
 * @package    local_skill5
 * @copyright  2025 Skill5
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_manager {
    /** @var string Skill5 API base URL. */
    private const SKILL5_URL = 'https://app.skill5.com';

    /**
     * Returns the base URL for the Skill5 API.
     *
     * @return string
     */
    public static function get_skill5_url(): string {
        return self::SKILL5_URL;
    }

    /**
     * Returns the API JWT Secret from configuration.
     *
     * @return string
     * @throws \moodle_exception if secret is not configured
     */
    private static function get_api_jwt_secret(): string {
        $secret = get_config('local_skill5', 'api_jwt_secret');

        if (empty($secret)) {
            throw new \moodle_exception('error_api_jwt_secret', 'local_skill5');
        }

        return $secret;
    }

    /**
     * Fetches all users associated with a Moodle entity.
     *
     * @return array|null The list of users or null on failure.
     * @throws \moodle_exception
     */
    public static function get_users(): ?array {
        // The API now expects the admin's Entity User ID to identify the Moodle instance.
        $adminentityuserid = get_config('local_skill5', 'entityuserid');

        if (empty($adminentityuserid)) {
            throw new \moodle_exception('error_entity_user_id', 'local_skill5');
        }

        $endpoint = self::SKILL5_URL . '/api/plugins/moodle/admin/users';
        $params = ['entityUserId' => $adminentityuserid];

        $responsedata = self::send_request($endpoint, $params, 'GET');

        if (empty($responsedata->data)) {
            return [];
        }

        return $responsedata->data;
    }

    /**
     * Fetches details for a specific user.
     *
     * @param string $entityuserid The entity user ID.
     * @return \stdClass|null The user details or null on failure.
     * @throws \moodle_exception If API request fails.
     */
    public static function get_user_details(string $entityuserid): ?\stdClass {
        $endpoint = self::SKILL5_URL . '/api/plugins/moodle/admin/users/' . $entityuserid;

        return self::send_request($endpoint, [], 'GET');
    }

    /**
     * Fetches the EntityUser ID from the Skill5 API for a given admin email.
     *
     * @param string $adminemail The admin email address.
     * @return string The entity user ID.
     * @throws \moodle_exception If API request fails.
     */
    public static function get_entity_user_id(string $adminemail): string {
        $endpoint = self::SKILL5_URL . '/api/plugins/moodle/register/info/entity-user';
        $payload = ['email' => $adminemail];

        $responsedata = self::send_request($endpoint, $payload, 'POST');

        if (empty($responsedata->entityUserId)) {
            throw new \moodle_exception('error_invalid_response', 'local_skill5');
        }

        return $responsedata->entityUserId;
    }

    /**
     * Registers the Moodle instance on the Skill5 application.
     *
     * @param string $clientid The LTI client ID.
     * @param string $entityuserid The entity user ID.
     * @return void
     * @throws \moodle_exception If API request fails.
     */
    public static function register_moodle_on_skill5_app(string $clientid, string $entityuserid): void {
        global $CFG;
        $endpoint = self::SKILL5_URL . '/api/plugins/moodle/register';
        $payload = [
            'clientId' => $clientid,
            'issuer' => $CFG->wwwroot,
            'entityUserId' => $entityuserid,
        ];

        self::send_request($endpoint, $payload, 'POST', [200, 201]);
    }

    /**
     * Sends a request to the Skill5 API.
     *
     * @param string $endpoint The API endpoint URL.
     * @param array $params The data/parameters for the request.
     * @param string $method The HTTP method (GET, POST, etc.).
     * @param array $expectedcodes Expected successful HTTP status codes.
     * @return mixed The decoded JSON response.
     * @throws \moodle_exception If request fails.
     */
    private static function send_request(string $endpoint, array $params = [], string $method = 'GET',
            array $expectedcodes = [200]) {
        $curl = new \curl();
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . self::get_api_jwt_secret(),
        ];

        $response = null;
        if ($method === 'POST') {
            $options = ['httpheader' => $headers];
            $response = $curl->post($endpoint, json_encode($params), $options);
        } else if ($method === 'GET') {
            $curl->setopt(['CURLOPT_HTTPHEADER' => $headers]);
            $response = $curl->get($endpoint, $params);
        }

        if ($response === false || $curl->get_errno()) {
            throw new \moodle_exception('error_curl_request', 'local_skill5', '', null, $curl->get_error());
        }

        if (!in_array($curl->info['http_code'], $expectedcodes)) {
            $errordata = (object)['endpoint' => $endpoint, 'httpcode' => $curl->info['http_code'], 'response' => $response];
            throw new \moodle_exception('error_api_request', 'local_skill5', '', $errordata);
        }

        return json_decode($response);
    }
}
