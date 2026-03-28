@component('mail::message')
{{-- Phase 8: newsletter body will be rendered here --}}

---
[Отписаться]({{ $unsubscribeUrl ?? '#' }})
@endcomponent
