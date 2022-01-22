@component('mail::message')
# Dear {{ $detailMail['dear'] }}

Please be informed that your Purchase Requisition No: {{ $detailMail['docno'] }} has been rejected by {{ $detailMail['actionby'] }} for the following reasons:
{{ $detailMail['reasons'] }}


Please review Purchase Requisition No: {{ $detailMail['docno'] }} before reopening this request.

<br>
Regards,
<br>{{ $detailMail['actionby'] }} 

<br><span style="color: red">*This is an automatically generated email - please do not reply to it*</span>
@endcomponent
