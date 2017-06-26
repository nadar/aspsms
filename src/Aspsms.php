<?php

namespace Aspsms;

/**
 * The Aspsms class provides the basic function to easily send message, check delivery status or
 * show the available amount of credits.
 *
 * @example See in examples/basic_composer_setup.php
 * @package Aspsms
 * @author Basil Suter <git@nadar.io>
 * @see https://github.com/nadar/aspsms
 */
class Aspsms
{
    /**
     * @var string Contains the Aspsms Class Version.
     */
    const VERSION = '1.0.3';

    /**
     * @var string Contains the services url (status 26.06.2017)
     */
    public $server = "https://soap.aspsms.com/aspsmsx2.asmx/";

    /**
     * @var string Contains the userkey which is provided from the aspsms.com webpage under the menu
     * point "USERKEY". Looks somewhat like this FAG9XPAUQLQ3
     */
    private $userkey = null;

    /**
     * @var string The password you use to login on the webpage (aspsms.com), in its blank not encrypted form...
     */
    private $password = null;

    /**
     * @var array All sms status service reason codes which appears to be used when u have a not usual
     * deliver status. There is an optional newsletter from 2009 with some more information.
     * @see http://www.aspsms.de/newsletter/html/en/200905/
     */
    private $deliveryReasonCodes = array(
        "000" => "Unknown Subscriber",
        "001" => "Service temporary not available",
        "009" => "Illegal error code",
        "010" => "Network time-out",
        "100" => "Facility not supported",
        "101" => "Unknown subscriber",
        "102" => "Facility not provided",
        "103" => "Call barred",
        "104" => "Operation barred",
        "105" => "SC congestion",
        "106" => "Facility not supported",
        "107" => "Absent subscriber",
        "108" => "Delivery fail",
        "109" => "SC congestion",
        "110" => "Protocol error",
        "111" => "MS not equipped",
        "112" => "Unknown SC",
        "113" => "SC congestion",
        "114" => "Illegal MS",
        "115" => "MS not a subscriber",
        "116" => "Error in MS",
        "117" => "SMS lower layer not provisioned",
        "118" => "System fail",
        "119" => "PLMN system failure",
        "120" => "HLR system failure",
        "121" => "VLR system failure",
        "122" => "Previous VLR system failure",
        "123" => "Controlling MSC system failure",
        "124" => "VMSC system failure",
        "125" => "EIR system failure",
        "126" => "System failure",
        "127" => "Unexpected data value",
        "200" => "Error in address service centre",
        "201" => "Invalid absolute Validity Period",
        "202" => "Short message exceeds maximum",
        "203" => "Unable to Unpack GSM message",
        "204" => "Unable to convert to IA5 ALPHABET",
        "205" => "Invalid validity period format",
        "206" => "Invalid destination address",
        "207" => "Duplicate message submit",
        "208" => "Invalid message type indicator",
    );

    /**
     * @var array Contains all delivery sms notification status.
     */
    private $deliveryStatusCodes = array(
       -1 => "Not yet submitted or rejected",
        0 => "Delivered",
        1 => "Buffered",
        2 => "Not Delivered",
    );

    /**
     * @var array All known valid response status codes when sending an sms over the aspnet api.
     */
    private $sendStatusCodes = array(
        1 => "Ok",
        2 => "Connect failed.",
        3 => "Authorization failed (wrong userkey and/or password).",
        4 => "Binary file not found. Please check the location.",
        5 => "Not enough Credits available.",
        6 => "Time out error.",
        7 => "Transmission error. Please try it again.",
        8 => "Invalid UserKey. Please check the spelling of the UserKey.",
        9 => "Invalid Password.",
        10 => "Invalid originator. A maximum of 11 characters is allowed for alphanumeric originators.",
        11 => "Invalid message date. Please verify the data.",
        12 => "Invalid binary data. Please verify the data.",
        13 => "Invalid binary file. Please check the file type.",
        14 => "Invalid MCC. Please check the number.",
        15 => "Invalid MNC. Please check the number.",
        16 => "Invalid XSer.",
        17 => "Invalid URL buffered message notification string.",
        18 => "Invalid URL delivery notification string.",
        19 => "Invalid URL non delivery notification string.",
        20 => "Missing a recipient. Please specify at least one recipient.",
        21 => "Missing binary data. Please specify some data.",
        22 => "Invalid deferred delivery time. Please check the format.",
        23 => "Missing transaction reference number.",
        24 => "Service temporarily not available.",
        25 => "User access denied.",
    );

