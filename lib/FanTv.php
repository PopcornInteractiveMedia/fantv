<?php
/**
 * Created by Md.Abdullah Al Mamun.
 * Email: dev.mamun@gmail.com
 * Date: 9/10/2016
 * Time: 1:48 AM
 * Year: 2016
 */

namespace lib;

use lib\HttpClient;


class FanTv
{
    private $_http;
    private $_helper;

    public function __construct()
    {
        $this->_helper = new Functions();
        $this->_http = new HttpClient();
        $this->_http->setBaseUrl($this->_helper->getConfig('fantv')['url']);
        $this->_http->setHeaders(['Authorization' => 'Bearer ' . $this->_helper->getConfig('fantv')['bearer_token']]);
        //$this->_http->isDebug = true;
        //$this->_http->showHeader = true;
    }

    public function getMovie($id)
    {
        $url = 'metadata/fantv/movies/' . $id;
        $this->_http->setRequestUrl($url);
        $result = $this->_http->request();
        return ($result);
    }

    public function getMovieCast($id)
    {
        $url = 'metadata/fantv/movies/' . $id . '/cast';
        $this->_http->setRequestUrl($url);
        $result = $this->_http->request();
        return ($result);
    }

    public function getTvSchedule($country, $zip)
    {
        $url = 'discover/browse/lineups';
        $this->_http->setRequestUrl($url);
        $postStr = [
            "country"=>$country,
            "postal_code"=>$zip
        ];
        $this->_http->setPostString($postStr,true);
        $result = $this->_http->request();
        return ($result);
    }
}