<?php
/*
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// include your composer autoloader
include 'vendor/autoload.php';

use Aspsms\Aspsms;

// define your
define('USER_KEY', '<YOUR_KEY_FROM_ASPSMS.NET>');
define('USER_PASS', '<YOUR_PASSWORD_FROM_ASPSMS.NET>');

// set optional attributes
$options = array(
    "AffiliateId" => "205567",
    "Originator" => "PHP ASPSMS CLASS",
);

// array with numbers and the generated unique tracking code. You should store this informations
// to a database to request tracking informations later on.
$recipients = array(
    "4565-".uniqid(microtime()) => "0041 079 123 45 65",
    "5678-".uniqid(microtime()) => "0041 079 123 56 78",
    "8789-".uniqid(microtime()) => "0043 078 665 87 89",
);

// create the aspsms object with they user_key, user_pass and options
$aspsms = new Aspsms(USER_KEY, USER_PASS, $options);

// send the message to the network
if (!$aspsms->sendTextSms("Hello everyone, we are now able to send SMS via ASPSMS.net! Regards", $recipients)) {
    echo "<p>Something went wrong while sending your Message to ASPSMS.net!</p>";
    echo $aspsms->getSendStatus();
}
