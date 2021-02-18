@include('notifications.partials.signature')

@component('mail::subcopy')
[{{ $unsubscribeText }}]({{ $unsubscribeUrl }}) &mdash; [{{ $manageSubscriptionText }}]({{ $manageSubscriptionUrl }})
@endcomponent
