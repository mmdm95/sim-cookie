<?php

use Sim\Cookie\Cookie;
use Sim\Cookie\Interfaces\ISetCookie;
use Sim\Cookie\SetCookie;
use Sim\Cookie\Utils\SameSiteUtil;
use Sim\Crypt\Crypt;

include_once '../../vendor/autoload.php';
//include_once '../../autoloader.php';

$main_key = 'fDhIL1dmU2swMyl+VEUxR3gkJWRJO0RQNUxRUks2aFZZKDJsOVhVYzdCNE52eiEreU9fPkA=';
$assured_key = 'eCtYfHRDOFVsOSV6aTZBNyk6Lyg+MGc0MTI8NTNKTXk=';

$test_agents = [
    'Samsung Galaxy S9' => "Mozilla/5.0 (Linux; Android 8.0.0; SM-G960F Build/R16NW) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.84 Mobile Safari/537.36",
    'Samsung Galaxy S8' => "Mozilla/5.0 (Linux; Android 7.0; SM-G892A Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/60.0.3112.107 Mobile Safari/537.36",
    'Samsung Galaxy S7' => "Mozilla/5.0 (Linux; Android 7.0; SM-G930VC Build/NRD90M; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/58.0.3029.83 Mobile Safari/537.36",
    'Samsung Galaxy S7 Edge' => "Mozilla/5.0 (Linux; Android 6.0.1; SM-G935S Build/MMB29K; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/55.0.2883.91 Mobile Safari/537.36",
    'Nexus 6P' => "Mozilla/5.0 (Linux; Android 6.0.1; Nexus 6P Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.83 Mobile Safari/537.36",
    'Sony Xperia XZ' => "Mozilla/5.0 (Linux; Android 7.1.1; G8231 Build/41.2.A.0.219; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/59.0.3071.125 Mobile Safari/537.36",
    'HTC One M9' => "Mozilla/5.0 (Linux; Android 6.0; HTC One M9 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.98 Mobile Safari/537.3",
    'Apple iPhone 6' => "Mozilla/5.0 (Apple-iPhone7C2/1202.466; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543 Safari/419.3",
    'Mac OS X-based computer using a Safari browser' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.9 (KHTML, like Gecko) Version/9.0.2 Safari/601.3.9",
    'Linux-based PC using a Firefox browser' => "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:15.0) Gecko/20100101 Firefox/15.0.1",
    'Playstation 4' => "Mozilla/5.0 (PlayStation 4 3.11) AppleWebKit/537.73 (KHTML, like Gecko)",
    'chrome 87' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36",
    'chrome 70' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3163.100 Safari/537.36",
    'Apple iPhone XR (Safari)' => "Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0 Mobile/15E148 Safari/604.1",
    'IOS 12' => "Mozilla/5.0 (iPhone; CPU iPhone OS 12_0 like Mac OS X) AppleWebKit/ 604.1.21 (KHTML, like Gecko) Version/ 12.0 Mobile/17A6278a Safari/602.1.26",
    'Safari OSX 10.14' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_5) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.1.1 Safari/605.1.15",
    'Chrome 50' => "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36",
    'Chrome 60' => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.78 Safari/537.36,",
    'UC browser 12.13.5' => "Mozilla/5.0 (Linux; U; Android 10; en-US; GM1911 Build/QKQ1.190716.003) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.13.5.1209 Mobile Safari/537.36",
    'Uc Browser 11.5.1' => "Mozilla/5.0 (Linux; U; Android 6.0.1; zh-CN; F5121 Build/34.0.A.1.247) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/40.0.2214.89 UCBrowser/11.5.1.944 Mobile Safari/537.36",
    'firefox' => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.10; rv:57.0) Gecko/20100101 Firefox/57.0",
];

$cookie = new Cookie(new Crypt($main_key, $assured_key));
$cookieName = 'tmp-cookie';

// comment these two lines after cookie set and check after 15 seconds, the value will be NULL
//$cookieSet = new SetCookie($cookieName, 'I am a simple cookie. Eat me!', time() + 15);

//$cookie->set($cookieSet);

//var_dump($cookie->get($cookieName));

//var_dump($cookie->getAsString(null, true));

// test toString from Cookie class
//$cookieSet = new SetCookie($cookieName, 'A simple cookie', time() + 60);
//$cookieSet->setSameSite(ISetCookie::SAME_SITE_NONE);
//
//$cookieSet2 = new SetCookie($cookieName, 'A simple cookie number 2', time() + 60);
//$cookieSet2->setSameSite(ISetCookie::SAME_SITE_NONE);
//
//echo "<pre>";
//var_dump($cookie->toString($cookieSet, false, true, "Mozilla/5.0 (Linux; U; Android 6.0.1; zh-CN; F5121 Build/34.0.A.1.247) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/40.0.2214.89 UCBrowser/11.5.1.944 Mobile Safari/537.36"));
//var_dump($cookie->toString($cookieSet2, false, true, "Mozilla/5.0 (Linux; U; Android 10; en-US; GM1911 Build/QKQ1.190716.003) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/57.0.2987.108 UCBrowser/12.13.5.1209 Mobile Safari/537.36"));
//echo "</pre>";

// test SameSiteUtil
//foreach ($test_agents as $name => $agent) {
//    echo "<pre>";
//    echo $name . ': ';
//    var_dump(SameSiteUtil::shouldSendSameSiteNone($agent));
//    echo "</pre>";
//}