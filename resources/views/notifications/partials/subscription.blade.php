{!! Config::get('setting.mail_signature') !!}
@component('mail::button', ['url' => $manageSubscriptionUrl])
    {{ $manageSubscriptionText }}
@endcomponent
@component('mail::subcopy')
    [{{ $unsubscribeText }}]({{ $unsubscribeUrl }})
@endcomponent