    /**
     * @var string Contains the sms status response value from $sendStatusCodes
     */
    private $sendStatus = null;

    /**
     * @var array Contains all valid option parameters which can be delivered trough option
     * arguments in functions.
     */
    private $validOptions = array(
        "Originator",
        "DeferredDeliveryTime",
        "FlashingSMS",
        "TimeZone",
        "URLBufferedMessageNotification",
        "URLDeliveryNotification",
        "URLNonDeliveryNotification",
        "AffiliateId",
        "MessageText",
        "Recipients",
        "TransactionReferenceNumbers",
    );

    /**
     * @var array All active options with their values, will be flushed after each request
     */
    private $currentOptions = array();

    /**
     * The class constructor contains basic information that is needed for each request type.
     *
     * @param string $userkey The userkey provided from aspsms.net
     * @param string $password The blank passwort from your aspsms.net login
     * @param array $options Basic associativ array, available keys see $validOptions array. Commonly used to provide AffiliateId or Originator.
     */
    public function __construct($userkey, $password, array $options = array())
    {
        // save userkey
        $this->userkey = $userkey;
        // save password
        $this->password = $password;
        // set optional options if any provided
        $this->setOptions($options);
    }

    /**
     * Send a text message to one or more recipients.
     *
     * Usage example without options:
     *
     * ```php
     * sendTextSms("Hello world! I am your message", array(
     *     "0001" => "0041123456789",
     *     "0002" => "0041123456780"
     * ));
     * ```
     *
     * Usage example with options:
     *
     * ```php
     * sendTextSms("Hello world! I am your message", array(
     *     "0001" => "0041123456789",
     *     "0002" => "0041123456780"
     * ), array(
     *     "Originator" => "MYCOMPANY_SENDER_NAME",
     *     "AffiliateId" => "1234567"
     * ));
     * ```
     *
     * @param string $message The message to sent to the recipients.
     * @param array $recipients Array containing the recipients, where the key is the tracking number and the value equals the mobile number. Mobile Number format must be without spaces or +(plus) signs.
     * @param array $options Basic associative array, available keys see $validOptions array. Commonly used to provide AffiliateId or Originator values.
     * @return boolean
     * @throws \Aspsms\Exception
     */
    public function sendTextSms($message, array $recipients, array $options = array())
    {
        // set message option
        $this->setOption("MessageText", $message);
        // set recipients option
        $recipientList = array();
        // collect all recipients with teir
        foreach ($recipients as $tracknr => $number) {
            // according to the docs multiple recipients must look like this: <NUMBER>:<TRACKNR>;<NUMBER>:<TRACKNR>
            $recipientList[] = "$number:$tracknr";
        }
        // se the recipients into the options list
        $this->setOption("Recipients", implode(";", $recipientList));
        // optional options parameter to set values into currentOptions
        $this->setOptions($options);
        // start request width defined options for this action
        $response = $this->request("SendTextSMS", $this->getOptions(array(
            "Recipients",
            "AffiliateId",
            "MessageText",
            "Originator",
            "DeferredDeliveryTime",
            "FlashingSMS",
            "TimeZone",
            "URLBufferedMessageNotification",
            "URLDeliveryNotification",
            "URLNonDeliveryNotification",
        )));

        $result = $this->parseResponse($response);

        // verify if the status code exists in sendStatusCodes
        if (!array_key_exists($result[1], $this->sendStatusCodes)) {
            throw new Exception("Error while printing the response code into sendStatus. ResponseCode seems not valid. Response: \"{$response}\"");
        }
        // send the status as text value into $sendStatus
        $this->sendStatus = $this->sendStatusCodes[$result[1]];
        // if the result is not equal 1 something is wrong
        if ($result[1] !== "1") {
            return false;
        }

        return true;
    }

