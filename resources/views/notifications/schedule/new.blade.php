@component('mail::message')
# {{$dateNumber}}{{$name}}

{{$message}}

{{$date}}
@include('notifications.partials.subscription')
@endcomponent
