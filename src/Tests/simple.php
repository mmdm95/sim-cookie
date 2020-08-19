<?php

use Sim\Cookie\Cookie;
use Sim\Cookie\SetCookie;

include_once '../../vendor/autoload.php';

$cookie = new Cookie();
$cookieName = 'tmp-cookie';
$cookieSet = new SetCookie($cookieName, 'I am a simple cookie without crypt library. Eat me!', time() + 15);
$cookie->set($cookieSet);

var_dump($cookie->get($cookieName));
var_dump($cookie->get());
