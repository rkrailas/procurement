@component('mail::message')
# Dear {{ $detailMail['dear'] }}

New Purchase Requisition No: {{ $detailMail['docno'] }} has been released by {{ $detailMail['releasedby'] }} and requesting your approval.
Please review the Purchase Requisition No: {{ $detailMail['docno'] }} before approving or rejecting it.

Link : <a href="{{ $detailMail["link_url"] }}">{{ $detailMail['docno'] }}</a>
{{-- @component('mail::button', ['url' => '{{ $detailMail["link_url"] }}'])
[Link to PR No]
@endcomponent --}}


<br>
Regards,
<br>{{ $detailMail['releasedby'] }}

<br><span style="color: red">*This is an automatically generated email - please do not reply to it*</span>
@endcomponent


