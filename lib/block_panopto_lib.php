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
 * @package block_panopto
 * @copyright  Panopto 2009 - 2015 /With contributions from Spenser Jones (sjones@ambrose.edu)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Prepend the instance name to the Moodle course ID to create an external ID for Panopto Focus.
 */
function panopto_decorate_course_id($moodlecourseid) {
    global $CFG;
    return ($CFG->block_panopto_instance_name . ":" . $moodlecourseid);
}

/**
 * Decorate a moodle username with the instancename outside the context of a panopto_data object.
 */
function panopto_decorate_username($moodleusername) {
    global $CFG;
    return ($CFG->block_panopto_instance_name . "\\" . $moodleusername);
}

/**
 * Sign the payload with the proof that it was generated by trusted code.
 */
function panopto_generate_auth_code($payload) {
    global $CFG;
    $index = 1;
    for ($x = 0; $x < 10; $x++) {
        $cfgservername = 'block_panopto_server_name' . ($x + 1);
        if (isset($CFG->$cfgservername)) {
            $thisservername = $CFG->$cfgservername;
            if (strpos($payload, $thisservername)) {
                $index = $x + 1;
                break;
            }
        }
    }

    $appstring = 'block_panopto_application_key' . $index;
    $sharedsecret = $CFG->$appstring;

    $signedpayload = $payload . "|" . $sharedsecret;

    $authcode = strtoupper(sha1($signedpayload));

    return $authcode;
}

/**
 * Ensures auth code is valid
 */
function panopto_validate_auth_code($payload, $authcode) {
    return (panopto_generate_auth_code($payload) == $authcode);
}

/* End of file block_panopto_lib.php */