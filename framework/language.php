<?php declare(strict_types=1);

class Language {

    /**
     * Returns an array of all languages defined in the 
     * configuration.
     * 
     * @return array
     * An array of all languages defined in the configuration.
     */
    public static function get(): array {
        $languages = Config::get("application->languages");
        return is_array($languages) ? $languages : [$languages];
    }

    /**
     * Sets the language cookie to the given language.
     * 
     * @param string $language
     * The ISO 639-1 abbreviation of the language.
     * 
     * @return void
     * Returns nothing.
     */
    public static function set(string $language): void {
        if (!self::validate($language)) $language = "en";
        Config::set("application->language", $language);
    }

    /**
     * Returns the current language based on the language cookie.
     * If the cookie is not set, the default language is returned
     * and the cookie is set to the default language.
     * 
     * @return string
     * The ISO 639-1 abbreviation of the language.
     */
    public static function current(): string {

        // Check if the language cookie is set and valid
        if (isset($_COOKIE['language']) && self::validate($_COOKIE['language'])) {

            $current = Request::current();
            if(!empty(Pages::get("{$current}->language")) && Pages::get("{$current}->language") !== $_COOKIE['language']){
                self::update(Pages::get("{$current}->language"));
            }
            return $_COOKIE['language'];
        }

        // Get the default language from the configuration
        $config = Config::get("application->meta->default->language");

        // If the default language is not valid, set it to "en"
        if (!self::validate($config)) {
            self::update("en");
            return "en";
        }

        // Update the language cookie with the default language and return it
        self::update($config);
        return $config;
    }

    /**
     * Updates the language cookie if the given language is valid.
     * Otherwise, the cookie is set to en (English).
     * 
     * @param string $language
     * The ISO 639-1 abbreviation of the language.
     * 
     * @return bool
     * Returns true if the language was updated, false otherwise.
     */
    public static function update(string $language): bool {
        $valid = self::validate($language);
        setcookie('language', $valid ? $language : 'en', time() + (86400 * 30), "/");
        return $valid;
    }

    /**
     * Validates the given language based on the 
     * ISO 639-1 standard.
     * 
     * @param string $language
     * The ISO 639-1 abbreviation of the language.
     * 
     * @return bool
     * Returns true if the language is valid, false otherwise.
     */
    private static function validate(string $language): bool {
        return isset(self::$iso639_1[$language]) 
            ? true : false;
    }

    /**
     * Returns true if the application supports multiple languages,
     * false otherwise.
     * 
     * @return bool
     * Returns true if the application supports multiple languages,
     */
    public static function supported(): bool {
        return count(self::get()) > 1 ? true : false;
    }

    /**
     * Redirect to the correct language version of the current page.
     * 
     * @return void
     * Returns nothing.
     */
    public static function redirect(string $language) {
        $current  = Request::current();
        $index    = Config::get('application->router->index');
        
        if ($current === $index || $current === "/") {
            
            $pages   = Path::pages();
            $default = Config::get('application->meta->default->language');
            $target  = is_dir("{$pages}/{$language}/{$index}") 
                            ? "{$language}/{$index}" 
                            : "{$default}/{$index}";

            Response::redirect($target);
        }
    }

