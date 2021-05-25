@component('mail::message')
    Your verification code:
    {{$code}}

    Thanks,
    {{ config('app.name') }}
@endcomponent
