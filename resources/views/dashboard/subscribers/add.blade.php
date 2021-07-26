@extends('layout.dashboard')

@section('content')
<div class="header">
    <div class="sidebar-toggler visible-xs">
        <i class="ion ion-navicon"></i>
    </div>
    <span class="uppercase">
        <i class="ion ion-ios-email-outline"></i> {{ trans('dashboard.subscribers.channel.email.subscribers') }}
    </span>
</div>
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-12">
        @include('partials.errors')
        <form name="SubscriberForm" class="form-vertical" role="form" action="{{ cachet_route('dashboard.subscribers.create', [], 'post') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <fieldset>
            <div class="form-group">
                <label for="subscriber-name">{{ trans('forms.channel.email.name') }}</label>
                <input type="text" class="form-control" name="subscriber[name]" id="subscriber-name" required placeholder="{{ trans('forms.channel.email.name-help') }}">
            </div>
            <div class="form-group">
                <label for="subscriber-email">{{ trans('forms.channel.email.email') }}</label>
                <input type="email" class="form-control" name="subscriber[email]" id="subscriber-email">
            </div>
            </fieldset>

            <div class="form-group">
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">{{ trans('forms.add') }}</button>
                    <a class="btn btn-default" href="{{ cachet_route('dashboard.subscribers') }}">{{ trans('forms.cancel') }}</a>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>
@stop