    // Abbreviations of languages:
    private static array $iso639_1 = [
        "aa" => "Afar",
        "ab" => "Abkhazian",
        "af" => "Afrikaans",
        "ak" => "Akan",
        "am" => "Amharic",
        "an" => "Aragonese",
        "ar" => "Arabic",
        "as" => "Assamese",
        "av" => "Avaric",
        "ay" => "Aymara",
        "az" => "Azerbaijani",
        "ba" => "Bashkir",
        "be" => "Belarusian",
        "bg" => "Bulgarian",
        "bh" => "Bihari",
        "bi" => "Bislama",
        "bm" => "Bambara",
        "bn" => "Bengali",
        "bo" => "Tibetan",
        "br" => "Breton",
        "bs" => "Bosnian",
        "ca" => "Catalan",
        "ce" => "Chechen",
        "ch" => "Chamorro",
        "co" => "Corsican",
        "cr" => "Cree",
        "cs" => "Czech",
        "cu" => "Church Slavic",
        "cv" => "Chuvash",
        "cy" => "Welsh",
        "da" => "Danish",
        "de" => "German",
        "dv" => "Divehi",
        "dz" => "Dzongkha",
        "ee" => "Ewe",
        "el" => "Greek",
        "en" => "English",
        "eo" => "Esperanto",
        "es" => "Spanish",
        "et" => "Estonian",
        "eu" => "Basque",
        "fa" => "Persian",
        "ff" => "Fulah",
        "fi" => "Finnish",
        "fj" => "Fijian",
        "fo" => "Faroese",
        "fr" => "French",
        "ga" => "Irish",
        "gd" => "Scottish Gaelic",
        "gl" => "Galician",
        "gn" => "Guarani",
        "gu" => "Gujarati",
        "gv" => "Manx",
        "ha" => "Hausa",
        "he" => "Hebrew",
        "hi" => "Hindi",
        "ho" => "Hiri Motu",
        "hr" => "Croatian",
        "ht" => "Haitian Creole",
        "hu" => "Hungarian",
        "hy" => "Armenian",
        "hz" => "Herero",
        "ia" => "Interlingua",
        "id" => "Indonesian",
        "ie" => "Interlingue",
        "ig" => "Igbo",
        "ii" => "Sichuan Yi",
        "ik" => "Inupiaq",
        "io" => "Ido",
        "is" => "Icelandic",
        "it" => "Italian",
        "iu" => "Inuktitut",
        "ja" => "Japanese",
        "jv" => "Javanese",
        "ka" => "Georgian",
        "kg" => "Kongo",
        "ki" => "Kikuyu",
        "kj" => "Kuanyama",
        "kk" => "Kazakh",
        "kl" => "Greenlandic",
        "km" => "Khmer",
        "kn" => "Kannada",
        "ko" => "Korean",
        "kr" => "Kanuri",
        "ks" => "Kashmiri",
        "ku" => "Kurdish",
        "kv" => "Komi",
        "kw" => "Cornish",
        "ky" => "Kyrgyz",
        "la" => "Latin",
        "lb" => "Luxembourgish",
        "lg" => "Ganda",
        "li" => "Limburgish",
        "ln" => "Lingala",
        "lo" => "Lao",
        "lt" => "Lithuanian",
        "lu" => "Luba-Katanga",
        "lv" => "Latvian",
        "mg" => "Malagasy",
        "mh" => "Marshallese",
        "mi" => "Maori",
        "mk" => "Macedonian",
        "ml" => "Malayalam",
        "mn" => "Mongolian",
        "mr" => "Marathi",
        "ms" => "Malay",
        "mt" => "Maltese",
        "my" => "Burmese",
        "na" => "Nauru",
        "nb" => "Norwegian Bokmål",
        "nd" => "North Ndebele",
        "ne" => "Nepali",
        "ng" => "Ndonga",
        "nl" => "Dutch",
        "nn" => "Norwegian Nynorsk",
        "no" => "Norwegian",
        "nr" => "South Ndebele",
        "nv" => "Navajo",
        "ny" => "Chichewa",
        "oc" => "Occitan",
        "oj" => "Ojibwa",
        "om" => "Oromo",
        "or" => "Oriya",
        "os" => "Ossetian",
        "pa" => "Punjabi",
        "pi" => "Pali",
        "pl" => "Polish",
        "ps" => "Pashto",
        "pt" => "Portuguese",
        "qu" => "Quechua",
        "rm" => "Romansh",
        "rn" => "Kirundi",
        "ro" => "Romanian",
        "ru" => "Russian",
        "rw" => "Kinyarwanda",
        "sa" => "Sanskrit",
        "sc" => "Sardinian",
        "sd" => "Sindhi",
        "se" => "Northern Sami",
        "sg" => "Sango",
        "si" => "Sinhalese",
        "sk" => "Slovak",
        "sl" => "Slovenian",
        "sm" => "Samoan",
        "sn" => "Shona",
        "so" => "Somali",
        "sq" => "Albanian",
        "sr" => "Serbian",
        "ss" => "Swati",
        "st" => "Southern Sotho",
        "su" => "Sundanese",
        "sv" => "Swedish",
        "sw" => "Swahili",
        "ta" => "Tamil",
        "te" => "Telugu",
        "tg" => "Tajik",
        "th" => "Thai",
        "ti" => "Tigrinya",
        "tk" => "Turkmen",
        "tl" => "Tagalog",
        "tn" => "Tswana",
        "to" => "Tonga",
        "tr" => "Turkish",
        "ts" => "Tsonga",
        "tt" => "Tatar",
        "tw" => "Twi",
        "ty" => "Tahitian",
        "ug" => "Uighur",
        "uk" => "Ukrainian",
        "ur" => "Urdu",
        "uz" => "Uzbek",
        "ve" => "Venda",
        "vi" => "Vietnamese",
        "vo" => "Volapük",
        "wa" => "Walloon",
        "wo" => "Wolof",
        "xh" => "Xhosa",
        "yi" => "Yiddish",
        "yo" => "Yoruba",
        "za" => "Zhuang",
        "zh" => "Chinese",
        "zu" => "Zulu"
    ];


}