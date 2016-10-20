#!/usr/bin/python
# -*- coding: utf-8 -*-

import sys
from pprint import pprint

# Подключаем top.mail.ru
from top_mail_ru import TopMailRu

EMAIL='__MY__EMAIL__'
PASSWORD='__MY__PASSWORD__'
MY_SITE='http://__MY__SITE__NAME__'
# Ключ к API, требуется для регистрации сайтов.
# Для получения ключа напишите нам на https://top.mail.ru/feedback
# см. https://help.mail.ru/top/API/main
API_KEY='__API__KEY__'

# 1-й аргумент - API key
tmr = TopMailRu(API_KEY)

# registerSite() - Регистрация сайта
# см. https://help.mail.ru/top/API/main - Регистрация сайта и управление
result = tmr.registerSite({
        'title' : 'my site', # название нового счетчика
        'url' : MY_SITE, # url ресурса где счетчик будет размешен
        'email' : EMAIL, # email
        'password' : PASSWORD, # пароль
        'public' : 0, # 1 - счетчик публичный, 0 - отчет закрытый
        'rating' : 0, # 1 - счетчик участвует в райтинге - 1, 0 - не учавствует
        # id категории, 0 - нет категории.
        # Для получения списка категорий можно воспользоваться /json/categories,
        # Опционально, требуется в случае участия в рейтинге (rating: 1).
        # Для участия в рейтинге выберите наиболее подходящую для Вашего ресурса категорию Рейтинга@Mail.ru.
        # Правильный выбор категории обеспечит Вам наибольший приток целевой аудитории со страниц Рейтинга@Mail.ru.
        # А также поможет сравнить популярность своего ресурса с популярностью ресурсов конкурентов.
        # см. https://help.mail.ru/top/API/response - Информация о категориях рейтинга
        'category' : 0
        })
if 'error' in result:
    print('registerSite(), error')
    pprint(result)
    sys.exit(1)

counter_id = result['id']

# login() - авторизированиться по паролю
# 1-й аргумент - id счетчика
# 2-й аргумент - пароль к счетчику
result = tmr.login(counter_id, PASSWORD);
if not result:
    print('login() error')
    sys.exit(1)

# getCode() - получить код счетчика
# 1-й аргумент id счетчика
# 2-й аргумент - пароль, если вы ранее и успешно вызвали login/loginByHash, то аргумент можно оставить пустым
# 3-й аргумент - опции (см. https://help.mail.ru/top/API/main Код счетчика)
result = tmr.getCode(counter_id, '', { 'mode' : 'nologo', 'pagetype' : 'xhtml' })
if 'error' in result:
    print('getCode() error')
    pprint(result)
    sys.exit(1)

print('Code')
pprint(result)

# getStat() - получить данные отчета
# 1-й аргумент - id счетчика
# 2-й аргумент - пароль, если вы ранее и успешно вызвали login/loginByHash, то аргумент можно оставить пустым
# 3-й аргумент - тип отчета (см. https://help.mail.ru/top/API/response)
# 4-й аргумент - аргументы конкретной статистики
# Более детально см. параметры запросов (https://help.mail.ru/top/API/params) и
# описания JSON ответов (https://help.mail.ru/top/API/response)
result = tmr.getStat(counter_id, PASSWORD, 'visits', { 'period' : 1 })
if 'error' in result:
    print('getStat(), error')
    pprint(result)
    sys.exit(1)

print('Counter ' + counter_id + ', visits')
# Результат зависит от типа отчета (см. https://help.mail.ru/top/API/response)
pprint(result)
