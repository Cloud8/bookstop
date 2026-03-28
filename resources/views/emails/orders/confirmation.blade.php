@component('mail::message')
# Ваш заказ подтверждён

{{-- Phase 5: order details will be rendered here --}}

С уважением,<br>
{{ config('app.name') }}
@endcomponent
