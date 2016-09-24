<?php
require_once getcwd() . '/autoloder.php';

use lib\FanTv;
use lib\Functions;

$helper = new Functions();

$fantv = new FanTv();

//Get Movies
/*$result = $fantv->getMovie(11);
$movie = json_decode($result);
foreach ($movie as $key => $val) {
    if (is_object($val)) {
        echo $key . ': ' . implode(',', $helper->object_to_array($val)) . '<br>';
    } elseif (is_array($val)) {
        echo $key . ': ' . implode(',', $helper->object_to_array($val)) . '<br>';
    } else {
        echo $key . ': ' . $val . '<br>';
    }
}*/

//get Movie cast
/*$result = $fantv->getMovieCast(11);
$casts = json_decode($result);
echo '<strong>Casting Inforation</strong><br>';
foreach ($casts->data as $key => $val) {
    echo $val->roles[0] . ': ' . $val->character_names[0] . '<br>';
}*/

//Get Tv Schedule
$result = $fantv->getTvSchedule();


$helper->debug($result);

/*


$tvschedule = "https://api.fan.tv/1.0/discover/browse/lineups";
$str = '{
  "country": "US",
  "postal_code": "90210"
}';
$db->setPostString($str,true);
$response = $db->getHTTPResponse($tvschedule);
$tvschedules = json_decode($response);
echo '<pre>';
print_r($tvschedules);*/