    /**
     * Getting all information from the sms delivery system for the provided tracking number (which you put besides the recipients)
     *
     * According to the Documentation on aspsms.net a response as partial separated by semicolon (;) below the descriptions of the parts:
     * => TransactionReferenceNumber ; DeliveryStatus ; SubmissionDate ; NotificationDate ; Reasoncode
     *
     * Below some sample responses from the "InquireDeliveryNotifications" method:
     * => success-delivery: 1359553540;0;30012013144546;30012013144555;000;;
     * => failure-delivery: 1359555046;2;30012013151053;30012013151053;206;;
     *
     * Please note: If you have multiple sms sent with the same tracking number only the status for the newest sms will be returned.
     *
     * @param mixed $tracknr The tracking number which is provided when setting the recipients. Can be an array of tracking numbers.
     * @return array (If an array with multiple tracking numbers is provided the response as an assoc array for each tracking number.)
     * @throws \Aspsms\Exception
     */
    public function deliveryStatus($tracknr)
    {
        // set the transaction reference numbers
        $this->setOption("TransactionReferenceNumbers", implode(";", (array) $tracknr));
        // start request
        $response = $this->request("InquireDeliveryNotifications", $this->getOptions(array(
            "TransactionReferenceNumbers",
        )));
        // response array
        $responseArray = array();
        // foreach multiple response codes
        foreach (explode(";;", $response) as $trackResponse) {
            // verify empty strings
            if (strlen($trackResponse) == 0 || empty($trackResponse) || $trackResponse === ";") {
                // skip this entrie
                continue;
            }
            // remove leading semicolons and new lines
            $trackResponse = ltrim($trackResponse, ";\n\r");
            // explode the response
            $result = explode(";", $trackResponse);
            // error while exploding the response
            if (count($result) == 0 || count($result) == 1 || !is_array($result)) {
                $errorExplode = explode(":", $response);
                if (count($errorExplode) !== 2) {
                    throw new Exception("Something went wrong while working with the deliveryStatus response. Response: \"{$response}\"");
                } else {
                    throw new Exception($this->sendStatusCodes[$errorExplode[1]]);
                }
            }
            // set default value for reasoncode
            if ($result[1] == 0) {
                // no error, but the reponse is 000 even when request was successful
                $reasoncode = "-";
            } else {
                // set string for reasoncode
                $reasoncode = (isset($result[4]) && isset($this->deliveryReasonCodes[$result[4]])) ? $this->deliveryReasonCodes[$result[4]] : null;
            }
            // There are parameters missing from the standard response if sms is not submitted yet.
            if ($result[1] === "-1") {
                $responseArray[$result[0]] = array(
                    "transactionReferenceNumber" => $result[0],
                    "deliveryStatus" => $this->deliveryStatusCodes[$result[1]],
                    "deliveryStatusBool" => ($result[1] == 0) ? true : false,
                    "submissionDate" => isset($result[2]) ? $this->dateSplitter($result[2]) : null,
                    "notificationDate" => null,
                    "reasoncode" => null,
                );
            } else {
                // add assoc array
                $responseArray[$result[0]] = array(
                    "transactionReferenceNumber" => $result[0],
                    "deliveryStatus" => $this->deliveryStatusCodes[$result[1]],
                    "deliveryStatusBool" => ($result[1] == 0) ? true : false,
                    "submissionDate" => $this->dateSplitter($result[2]),
                    "notificationDate" => $this->dateSplitter($result[3]),
                    "reasoncode" => $reasoncode,
                );
            }
        }
        // see if we have an error with the response
        if (count($responseArray) === 0) {
            throw new Exception("The provided Tracking Number does not exists.");
        }
        // if there is only 1 result, we have to return only the single assoc array
        if (count($responseArray) === 1) {
            // return the first element (there is only one)
            foreach ($responseArray as $item) {
                return $item;
            }
        }

        return $responseArray;
    }

    /**
     * Get the amount of left credits connected to your account.
     *
     * @return integer
     * @throws Exception
     */
    public function credits()
    {
        // make request for "CheckCredits" aspsms method
        $response = $this->request("CheckCredits");
        // explode the response
        $result = $this->parseResponse($response);
        // check result
        if ($result[0] !== "Credits") {
            if ($result[0] === "StatusCode" && array_key_exists($result[1], $this->sendStatusCodes)) {
                throw new Exception($this->sendStatusCodes[$result[1]]);
            } else {
                throw new Exception("An unknown error occurred while checking the account balance. Response: \"{$response}\"");
            }
        }
        // return the amount
        return (int) $result[1];
    }

    /**
     * Parse the response the response stringo into an array.
     *
     * @param string $response
     * @throws \Aspsms\Exception
     * @return array
     */
    public function parseResponse($response)
    {
        if (empty($response)) {
            throw new Exception("Unable to parse an empty response string.");
        }
        
        return explode(":", $response);
    }

    /**
     * Provides the possibility to read the sendstatus as a readable-response-string (from $sendStatusCodes array)
     *
     * @return string
     */
    public function getSendStatus()
    {
        return $this->sendStatus;
    }

