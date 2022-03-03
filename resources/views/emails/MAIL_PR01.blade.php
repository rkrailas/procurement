@component('mail::message')
# Dear {{ $detailMail['dear'] }}

Please be informed that your Purchase Requisition {{ $detailMail['docno'] }} was updated by {{ $detailMail['changed_by'] }} on {{ $detailMail['changed_on'] }}.

Link : <a href="{{ $detailMail["link_url"] }}">{{ $detailMail['docno'] }}</a>

<br>

<br><span style="color: red">*This is an automatically generated email - please do not reply to it*</span>
@endcomponent