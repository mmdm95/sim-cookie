<?php

use Sim\Cookie\Cookie;
use Sim\Cookie\SetCookie;
use Sim\Crypt\Crypt;

//include_once '../../vendor/autoload.php';

//include_once '../../vendor/mmdm/sim-crypt/autoloader.php';
//include_once '../../autoloader.php';

$main_key = 'fDhIL1dmU2swMyl+VEUxR3gkJWRJO0RQNUxRUks2aFZZKDJsOVhVYzdCNE52eiEreU9fPkA=';
$assured_key = 'eCtYfHRDOFVsOSV6aTZBNyk6Lyg+MGc0MTI8NTNKTXk=';

$cookie = new Cookie(new Crypt($main_key, $assured_key));
$cookieName = 'tmp-cookie';

// comment these two lines after cookie set and check after 15 seconds, the value will be NULL
$cookieSet = new SetCookie($cookieName, 'I am a simple cookie. Eat me!', time() + 15);
$cookie->set($cookieSet);

var_dump($cookie->get($cookieName));
//var_dump($cookie->get());

//var_dump($cookie->getAsString(null, true));
