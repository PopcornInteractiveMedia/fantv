<?php
require_once getcwd() . '/autoloder.php';

$db = new Database();
$token = $db->getConfig('fantv')['bearer_token'];
$db->_isDebug = true;
$url = "https://api.fan.tv/1.0/metadata/fantv/movies/11";

$header = [
    'Authorization' => 'Bearer ' . $token
];

$db->setHeaders($header);
$response = $db->getHTTPResponse($url);
$movie = json_decode($response);
foreach ($movie as $key => $val) {
    if (is_object($val)) {
        echo $key . ': ' . implode(',', $db->object_to_array($val)) . '<br>';
    } elseif (is_array($val)) {
        echo $key . ': ' . implode(',', $db->object_to_array($val)) . '<br>';
    } else {
        echo $key . ': ' . $val . '<br>';
    }
}
echo '<hr>';
$casts = "https://api.fan.tv/1.0/metadata/fantv/movies/11/cast";
$response = $db->getHTTPResponse($casts);
$casts = json_decode($response);
echo '<strong>Casting Inforation</strong><br>';
foreach ($casts->data as $key => $val) {
    echo $val->roles[0] . ': ' . $val->character_names[0] . '<br>';
}
echo '<hr>';
$tvschedule = "https://api.fan.tv/1.0/discover/browse/lineups";
$str = '{
  "country": "US",
  "postal_code": "90210"
}';
$db->setPostString($str,true);
$response = $db->getHTTPResponse($tvschedule);
$tvschedules = json_decode($response);
echo '<pre>';
print_r($tvschedules);