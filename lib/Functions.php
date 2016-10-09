<?php
defined('_VALID_INCLUDE') or die('Restricted access');

/**
 *
 *
 * @author Md.Abdullah al mamun | dev.mamun@gmail.com
 * @version 1.0
 * @copyright (c) 2014, PHSS
 *
 */
class Functions
{

    private $_patternAlphabets = "/^[a-zA-Z]+$/";
    private $_namePattern = "/^[a-zA-z0-9.\-]+$/";
    private $_htmlPattern = "/<\/?\w+((\s+\w+(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>/";
    private $_addressPattern = "/^[a-zA-Z0-9,. -]+$/";
    private $_cityPattern = "/^[a-zA-Z' ]+$/";

    /**
     * @param String $var as input
     * @param String $text as Input identification name
     * Array $var as input
     * Object $var as input Description* */
    public function debug($var, $text = "", $isExit = true)
    {
        $str = '';
        $isShow = false;
        if (is_bool($text)) {
            $isExit = $text;
        }
        if (is_string($text)) {
            $text = $text;
        }
        if (self::getConfig('debug')) {
            $isShow = true;
        }
        if ($isShow) {
            $str = "<pre style=\"color: rgb(255, 255, 255); background-color: rgb(102, 102, 102); display: block; padding-left: 10px;\">";
            if ($text != "") {
                $str .= "<h3>{$text}</h3>";
            }
            $str .= print_r($var, true);
            $str .= "</pre>";
            if ($isExit) {
                die($str);
            } else {
                echo $str;
            }
        }
    }

