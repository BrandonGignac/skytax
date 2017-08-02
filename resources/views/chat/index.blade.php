@extends('layouts.app')

@section('page-title', 'List of Chats')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Chat
                <small>Real-time transmission of text messages</small>
                <div class="pull-right">
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                        <li class="active">Chat</li>
                    </ol>
                </div>
            </h1>
        </div>
    </div>

    @include('partials.messages')

    <div class="row tab-search">
        <div class="col-md-2">
            <a href="{{ route('chat.create') }}" class="btn btn-success">
                <i class="glyphicon glyphicon-plus"></i>
                Add Chat
            </a>
        </div>
    </div>

    <div class="table-responsive" id="users-table-wrapper">
        <table class="table">
            <thead>
            <th>Title</th>
            <th>Slug</th>
            <th># of messages</th>
            <th>Participants</th>
            <th>Created at</th>
            <th class="text-center">@lang('app.action')</th>
            </thead>
            <tbody>
            @if (count($chats))
                @foreach ($chats as $chat)
                    <tr>
                        <td>{{ $chat->title }}</td>
                        <td>{{ $chat->slug }}</td>
                        <td>{{ count($chat->messages) }}</td>
                        <td>
                            @forelse ($chat->users as $user)
                                <span class="label label-default">
                                    {{ $user->username ?: $user->first_name . ' ' . $user->last_name }}
                                </span>
                                &nbsp
                            @empty
                                <span class="label label-danger">
                                    There are no participants yet
                                </span>
                            @endforelse
                        </td>
                        <td>{{ $chat->created_at->format('Y-m-d') }}</td>
                        <td class="text-center">
                            <a href="{{ route('chat.edit', $chat->id) }}" class="btn btn-primary btn-circle edit"
                               title="Edit Chat" data-toggle="tooltip" data-placement="top">
                                <i class="glyphicon glyphicon-edit"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4"><em>@lang('app.no_records_found')</em></td>
                </tr>
            @endif
            </tbody>
        </table>
        {!! $chats->render() !!}
    </div>
@stop
