from top_mail_ru import TopMailRu

tmr = TopMailRu('fabfd4e262335f6a718a51e876372e51')

tmr.registerSite({
        'title' : 'my site',
        'url' : 'http://mysite.domain',
        'email' : 'mylogin@mail.ru',
        'password' : 'mypass12345678',
        'public' : 0,
        'rating' : 0,
        'category' : 0
        })

id = 2470518
tmr.login(id, '12345678');

tmr.editSite(id, '', {
        'title' : 'my cool site',
        'url' : 'http://mysite.domain',
        'email' : 'mylogin@mail.ru',
        'rating' : 0,
        'category' : 0
        })
tmr.getCode(id, '', { 'mode' : 'nologo', 'pagetype' : 'xhtml' })
tmr.getStat(id, '', 'visits', { 'period' : 1 })