    public function decrypt($encryptedPassword, $key = "")
    {
        $decrypt = "";
        if (empty($key)) {
            $key = 'CSC';
            $salt = strlen($key);
            $SecretKey = sha1($key . $salt);
        }
        if ($encryptedPassword) {
            $decrypt = (mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $SecretKey, base64_decode($encryptedPassword), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
        }
        return $decrypt;
    }

    public function encryptN($pwd, $data)
    {
        $key[] = '';
        $box[] = '';
        $cipher = '';
        $pwd_length = strlen($pwd);
        $data_length = strlen($data);
        for ($i = 0; $i < 256; $i++) {
            $key[$i] = ord($pwd[$i % $pwd_length]);
            $box[$i] = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $data_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
        }
        return $cipher;
    }

    public function dekode($u)
    {
        $p = 'CSC';
        $u = $this->hex2bin($u);
        $a = $this->decryptN($p, $u);
        $a = html_entity_decode($a);
        return $a;
    }

    public function decryptN($pwd, $data)
    {
        return $this->encryptN($pwd, $data);
    }

    public function hex2bin($kodehexa)
    {
        $biner = "";
        for ($i = 0; $i < strlen($kodehexa); $i += 2) {
            $biner .= chr(hexdec(substr($kodehexa, $i, 2)));
        }
        return $biner;
    }

    public function encrypt($password, $key = "")
    {
        if (empty($key)) {
            $key = 'CSC';
            $salt = strlen($key);
            $SecretKey = sha1($key . $salt);
        }
        return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $SecretKey, $password, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
    }

    /**
     * Generates a random password drawn from the defined set of characters.
     *
     * @since 1.0
     *
     * @param int $length The length of password to generate
     * @param bool $special_chars Whether to include standard special characters. Default true.
     * @param bool $extra_special_chars Whether to include other special characters. Used when
     *   generating secret keys and salts. Default false.
     * @return string The random password
     * */
    public function generatePassword($length = 6, $special_chars = true, $extra_special_chars = false)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($special_chars) {
            $chars .= '!@#$%^&*()';
        }
        if ($extra_special_chars) {
            $chars .= '-_ []{}<>~`+=,.;:/?|';
        }
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= substr($chars, $this->_rand(0, strlen($chars) - 1), 1);
        }
        return $password;
    }

    /**
     * Generates a random number
     *
     * @since 1.0
     *
     * @param int $min Lower limit for the generated number (optional, default is 0)
     * @param int $max Upper limit for the generated number (optional, default is 4294967295)
     * @return int A random number between min and max
     */
    public function _rand($min = 0, $max = 0)
    {
        // Reset $rnd_value after 14 uses
        // 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value
        $seed = "ed83d8b178fde8db05a4955b294da17c";
        $rnd_value = md5(uniqid(microtime() . mt_rand(), true) . $seed);
        $rnd_value .= sha1($rnd_value);
        $rnd_value .= sha1($rnd_value . $seed);
        $seed = md5($seed . $rnd_value);
        // Take the first 8 digits for our value
        $value = substr($rnd_value, 0, 8);
        // Strip the first eight, leaving the remainder for the next call to wp_rand().
        $rnd_value = substr($rnd_value, 8);
        $value = abs(hexdec($value));
        // Reduce the value to be within the min - max range
        // 4294967295 = 0xffffffff = max random number
        if ($max != 0) {
            $value = $min + (($max - $min + 1) * ($value / (4294967295 + 1)));
        }
        return abs(intval($value));
    }

    public function isIsset($data, $fields = array())
    {
        if (empty($data)) {
            return array(
                'status' => false,
                'msg' => 'Please provide correct information.',
            );
        } elseif (empty($fields)) {
            return array(
                'status' => false,
                'msg' => 'Sorry could not process your request. please try again.',
            );
        }
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return array(
                    'status' => false,
                    'msg' => ucfirst($field) . ' can not be empty.'
                );
            }
        }
        return array(
            'status' => true,
            'data' => $data
        );
    }

    public function validateInputArray($inputs)
    {
        if (empty($inputs)) {
            return 'Oops!Somthing goes wrong.please try again.';
        }
        foreach ($inputs as $key => $val) {
            switch ($key) {
                case 'email':
                    if (!$this->isValidEmail($val)) {
                        return 'Invalid email address';
                    }
                    break;
                case 'password':
                    if (!$this->isValidPass($val)) {
                        return 'Invalid password. The password entered must be between 6-12 characters long.';
                    }
                    break;
                case 'role':
                    if (!$this->isValidRole($val)) {
                        return "Invalid user role.";
                    }
                    break;
                case 'first_name':
                    if (!$this->isValidName($val)) {
                        return "Invalid first name";
                    }
                    break;
                case 'last_name':
                    if (!$this->isValidName($val)) {
                        return "Invalid last name.";
                    }
                    break;
                case 'address':
                    if (!$this->isValidAddress($val)) {
                        return "Invalid address.";
                    }
                    break;
                case 'country':
                    if (!$this->isValidCountry($val)) {
                        return "Invalid country.";
                    }
                    break;
                case 'city':
                    if (!$this->isValidCity($val)) {
                        return "Invalid city.";
                    }
                    break;
                case 'zip_code':
                    if (!$this->isValidZip($val)) {
                        return "Invalid zip code.";
                    }
                    break;
            }
        }
        return $inputs;
    }

    public function isInt($string)
    {
        return ctype_digit($string);
    }

    public function isAlphabets($string)
    {
        return ctype_alpha($string);
    }

    public function isAlphaNumeric($string)
    {
        return ctype_alnum($string);
    }

    public function isHtml($string)
    {
        preg_match($this->_htmlPattern, $string, $matches);
        if (count($matches) == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function isValidEmail($string)
    {
        if (strlen($string) > 60 && strlen($string) < 10) {
            return false;
        }
        $valid = filter_var($string, FILTER_VALIDATE_EMAIL);
        return $valid;
    }

    public function isValidPass($string)
    {
        if (strlen($string) >= 5 && strlen($string) <= 40) {
            return true;
        }
        return false;
    }

    public function isValidRole($string)
    {
        return $this->isInt($string);
    }

    public function isValidName($string)
    {
        if (strlen($string) > 50 && strlen($string) < 3) {
            return false;
        }
        return preg_match_all($this->_namePattern, $string, $matches);
    }

    public function isValidAddress($string)
    {
        if (strlen($string) > 100) {
            return false;
        }
        $search = array("\\", "/");
        $string = str_replace($search, "", $string);
        return preg_match_all($this->_addressPattern, $string);
    }

    public function isValidCountry($string)
    {
        return $this->isInt($string);
    }

    public function isValidCity($string)
    {
        return $this->isInt($string);
    }

    public function isValidZip($string)
    {
        return $this->isAlphaNumeric($string);
    }

    public function isConfirmPassMatched($newPss, $confirmPass)
    {
        if ($newPss === $confirmPass) {
            return true;
        }
        return false;
    }

    public function object_to_array($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = $this->object_to_array($value);
            }
            return $result;
        } else {
            return $data;
        }
    }

    public function search_array($array, $keys = "")
    {
        if (is_object($array)) {
            $array = $this->object_to_array($array);
        }
        $result = array();
        if ($keys != "") {
            $keys = explode(",", $keys);
        }
        if (is_array($array)) {
            foreach ($array as $key => $val) {
                if (is_array($val)) {
                    $result = $val;
                } else {

                }
            }
        }
        return $result;
    }

    public function findValueByKey($array, $keySearch)
    {
        $result = "";
        if (is_array($array)) {
            if (array_key_exists($keySearch, $array)) {
                return $array[$keySearch];
            }
            foreach ($array as $item) {
                if (is_array($item)) {
                    if (array_key_exists($keySearch, $item)) {
                        return $item[$keySearch];
                    }
                }
                $result = $this->findValueByKey($item, $keySearch);
            }
        }
        return $result;
    }

    public function search($array, $key, $value)
    {
        if (is_object($array)) {
            $array = $this->object_to_array($array);
        }
        $results = array();
        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }
            foreach ($array as $subarray) {
                $results = array_merge($results, $this->search($subarray, $key, $value));
            }
        }
        return $results;
    }

    public function jsonResponse($response)
    {
        if(is_array($response)){
            $response = json_encode($response, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        }
        $cacheDuration = 900;
        header('Content-Type: application/json');
        header("Access-Control-Allow-Origin: *", true);
        header("Access-Control-Allow-Methods: *", true);
        //header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: *", true);
        header('Cache-Control: public,max-age=' . $cacheDuration . ',must-revalidate, post-check=3600,pre-check=43200');
        header('Expires: ' . gmdate('D, d M Y H:i:s', ($_SERVER['REQUEST_TIME'] + $cacheDuration)) . ' GMT');
        header('Last-modified: ' . gmdate('D, d M Y H:i:s', $_SERVER['REQUEST_TIME']) . ' GMT');
        echo $response;
        exit;
    }

    public function minByKey($arr, $key)
    {
        $min = array();
        foreach ($arr as $val) {
            if (!isset($val[$key]) && is_array($val)) {
                $min2 = $this->minByKey($val, $key);
                $min[$min2] = 1;
            } elseif (!isset($val[$key]) && !is_array($val)) {
                return false;
            } elseif (isset($val[$key])) {
                $min[$val[$key]] = 1;
            }
        }
        return min(array_keys($min));
    }

    public function getGMTTimestamp($time = "")
    {
        if ($time == "") {
            return gmdate("Y-m-d\TH:i:s\Z");
        } else {
            return gmdate("Y-m-d\TH:i:s\Z", $time);
        }
    }

    public function getCurrentPageUrl()
    {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public function getCurrentFile($hideExt = false)
    {
        if ($hideExt) {
            return pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        } else {
            return pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME);
        }
    }

    public function getClosest($search, $arr)
    {
        $closest = null;
        foreach ($arr as $item) {
            if ($closest == null || abs($search - $closest) > abs($item - $search)) {
                $closest = $item;
            }
        }
        return $closest;
    }

    public function time_ago($date, $time)
    {
        $days = abs(ceil((strtotime($date) - strtotime("now")) / 86400));
        if ($days > 0)
            $timepast = $days . " days";
        if ($days == 1)
            $timepast = $days . " day";
        $hours = abs(ceil((strtotime($time) - strtotime("now")) / 3600));
        if ($days == 0)
            $timepast = "about " . $hours . " hours";
        if ($hours == 1)
            $timepast = "about " . $hours . " hour";
        $minutes = abs(ceil((strtotime($time) - strtotime("now")) / 60)) - ($hours * 60);
        if ($hours == 0)
            $timepast = $minutes . " minutes";
        if ($minutes == 1)
            $timepast = $minutes . " minute";
        return $timepast;
    }

    public function getExtension($str)
    {
        $ext = pathinfo($str, PATHINFO_EXTENSION);
        return $ext;
    }

    public function getFileName($str)
    {
        $name = pathinfo($str, PATHINFO_FILENAME);
        return $name;
    }

    public function getIncludeInfos()
    {
        $infos = array();
        $infos['included_files'] = get_included_files();
        $infos['include_path'] = get_include_path();
        $infos['defined_constants'] = get_defined_constants();
        $infos['declared_class'] = get_declared_classes();
        $infos['loaded_extensions'] = get_loaded_extensions();
        return $infos;
    }

    public function hasTags($str)
    {
        return !(strcmp($str, strip_tags($str)) == 0);
    }

    public function isJson($string)
    {
        if (empty($string)) {
            return false;
        } elseif (is_array($string)) {
            return false;
        } elseif (is_string($string)) {
            json_decode($string);
            return (json_last_error() == JSON_ERROR_NONE);
        } else {
            return false;
        }
    }

    public function getFilter($rows, $fields)
    {
        if (is_string($fields)) {
            $fields = array($fields);
        }
        if (is_object($rows)) {
            foreach ($fields as $field) {
                unset($rows->$field);
            }
            return $rows;
        }
        if (is_array($rows)) {
            foreach ($fields as $field) {
                unset($rows[$field]);
            }
            return $rows;
        }
    }

    public function getConfig($key)
    {
        return $GLOBALS['cfg'][$key];
    }

    public function setConfig($key, $value, $string = true, $filenmae = "config.php")
    {
        $filename = DOCUMENT_ROOT . '/' . $filenmae;
        $contents = file_get_contents($filename);

        if ($string) {
            $contents = preg_replace('/\$cfg\[\'' . $key . '\'\](\s+)=(\s+).+;/', "\$cfg['$key']$1=$2'$value';", $contents);
        } else {
            $contents = preg_replace('/\$cfg\[\'' . $key . '\'\]\s+=\s+(.+);/', "\$cfg['$key'] = $value;", $contents);
        }

        $file_write = fopen($filename, 'w');
        if (fputs($file_write, $contents)) {
            return true;
        } else {
            return false;
        }

        fclose($file_write);
    }

    public function getFileList($dir)
    {
        $files = scandir($dir, SCANDIR_SORT_DESCENDING);
        $files = array_diff($files, array('.', '..'));
        return $files;
    }

    public function listDirByDate($path)
    {
        $dir = opendir($path);
        $list = array();
        while ($file = readdir($dir)) {
            if ($file != '.' and $file != '..') {
                // add the filename, to be sure not to
                // overwrite a array key
                $ctime = filectime($path . $file);
                $list[$ctime] = $file;
            }
        }
        closedir($dir);
        krsort($list);
        return $list;
    }

    public function writeFile($content, $filePath = "", $fileName = "", $isJson = false)
    {
        if ($this->getConfig('log')) {
            if ($filePath == "") {
                $filePath = $this->getConfig('log_path');
                if ($filePath == '') {
                    $filePath = dirname(__FILE__) . "\\debug\\";
                }
            }
            if ($fileName == "") {
                $fileName = date('Y_m_d') . "_log.log";
            }
            if (!is_dir($filePath)) {
                if (!mkdir($filePath, 0777, true)) {
                    throw new Exception("'Failed to create folder..");
                }
            }
            if (is_object($content)) {
                $content = object_to_array($content);
            }
            if (is_array($content)) {
                $content = print_r($content, true);
            }
            if ($isJson) {
                $content = json_encode($content);
            }
            if (is_dir($filePath)) {
                $file = $filePath . $fileName;
                $handle = fopen($file, 'a+');
                fwrite($handle, $content);
                fclose($handle);
            }
        }
    }

    public function ip2Location($ip)
    {
        $rand = rand(0, 1);
        $ip2locUrl = $this->getConfig('ip2loc');
        $url = $ip2locUrl[$rand] . $ip;
        $result = json_decode($this->getHTTPResponse($url));
        return $result;
    }

    public function getLatLngByAdress($address)
    {
        if (empty($address)) {
            return array(
                'status' => false,
                'msg' => 'Address can not be empty.'
            );
        }
        $response = array();
        $lat = $lng = "";
        $url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=true&language=en";
        $result = json_decode($this->getHTTPResponse($url));
        switch ($result->status) {
            case "OK":
                $lat = $result->results[0]->geometry->location->lat;
                $lng = $result->results[0]->geometry->location->lng;
                $response = array(
                    'status' => true,
                    'lat' => number_format($lat, 6),
                    'lng' => number_format($lng, 6),
                    'gecoding_response' => $result->status,
                    'msg' => 'Geocode was successful',
                    'result' => $result,
                );
                break;
            case "ZERO_RESULTS":
                $response = array(
                    'status' => false,
                    'lat' => $lat,
                    'lng' => $lng,
                    'gecoding_response' => $result->status,
                    'msg' => "Geocode was not successful for the following reason: " . $result->status,
                );
                break;
            default :
                $response = array(
                    'status' => false,
                    'lat' => $lat,
                    'lng' => $lng,
                    'gecoding_response' => $result->status,
                    'msg' => "Geocode was not successful for the following reason: " . $result->status,
                );
        }
        return $response;
    }

    public function getAddressByLatLng($lat, $lng)
    {
        if (empty($lat) || empty($lng)) {
            return array(
                'status' => false,
                'msg' => 'Latitude/Longitude can not be empty.',
            );
        }
        $response = array();
        $latlng = floatval($lat) . "," . floatval($lng);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$latlng}&sensor=true&language=en";
        $result = json_decode($this->getHTTPResponse($url));
        switch ($result->status) {
            case "OK":
                $response = array(
                    'status' => true,
                    'lat' => $lat,
                    'lng' => $lng,
                    'address' => $result->results[0]->formatted_address,
                    'gecoding_response' => $result->status,
                    'msg' => 'Geocode was successful',
                    'result' => $result,
                );
                break;
            case "ZERO_RESULTS":
                $response = array(
                    'status' => false,
                    'lat' => $lat,
                    'lng' => $lng,
                    'address' => '',
                    'gecoding_response' => $result->status,
                    'msg' => "Geocode was not successful for the following reason: " . $result->status,
                    'result' => $result,
                );
                break;
            default :
                $response = array(
                    'status' => false,
                    'lat' => $lat,
                    'lng' => $lng,
                    'address' => '',
                    'gecoding_response' => $result->status,
                    'msg' => "Geocode was not successful for the following reason: " . $result->status,
                    'result' => $result,
                );
        }
        return $response;
    }

    public function getImageSizeByUrl($url)
    {
        list($width, $height, $type, $attr) = getimagesize($url);
        $size = array();
        $size['width'] = $width;
        $size['height'] = $height;
        $size['attr'] = $attr;
        return $size;
    }

    public function getUploadImageInfo($file)
    {
        list($width, $height, $type, $attr) = getimagesize($file['tmp_name']);
        $info = array();
        $info['width'] = $width;
        $info['height'] = $height;
        $info['type'] = $type;
        $info['attr'] = $attr;
        $info['mime'] = image_type_to_mime_type($type);
        return $info;
    }

    public function file_upload_error_message($error_code)
    {
        switch ($error_code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'File upload stopped by extension';
            default:
                return 'Unknown upload error';
        }
    }

    public function getImageExtension($files)
    {
        $imageData = getimagesize($files['tmp_name']);
        $extension = image_type_to_extension($imageData[2]);
        return $extension;
    }

    public function uploadImage($files, $infos = array('id' => '', 'width' => '', 'height' => '', 'save_path' => '', 'filename' => ''), $isMd5 = true)
    {
        $imgInfo = $this->getUploadImageInfo($files);
        if (is_array($infos) && !empty($infos)) {
            extract($infos);
        }
        if (empty($save_path)) {
            $save_path = pathinfo(getcwd(), PATHINFO_DIRNAME) . "/images/";
            if (false === is_dir($save_path)) {
                mkdir($save_path, 0777);
            }
        }
        if (empty($id)) {
            $id = mt_rand(10, 9999);
        }
        if (empty($width)) {
            $width = $imgInfo['width'];
        }
        if (empty($height)) {
            $height = $imgInfo['height'];
        }
        if (empty($filename)) {
            $filename = $this->getFileName($files["name"]);
        }
        if (is_array($files) && !empty($files)) {
            if ($files['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $files["tmp_name"];
                $ext = $this->getImageExtension($files);
                if (!empty($pic)) {
                    $filename .= "_" . $pic;
                }
                if ($isMd5) {
                    $fileName = md5($id . '_' . $filename) . $ext;
                } else {
                    $fileName = ($id . '_' . $filename) . $ext;
                }
                $imageName = $save_path . $fileName;
                $imageResize = new ImageResize();
                $imageResize->load($tmp_name);
                $imageResize->resize($width, $height);
                $imageResize->save($imageName);
                $response = array('status' => true, 'msg' => 'File upload successfully.', 'filename' => $fileName);
            } else {
                $error = file_upload_error_message($files['error']);
                $response = array('status' => false, 'msg' => $error, 'filename' => $fileName);
            }
        } else {
            $response = array('status' => false, 'msg' => 'Upload FILE can not be empty.');
        }
        return json_encode($response);
    }

    function __destruct()
    {
        ;
    }

}
