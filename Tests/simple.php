<?php

use Sim\Cookie\Cookie;

include_once '../vendor/autoload.php';

$cookie = new Cookie();
$cookieName = 'tmp-cookie';
$cookieSet = $cookie->set($cookieName);
$cookieSet
    ->setValue('I am a simple cookie without raw value. Eat me!')
    ->setExpiration(time() + 15);

var_dump($cookie->get($cookieName));
//var_dump($cookie->get());
