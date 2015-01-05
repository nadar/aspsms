<?php

namespace Aspsms;

/**
 * Little helper class to make the curl-post-request to the aspsms server.
 *
 * Usage example:
 *
 * $request = new \Request('https://webservice.aspsms.com/aspsmsx2.asmx/CheckCredits');
 * // transfer the request
 * $response = $request->transfer();
 * // flush the request object
 * $request->flush();
 *
 * @package Aspsms
 * @author nadar <n@adar.ch>
 * @see https://github.com/nadar/aspsms
 */
class Request
{
    /**
     * Default options for the curl request
     *
     * @param array
     */
    private $options = array(
        CURLOPT_TIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POST => true,
    );

    /**
     * All values which are provided trought value() or __construct()
     *
     * @param array
     */
    private $values = array();

    /**
     * AspsmsRequest constructor requerd call service url.
     *
     * @param string $url              The called webservice url
     * @param array  $values[optional] Values can be set direct in the class construct or via the value() method.
     */
    public function __construct($url, array $values = array())
    {
        // assign CURLOPT_URL into options array
        $this->options[CURLOPT_URL] = $url;
        // set basic value keys into values array
        $this->values = $values;
    }

    /**
     * Optional method to set values.
     *
     * @param  string  $key   The POST-FIELD-KEY
     * @param  string  $value The value of the postfield
     * @return boolean
     */
    public function value($key, $value)
    {
        // save values into values array (great comment)
        $this->values[$key] = $value;

        return true;
    }

    /**
     * Unset all values from the values array to make new requests.
     *
     * @return boolean
     */
    public function flush()
    {
        // overwrite $values with empty array()
        $this->values = array();

        return true;
    }

    /**
     * Could not use http_build_query() because of &, ; & : signs changing, need to build a
     * simple function to build the strings.
     * @todo url_encoding the values (verify affecting requests first)
     * @param  array  $values Key value pared parameter values
     * @return string
     */
    private function buildPostfields($values)
    {
        $params = array();
        foreach ($values as $k => $v) {
            $params[] = $k.'='.$v;
        }

        return implode("&", $params);
    }

    /**
     * Init the main curl excution.
     *
     * @return string/mixed
     * @throws Exception
     */
    public function transfer()
    {
        // prepare postfields
        $this->options[CURLOPT_POSTFIELDS] = $this->buildPostfields($this->values);
        // init curl
        $curl = curl_init();
        // set all options into curl object from $options
        curl_setopt_array($curl, $this->options);
        // excute the curl and write response into $response
        $response = curl_exec($curl);
        // close the curl connection
        curl_close($curl);
        // see if response is xml valid (else we have a basic api error)
        if (preg_match('/\<\?xml(.*)\?\>/', $response)) {
            // get node content
            $doc = new \DOMDocument();
            // load the xml reponse (which is xml)
            $doc->loadXML($response);
            // get content from the firstChild (there is no good documentation about aspsms response bodys)
            $nodeContent = $doc->firstChild->textContent;
            // return the content
            return $nodeContent;
        } else {
            throw new Exception(sprintf("Invalid API Response '%s'", trim($response)));
        }
    }
}
