@component('mail::message')
# Подтвердите ваш email

Здравствуйте, {{ $user->name }}!

Нажмите на кнопку ниже, чтобы подтвердить ваш email-адрес.

@component('mail::button', ['url' => $url])
Подтвердить email
@endcomponent

Если вы не создавали аккаунт, просто проигнорируйте это письмо.

С уважением,<br>
{{ config('app.name') }}

@component('mail::subcopy')
Если кнопка не работает, скопируйте и вставьте эту ссылку в браузер: [{{ $url }}]({{ $url }})
@endcomponent
@endcomponent
