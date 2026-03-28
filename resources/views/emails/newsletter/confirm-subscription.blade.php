@component('mail::message')
# Подтвердите подписку

Нажмите кнопку ниже, чтобы подтвердить подписку на рассылку Книжной лавки.

@component('mail::button', ['url' => $confirmUrl ?? '#'])
Подтвердить подписку
@endcomponent

Если вы не подписывались, просто проигнорируйте это письмо.

С уважением,<br>
{{ config('app.name') }}
@endcomponent