    /**
     * Delete all not allowed signs from a tracking number.
     *
     * @param string $trackingNumber The tracking number to verify
     * @return string
     */
    public static function verifyTrackingNumber($trackingNumber)
    {
        // only a-z A-Z and 0-9 are allowed signs for tracking numbers, preg replace and return.
        return preg_replace("/[^a-zA-Z0-9]/", "", $trackingNumber);
    }

    /**
     * Delete not allowed signs from the mobile phone number.
     *
     * @param string $mobileNumber The mobile number to verify
     * @return string
     */
    public static function verifyMobileNumber($mobileNumber)
    {
        return preg_replace("/[^0-9]/", "", $mobileNumber);
    }

    /**
     * Execute the CURL HTTP POST request to aspsms server. Below a description of the different actions and their values/options:
     *
     * @see https://webservice.aspsms.com/aspsmsx2.asmx
     * @param string $action Contains the aspsms service defined action name like: CheckCredits, InquireDeliveryNotifications, SendTextSMS
     * @param array $values The values which needs to be passed to this action
     * @return string/mixed
     */
    private function request($action, array $values = array())
    {
        // build new AspsmsRequest-Object
        $request = new Request($this->server.$action, $this->prepareValues($values));
        // transfer the request
        $response = $request->transfer();
        // flush request class
        $request->flush();
        // flush local settings
        $this->flush();
        // return request response to its executed method
        return $response;
    }

    /**
     * We put all options together, and also add the always the basics like userkey and password.
     *
     * @param array $values The key is the "post-field" and the value the "post-field-associated-value"
     * @return array
     */
    private function prepareValues($values)
    {
        // set default transfer values
        $transferValues = array(
            'UserKey' => $this->userkey,
            'Password' => $this->password,
        );
        /// get the request values urlencode und utf8encode first.
        foreach ($values as $key => $value) {
            $transferValues[$key] = $value;
        }
        // return changed transfer values
        return $transferValues;
    }

    /**
     * After a request like smssend, deliverystatus or creditscheck we set back the default
     * options value for further requests. which is an emptyarray
     *
     * @return void
     */
    private function flush()
    {
        // set back the default empty array
        $this->currentOptions = array();
        // set back send status buffer string
        $this->sendStatus = null;
    }

    /**
     * Set transfer options/values into currentOptions. Only options which are in the list $validOptions
     * are allowed to set.
     *
     * @param string $key The Option-Key/Name
     * @param string $value The value for the Option-Key
     * @return boolean
     * @throws \Aspsms\Exception
     */
    private function setOption($key, $value)
    {
        // see if key is in the validOptions list.
        if (!in_array($key, $this->validOptions)) {
            throw new Exception("setOption: Could not find the option \"$key\" in the validOptions list!");
        }
        // set the options into the currentOptions list
        $this->currentOptions[$key] = $value;

        return true;
    }

    /**
     * Set multiple transfer options into currentOptions. Only options which are in the list
     * $validOptions are allowed to set.
     *
     * @param array $options An associative array containing the option-keys and option-key-values
     * @return boolean
     * @throws \Aspsms\Exception
     */
    private function setOptions(array $options)
    {
        // loop the $options items
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }

        return true;
    }

    /**
     * Gets all options/values. If there is no value set for the needed $optionKeys the function sets
     * an empty default string '' and returns all requests $optionKeys
     *
     * @param array $optionKeys All options which are requested
     * @return array
     */
    private function getOptions(array $optionKeys)
    {
        // return options array
        $options = array();
        // foreach all option keys and see if some values are set in the currentOptions array
        foreach ($optionKeys as $key) {
            // see if this option is set in options list
            if (array_key_exists($key, $this->currentOptions)) {
                $options[$key] = $this->currentOptions[$key];
            } else {
                $options[$key] = '';
            }
        }

        return $options;
    }

    /**
     * The delivery status return timestamp seems not to be very readable, so we have to split
     * the input string by "hand"
     *
     * @param string $date Input date string like: 30012013223015
     * @throws \Aspsms\Exception
     * @return string
     */
    public function dateSplitter($date)
    {
        $datetime = \DateTime::createFromFormat('dmYHis', $date);
        
        if (!$datetime) {
            throw new Exception("Unable to split invalid input date '{$date}'.");
        }

        return $datetime->format('d.m.Y H:i:s');
    }
}
