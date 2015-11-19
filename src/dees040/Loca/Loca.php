<?php

namespace dees040\Loca;

class Loca {

    /**
     * A list with all the country codes
     *
     * @var array
     */
    static private $countryCodes = [];

    /**
     * Main language to use
     *
     * @var string
     */
    static private $locale = 'en';

    /**
     * Fallback language to use if main languages fails
     *
     * @var string
     */
    static private $fallbackLocale = 'en';

    /**
     * Directory with all the language files
     *
     * @var
     */
    static private $langDir;

    /**
     * Prepare our Loca class.
     *
     * @param array $parameters
     */
    static public function prepare(array $parameters = [])
    {
        $currentDir = realpath(dirname(__FILE__));

        self::$countryCodes = include $currentDir . '\CountryCodes.php';

        if (array_key_exists('locale', $parameters)) {
            self::$locale = $parameters['locale'];
        }

        if (array_key_exists('fallbackLocale', $parameters)) {
            self::$fallbackLocale = $parameters['fallbackLocale'];
        }

        if (array_key_exists('langDir', $parameters)) {
            self::$langDir = $parameters['langDir'];
        } else {
            self::$langDir = $currentDir . '\languages';
        }
    }

    /**
     * Search for translation. Send the id and if wanted the
     * placeholders and there values.
     *
     * @param string $id
     * @param array $parameters
     * @return string
     */
    static public function translate($id, array $parameters = [])
    {
        return self::executeParameters(
            self::getTranslation($id),
            $parameters
        );
    }

    /**
     * Get the translations from the main or fallback language.
     * If none translations are found we will return a simple
     * warning message.
     *
     * @param string $id
     * @return string
     */
    private function getTranslation($id)
    {
        list($file, $key) = explode('.', $id);

        $file .= '.php';

        $mainDir = self::getMainLangDir() . '\\' . $file;
        if (file_exists($mainDir) &&
            array_key_exists($key, $array = include $mainDir)) {
            return $array[$key];
        }

        $fallbackDir = self::getFallbackLangDir() . '\\' . $file;
        if (file_exists($fallbackDir) &&
            array_key_exists($key, $array = include $fallbackDir)) {
            return $array[$key];
        }

        return "You're missing a translation for: ".$id;
    }

    /**
     * Replace placeholder with the given value.
     *
     * @param $translation
     * @param $parameters
     * @return string
     */
    private function executeParameters($translation, $parameters)
    {
        foreach($parameters as $valueToFind => $output) {
            $translation = str_replace(':' . $valueToFind, $output, $translation);
        }

        return $translation;
    }

    /**
     * Get the main language directory.
     *
     * @return string
     */
    private function getMainLangDir()
    {
        return self::$langDir . '\\' . self::$locale;
    }

    /**
     * Get the fallback languages directory.
     *
     * @return string
     */
    private function getFallbackLangDir()
    {
        return self::$langDir . '\\' . self::$fallbackLocale;
    }

    static public function visitorCountry($ip = "Visistor")
    {
        return self::ipInfo($ip, "Country Code");
    }

    /* -----------------------------------------------------------------------------------
     *
     * Helpers
     *
     * ----------------------------------------------------------------------------------- */

    /**
     * @param $code
     * @return string|FALSE
     */
    static public function getCountryByCode($code)
    {
        if (array_key_exists(strtoupper($code), self::$countryCodes)) {
            return self::$countryCodes[$code];
        }

        return FALSE;
    }

    /**
     * @param $country
     * @return string|FALSE
     */
    static public function getCodeByCountry($country)
    {
        return array_search(
            ucwords(strtolower($country)), // Needle, first lowercase input then uppercase the first character of each word
            self::$countryCodes // Haystack
        );
    }

    /**
     * Function from Stackoverflow:
     * http://stackoverflow.com/questions/12553160/getting-visitors-country-from-their-ip
     *
     * Function to get country info of visitor.
     *
     * @param null $ip
     * @param string $purpose
     * @param bool|TRUE $deep_detect
     * @return array|null|string
     */
    private function ipInfo($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        $purpose    = str_replace(["name", "\n", "\t", " ", "-", "_"], NULL, strtolower(trim($purpose)));
        $support    = ["country", "countrycode", "location"];
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = [
                            "city"           => @$ipdat->geoplugin_city,
                            "state"          => @$ipdat->geoplugin_regionName,
                            "country"        => @$ipdat->geoplugin_countryName,
                            "country_code"   => @$ipdat->geoplugin_countryCode,
                        ];
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }

}