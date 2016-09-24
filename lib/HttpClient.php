<?php
/**
 * Created by Md.Abdullah Al Mamun.
 * Email: dev.mamun@gmail.com
 * Date: 9/24/2016
 * Time: 3:14 PM
 * Year: 2016
 */

namespace lib;

use lib\Functions;

class HttpClient
{
    private $baseUrl = "";
    private $config = [];
    private $_headers = [];

    private $_requestUrl = "";
    private $_userAgent = "Mozilla/5.0 (Windows NT 6.1; rv:12.0) Gecko/20100101 Firefox/12.0";
    private $_method = "GET";
    private $_postString = "";
    private $_referrer = '';
    private $isSetHeader = true;
    private $statusCode = '';
    private $error = '';

    public $showHeader = false;
    public $isDebug = false;

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    public function setRequestUrl($type)
    {
        $this->_requestUrl = $this->baseUrl . $type;
    }

    public function setHeaders($headers)
    {
        if (!empty($headers) && is_array($headers)) {
            $this->isSetHeader = true;
            foreach ($headers as $key => $val) {
                $this->_headers[] = $key . ': ' . $val;
            }
        }
    }

    public function setContentType($type)
    {
        $this->setHeaders(['Content-Type' => $type]);
    }

    public function setUserAgent($agent)
    {
        $this->_userAgent = $agent;
    }

    public function setMethod($method)
    {
        $this->_method = strtoupper($method);
    }

    public function setPostString($data, $isJson = false)
    {
        if ($isJson) {
            $this->_postString = ($data);
        } else {
            $this->_postString = http_build_query($data);
        }
    }

    public function setReferrer($url)
    {
        $this->_referrer = $url;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHttpError()
    {
        return $this->error;
    }

    function request()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_requestUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->_userAgent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        if ($this->_referrer != "") {
            curl_setopt($ch, CURLOPT_REFERER, $this->_referrer);
        }
        if ($this->_method == "PUT" || $this->_method == "DELETE") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->_method);
        }
        if ($this->_postString != "") {
            curl_setopt($ch, CURLOPT_POST, 1.1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_postString);
        }
        if ($this->isSetHeader) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_headers);
        }
        if ($this->showHeader) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }
        $result = curl_exec($ch);
        $this->error = curl_error($ch);
        $this->statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($this->isDebug) {
            echo("url=" . $this->_requestUrl .
                "<hr>PostFields=" . print_r($this->_postString, true) .
                "<hr>Error=" . $this->error .
                "<hr>" . "Code=" . $this->statusCode .
                "<hr>" . nl2br(htmlspecialchars($result)) . "<hr>");
        }
        return $result;
    }
}