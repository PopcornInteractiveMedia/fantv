<?php
require_once getcwd() . '/autoloder.php';

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
$result = $fantv->getLineups('US','90210',1,32);
//$tvschedules = json_decode($result);
$helper->jsonResponse($result);
//$helper->debug($tvschedules);