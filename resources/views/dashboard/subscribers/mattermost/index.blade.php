@extends('layout.dashboard')

@section('content')
<div class="content-panel">
    @includeWhen(isset($subMenu), 'dashboard.partials.sub-sidebar')
    <div class="content-wrapper">
        <div class="header sub-header">
            <span class="uppercase">
                <i class="ion ion-paper-airplane"></i> {{ trans('dashboard.subscribers.channel.mattermost.subscribers') }}
            </span>
            @if($currentUser->isAdmin)
            <a class="btn btn-md btn-success pull-right" href="{{ cachet_route('dashboard.subscribers.mattermost.create') }}">
                {{ trans('dashboard.subscribers.add.title') }}
            </a>
            @endif
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <p class="lead">
                    {{ trans('dashboard.subscribers.channel.mattermost.description') }}
                </p>

                <div class="striped-list">
                    @foreach($subscribers as $subscriber)
                    <div class="row striped-list-item">
                        <div class="col-xs-2">
                            <p>{{ $subscriber->name }}</p>
                        </div>
                        <div class="col-xs-2">
                            <p>{{ trans('dashboard.subscribers.subscribed_at', ['date' => $subscriber->created_at]) }}</p>
                        </div>
                        <div class="col-xs-2">
                            @if($subscriber->global)
                            <p>{{ trans('dashboard.subscribers.global') }}</p>
                            @endif
                        </div>
                        <div class="col-xs-2">
                            @if($subscriber->subscriptions->isNotEmpty())
                            {!! $subscriber->subscriptions->map(function ($subscription) {
                                return sprintf('<span class="label label-primary">%s</span>', $subscription->component->name);
                            })->implode(' ') !!}
                            @else
                            <p>{{ trans('dashboard.subscribers.no_subscriptions') }}</p>
                            @endif
                        </div>
                        <div class="col-xs-2">
                            @if($subscriber->maintenance_schedules)
                                <p>{{ trans('dashboard.subscribers.maintenances') }}</p>
                            @endif
                        </div>
                        <div class="col-xs-2 text-right">
                            <a href="{{ URL::signedRoute(cachet_route_generator('subscribe.manage'), ['code' => $subscriber->verify_code]) }}" target="_blank" class="btn btn-success">{{ trans('forms.edit') }}</a>
                            <a href="{{ cachet_route('dashboard.subscribers.mattermost.delete', [$subscriber->id], 'delete') }}" class="btn btn-danger confirm-action" data-method='DELETE'>{{ trans('forms.delete') }}</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@stop
