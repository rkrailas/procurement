@component('mail::message')
# Dear {{ $detailMail['dear'] }}

Please be informed that your Purchase Requisition No: {{ $detailMail['docno'] }} has been cancelled by {{ $detailMail['cancel_by'] }}.

Link : <a href="{{ $detailMail["link_url"] }}">{{ $detailMail['docno'] }}</a>

<br>
Regards,
<br>{{ $detailMail['cancel_by'] }}

<br><span style="color: red">*This is an automatically generated email - please do not reply to it*</span>
@endcomponent