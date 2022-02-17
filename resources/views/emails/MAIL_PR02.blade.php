@component('mail::message')
# Dear {{ $detailMail['dear'] }}

Please be informed that your Purchase Requisition No: {{ $detailMail['docno'] }} has been approved by {{ $detailMail['actionby'] }}.

Link : <a href="{{ $detailMail["link_url"] }}">{{ $detailMail['docno'] }}</a>

<br>
Regards,
<br>{{ $detailMail['actionby'] }} 

<br><span style="color: red">*This is an automatically generated email - please do not reply to it*</span>
@endcomponent
