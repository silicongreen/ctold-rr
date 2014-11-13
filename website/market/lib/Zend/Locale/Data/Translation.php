<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Locale
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: Translation.php 24593 2012-01-05 20:35:02Z matthew $
 */

/**
 * Definition class for all Windows locales
 * Based on this two lists:
 * @link http://msdn.microsoft.com/en-us/library/39cwe7zf.aspx
 * @link http://msdn.microsoft.com/en-us/library/cdax410z.aspx
 * @link http://msdn.microsoft.com/en-us/goglobal/bb964664.aspx
 * @link http://msdn.microsoft.com/en-us/goglobal/bb895996.aspx
 *
 * @category  Zend
 * @package   Zend_Locale
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Locale_Data_Translation
{
    /**
     * Locale Translation for Full Named Locales
     *
     * @var array $localeTranslation
     */
    public static $languageTranslation = array(
        'Afrikaans'         => 'af',
        'Albanian'          => 'sq',
        'Amharic'           => 'am',
        'Arabic'            => 'ar',
        'Armenian'          => 'hy',
        'Assamese'          => 'as',
        'Azeri'             => 'az',
        'Azeri Latin'       => 'az_Latn',
        'Azeri Cyrillic'    => 'az_Cyrl',
        'Basque'            => 'eu',
        'Belarusian'        => 'be',
        'Bengali'           => 'bn',
        'Bengali Latin'     => 'bn_Latn',
        'Bosnian'           => 'bs',
        'Bulgarian'         => 'bg',
        'Burmese'           => 'my',
        'Catalan'           => 'ca',
        'Cherokee'          => 'chr',
        'Chinese'           => 'zh',
        'Croatian'          => 'hr',
        'Czech'             => 'cs',
        'Danish'            => 'da',
        'Divehi'            => 'dv',
        'Dutch'             => 'nl',
        'English'           => 'en',
        'Estonian'          => 'et',
        'Faroese'           => 'fo',
        'Faeroese'          => 'fo',
        'Farsi'             => 'fa',
        'Filipino'          => 'fil',
        'Finnish'           => 'fi',
        'French'            => 'fr',
        'Frisian'           => 'fy',
        'Macedonian'        => 'mk',
        'Gaelic'            => 'gd',
        'Galician'          => 'gl',
        'Georgian'          => 'ka',
        'German'            => 'de',
        'Greek'             => 'el',
        'Guarani'           => 'gn',
        'Gujarati'          => 'gu',
        'Hausa'             => 'ha',
        'Hawaiian'          => 'haw',
        'Hebrew'            => 'he',
        'Hindi'             => 'hi',
        'Hungarian'         => 'hu',
        'Icelandic'         => 'is',
        'Igbo'              => 'ig',
        'Indonesian'        => 'id',
        'Inuktitut'         => 'iu',
        'Italian'           => 'it',
        'Japanese'          => 'ja',
        'Kannada'           => 'kn',
        'Kanuri'            => 'kr',
        'Kashmiri'          => 'ks',
        'Kazakh'            => 'kk',
        'Khmer'             => 'km',
        'Konkani'           => 'kok',
        'Korean'            => 'ko',
        'Kyrgyz'            => 'ky',
        'Lao'               => 'lo',
        'Latin'             => 'la',
        'Latvian'           => 'lv',
        'Lithuanian'        => 'lt',
        'Macedonian'        => 'mk',
        'Malay'             => 'ms',
        'Malayalam'         => 'ml',
        'Maltese'           => 'mt',
        'Manipuri'          => 'mni',
        'Maori'             => 'mi',
        'Marathi'           => 'mr',
        'Mongolian'         => 'mn',
        'Nepali'            => 'ne',
        'Norwegian'         => 'no',
        'Norwegian Bokmal'  => 'nb',
        'Norwegian Nynorsk' => 'nn',
        'Oriya'             => 'or',
        'Oromo'             => 'om',
        'Papiamentu'        => 'pap',
        'Pashto'            => 'ps',
        'Polish'            => 'pl',
        'Portuguese'        => 'pt',
        'Punjabi'           => 'pa',
        'Quecha'            => 'qu',
        'Quechua'           => 'qu',
        'Rhaeto-Romanic'    => 'rm',
        'Romanian'          => 'ro',
        'Russian'           => 'ru',
        'Sami'              => 'smi',
        'Sami Inari'        => 'smn',
        'Sami Lule'         => 'smj',
        'Sami Northern'     => 'se',
        'Sami Skolt'        => 'sms',
        'Sami Southern'     => 'sma',
        'Sanskrit'          => 'sa',
        'Serbian'           => 'sr',
        'Serbian Latin'     => 'sr_Latn',
        'Serbian Cyrillic'  => 'sr_Cyrl',
        'Sindhi'            => 'sd',
        'Sinhalese'         => 'si',
        'Slovak'            => 'sk',
        'Slovenian'         => 'sl',
        'Somali'            => 'so',
        'Sorbian'           => 'wen',
        'Spanish'           => 'es',
        'Swahili'           => 'sw',
        'Swedish'           => 'sv',
        'Syriac'            => 'syr',
        'Tajik'             => 'tg',
        'Tamazight'         => 'tmh',
        'Tamil'             => 'ta',
        'Tatar'             => 'tt',
        'Telugu'            => 'te',
        'Thai'              => 'th',
        'Tibetan'           => 'bo',
        'Tigrigna'          => 'ti',
        'Tsonga'            => 'ts',
        'Tswana'            => 'tn',
        'Turkish'           => 'tr',
        'Turkmen'           => 'tk',
        'Uighur'            => 'ug',
        'Ukrainian'         => 'uk',
        'Urdu'              => 'ur',
        'Uzbek'             => 'uz',
        'Uzbek Latin'       => 'uz_Latn',
        'Uzbek Cyrillic'    => 'uz_Cyrl',
        'Venda'             => 've',
        'Vietnamese'        => 'vi',
        'Welsh'             => 'cy',
        'Xhosa'             => 'xh',
        'Yiddish'           => 'yi',
        'Yoruba'            => 'yo',
        'Zulu'              => 'zu',
    );

    public static $regionTranslation = array(
        'Albania'                    => 'AL',
        'Algeria'                    => 'DZ',
        'Argentina'                  => 'AR',
        'Armenia'                    => 'AM',
        'Australia'                  => 'AU',
        'Austria'                    => 'AT',
        'Bahrain'                    => 'BH',
        'Bangladesh'                 => 'BD',
        'Belgium'                    => 'BE',
        'Belize'                     => 'BZ',
        'Bhutan'                     => 'BT',
        'Bolivia'                    => 'BO',
        'Bosnia Herzegovina'         => 'BA',
        'Brazil'                     => 'BR',
        'Brazilian'                  => 'BR',
        'Brunei Darussalam'          => 'BN',
        'Cameroon'                   => 'CM',
        'Canada'                     => 'CA',
        'Chile'                      => 'CL',
        'China'                      => 'CN',
        'Colombia'                   => 'CO',
        'Costa Rica'                 => 'CR',
        "Cote d'Ivoire"              => 'CI',
        'Czech Republic'             => 'CZ',
        'Dominican Republic'         => 'DO',
        'Denmark'                    => 'DK',
        'Ecuador'                    => 'EC',
        'Egypt'                      => 'EG',
        'El Salvador'                => 'SV',
        'Eritrea'                    => 'ER',
        'Ethiopia'                   => 'ET',
        'Finland'                    => 'FI',
        'France'                     => 'FR',
        'Germany'                    => 'DE',
        'Greece'                     => 'GR',
        'Guatemala'                  => 'GT',
        'Haiti'                      => 'HT',
        'Honduras'                   => 'HN',
        'Hong Kong'                  => 'HK',
        'Hong Kong SAR'              => 'HK',
        'Hungary'                    => 'HU',
        'Iceland'                    => 'IS',
        'India'                      => 'IN',
        'Indonesia'                  => 'ID',
        'Iran'                       => 'IR',
        'Iraq'                       => 'IQ',
        'Ireland'                    => 'IE',
        'Italy'                      => 'IT',
        'Jamaica'                    => 'JM',
        'Japan'                      => 'JP',
        'Jordan'                     => 'JO',
        'Korea'                      => 'KR',
        'Kuwait'                     => 'KW',
        'Lebanon'                    => 'LB',
        'Libya'                      => 'LY',
        'Liechtenstein'              => 'LI',
        'Luxembourg'                 => 'LU',
        'Macau'                      => 'MO',
        'Macao SAR'                  => 'MO',
        'Malaysia'                   => 'MY',
        'Mali'                       => 'ML',
        'Mexico'                     => 'MX',
        'Moldava'                    => 'MD',
        'Monaco'                     => 'MC',
        'Morocco'                    => 'MA',
        'Netherlands'                => 'NL',
        'New Zealand'                => 'NZ',
        'Nicaragua'                  => 'NI',
        'Nigeria'                    => 'NG',
        'Norway'                     => 'NO',
        'Oman'                       => 'OM',
        'Pakistan'                   => 'PK',
        'Panama'                     => 'PA',
        'Paraguay'                   => 'PY',
        "People's Republic of China" => 'CN',
        'Peru'                       => 'PE',
        'Philippines'                => 'PH',
        'Poland'                     => 'PL',
        'Portugal'                   => 'PT',
        'PRC'                        => 'CN',
        'Puerto Rico'                => 'PR',
        'Qatar'                      => 'QA',
        'Reunion'                    => 'RE',
        'Russia'                     => 'RU',
        'Saudi Arabia'               => 'SA',
        'Senegal'                    => 'SN',
        'Singapore'                  => 'SG',
        'Slovakia'                   => 'SK',
        'South Africa'               => 'ZA',
        'Spain'                      => 'ES',
        'Sri Lanka'                  => 'LK',
        'Sweden'                     => 'SE',
        'Switzerland'                => 'CH',
        'Syria'                      => 'SY',
        'Taiwan'                     => 'TW',
        'The Netherlands'            => 'NL',
        'Trinidad'                   => 'TT',
        'Tunisia'                    => 'TN',
        'UAE'                        => 'AE',
        'United Kingdom'             => 'GB',
        'United States'              => 'US',
        'Uruguay'                    => 'UY',
        'Venezuela'                  => 'VE',
        'Yemen'                      => 'YE',
        'Zimbabwe'                   => 'ZW',
    );
}
