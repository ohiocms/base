<?php

namespace Belt\Core\Helpers;

use Sabre\Uri;

/**
 * Class UrlHelper
 * @package Belt\Core\Helpers
 */
class UrlHelper
{

    /**
     * Determine static path
     *
     * @param $path
     * @return string
     */
    public static function staticUrl($path)
    {
        if (!$static_url = env('APP_STATIC_URL')) {
            return url($path);
        }

        $url = sprintf('%s/%s', $static_url, $path);
        $url = self::normalize($url);

        return $url;
    }

    /**
     * @param $str
     * @return string
     */
    public static function normalize($str)
    {
        return rtrim(Uri\normalize($str), '/');
    }

    /**
     * See if url opens
     *
     * @param string $url
     * @return boolean
     */
    public static function exists($url)
    {
        $exists = true;

        try {

            $headers = get_headers($url);

            $status = $headers[0] ?? false;

            if (in_array($status, [
                false,
                'HTTP/1.1 404 Not Found',
                'HTTP/1.1 500 Internal Server Error',
            ])) {
                $exists = false;
            }

        } catch (\Exception $e) {
            $exists = false;
        }

        return $exists;
    }

}