@component('mail::message')
# Introduction

This is PR No. : {{ $prno }}


@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
