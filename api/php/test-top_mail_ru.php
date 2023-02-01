<?php

// Подключаем top.mail.ru
include_once __DIR__ . '/top_mail_ru.php';

$EMAIL = '__MY__EMAIL__@my.com';
$PASSWORD = '__MY__PASSWORD__';
$MY_SITE = 'http://__MY__SITE__NAME__.domain.3';
// Ключ к API, требуется ТОЛЬКО для регистрации сайтов.
// Для получения ключа напишите нам на https://top.mail.ru/feedback
// В других случаях __API__KEY__ удалите и API_KEY оставьте пустым.
// см. https://top.mail.ru/help/ru/api/main
$API_KEY = '__API__KEY__';

// 1-й аргумент API KEY
// Ключ к API, требуется для регистрации сайтов.
// Для получения ключа напишите нам на https://top.mail.ru/feedback
// см. https://top.mail.ru/help/ru/api/main
$tmr = new TopMailRu($API_KEY, false);

// registerSite() - Регистрация сайта
// с.м. https://top.mail.ru/help/ru/api/main - Регистрация сайта и управление
$result = $tmr->registerSite(array(
    'title' => 'my site', // название нового счетчика
    'url' => $MY_SITE, // url ресурса где счетчик будет размешен
    'email' => $EMAIL, // email
    'password' => $PASSWORD, // пароль
    'public' => 0, // 1 - счетчик публичный, 0 - отчет закрытый
    'rating' => 0, // 1 - счетчик участвует в райтинге - 1, 0 - не учавствует
    // id категории, 0 - нет категории.
    // Для получения списка категорий можно воспользоваться /json/categories,
    // Опционально, требуется в случае участия в рейтинге (rating: 1).
    // Для участия в рейтинге выберите наиболее подходящую для Вашего ресурса категорию Рейтинга@Mail.ru.
    // Правильный выбор категории обеспечит Вам наибольший приток целевой аудитории со страниц Рейтинга@Mail.ru.
    // А также поможет сравнить популярность своего ресурса с популярностью ресурсов конкурентов.
    // см. https://top.mail.ru/help/ru/api/response - Информация о категориях рейтинга
    'category' => 0
));
if (array_key_exists('error', $result)) {
    echo 'registerSite() error';
    print_r($result);
    exit(1);
}

$counterId = $result->id;

// login() - авторизированиться по паролю
// 1-й аргумент - id счетчика
// 2-й аргумент - пароль к счетчику
if (!$tmr->login($counterId, $PASSWORD)) {
    echo 'login() error';
    exit(1);
}

// getCode() - получить код счетчика
// 1-й аргумент id счетчика
// 2-й аргумент - пароль, если вы ранее и успешно вызвали login, то аргумент можно оставить пустым
// 3-й аргумент - опции (см. https://top.mail.ru/help/ru/api/main Код счетчика)
$result = $tmr->getCode($counterId, $PASSWORD, array(
    'mode' => 'nologo',
    'pagetype' => 'xhtml'
));
if (array_key_exists('error', $result)) {
    echo 'getCode() error';
    print_r($result);
    exit(1);
}

echo 'Code';
print_r($result);

// getStat() - получить данные отчета
// 1-й аргумент - id счетчика
// 2-й аргумент - пароль, если вы ранее и успешно вызвали login, то аргумент можно оставить пустым
// 3-й аргумент - тип отчета (см. https://top.mail.ru/help/ru/api/response)
// 4-й аргумент - аргументы конкретной статистики
// Более детально см. параметры запросов (https://top.mail.ru/help/ru/api/params) и
// описания JSON ответов (https://top.mail.ru/help/ru/api/response)
$tmr->getStat($counterId, $PASSWORD, 'visits', array(
    'period' => 1,
));
if (array_key_exists('error', $result)) {
    echo 'getStat(), error';
    print_r($result);
    exit(1);
}

echo 'Visits';
print_r($result);
