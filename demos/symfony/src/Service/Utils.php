<?php

namespace App\Service;

class Utils
{
    public function isMobile($user_agent)
    {
        if (
            strpos($user_agent, 'Android') !== false ||
            strpos($user_agent, 'IEMobile') !== false ||
            strpos($user_agent, 'BlackBerry') !== false ||
            strpos($user_agent, 'iPod') !== false ||
            strpos($user_agent, 'iPad') !== false ||
            strpos($user_agent, 'iPhone') !== false ||
            strpos($user_agent, 'webOS') !== false
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function getBrowser($user_agent)
    {
        if (strpos($user_agent, 'MSIE') !== false) {
            return 'ie';
        } elseif (strpos($user_agent, 'Edge') !== false) {
            //Microsoft Edge
            return 'edge';
        } elseif (strpos($user_agent, 'Trident') !== false) {
            //IE 11
            return 'ie';
        } elseif (strpos($user_agent, 'Opera Mini') !== false) {
            return 'op';
        } elseif (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== false) {
            return 'op';
        } elseif (strpos($user_agent, 'Firefox') !== false) {
            return 'moz';
        } elseif (strpos($user_agent, 'Chrome') !== false) {
            return 'chrome';
        } elseif (strpos($user_agent, 'Safari') !== false) {
            return 'safari';
        } elseif (strpos($user_agent, 'Android') !== false) {
            return 'android';
        } elseif (strpos($user_agent, 'IEMobile') !== false) {
            return 'iemobile';
        } elseif (strpos($user_agent, 'BlackBerry') !== false) {
            return 'blackberry';
        } elseif (strpos($user_agent, 'iPod') !== false) {
            return 'ipod';
        } elseif (strpos($user_agent, 'iPad') !== false) {
            return 'ipad';
        } elseif (strpos($user_agent, 'iPhone') !== false) {
            return 'iphone';
        } elseif (strpos($user_agent, 'webOS') !== false) {
            return 'webos';
        } else {
            return 'No hemos podido detectar su navegador';
        }
    }

    public function base64ToJpeg(string $base64_string, string $output_file)
    {
        // open the output file for writing
        $ifp = fopen($output_file, 'wb');

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $base64_string);

        // we could add validation here with ensuring count( $data ) > 1
        fwrite($ifp, base64_decode($data[1]));

        // clean up the file resource
        fclose($ifp);

        return $output_file;
    }
}
