<?php

include_once 'top_mail_ru.php';

$tmr = new TopMailRu('fabfd4e262335f6a718a51e876372e51', false);
$tmr->registerSite(array(
	'title' => 'my site',
	'url' => 'http://mysite.domain',
	'email' => 'mylogin@mail.ru',
	'password' => 'mypass12345678',
	'public' => 0, // - 0/1
	'rating' => 0, // - 0/1
	'category' => 0 //опционально, требуется в случае rating=1
	));

$id = 2470518;
$tmr->login($id, '12345678');

$tmr->editSite($id, '', array(
	'title' => 'my cool site',
	'url' => 'http://mysite.domain',
	'email' => 'mylogin@mail.ru',
	'rating' => 0, // - 0/1
	'category' => 0 //опционально, требуется в случае rating=1
	));
$tmr->getCode($id, '', array(
	'mode' => 'nologo',
	'pagetype' => 'xhtml'
	));
$tmr->getStat($id, '', 'visits', array(
	'period' => 1,
	));

