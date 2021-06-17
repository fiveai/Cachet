@extends('layout.dashboard')

@section('content')
<div class="header">
    <div class="sidebar-toggler visible-xs">
        <i class="ion ion-navicon"></i>
    </div>
    <span class="uppercase">
        <i class="ion ion-paper-airplane"></i> {{ trans('dashboard.subscribers.channel.mattermost.subscribers') }}
    </span>
</div>
<div class="content-wrapper">
    <div class="row">
        <div class="col-sm-12">
        @include('partials.errors')
        <form name="SubscriberMattermostForm" class="form-vertical" role="form" action="{{ cachet_route('dashboard.subscribers.mattermost.create', [], 'post') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <fieldset>
            <div class="form-group">
                <label for="mattermost-name">{{ trans('forms.channel.mattermost.name') }}</label>
                <input type="text" class="form-control" name="subscriber[name]" id="mattermost-name" required placeholder="{{ trans('forms.channel.mattermost.name-help') }}">
            </div>
            <div class="form-group">
                <label for="mattermost-hook">{{ trans('forms.channel.mattermost.hook') }}</label>
                <input type="text" class="form-control" name="subscriber[hook]" id="mattermost-hook" required placeholder="{{ trans('forms.channel.mattermost.hook-help') }}">
            </div>
            </fieldset>

            <div class="form-group">
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">{{ trans('forms.add') }}</button>
                    <a class="btn btn-default" href="{{ cachet_route('dashboard.subscribers.mattermost') }}">{{ trans('forms.cancel') }}</a>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>
@stop
