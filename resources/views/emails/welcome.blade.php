@component('mail::message')
# Introduction
Template : {{ $detailMail['template'] }}
Dear : {{ $detailMail['dear'] }}
This is PR No. : {{ $detailMail['docno'] }}


@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
