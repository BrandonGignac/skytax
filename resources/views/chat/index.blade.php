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
            <th>Secret slug</th>
            <th># of messages</th>
            <th class="text-center">@lang('app.action')</th>
            </thead>
            <tbody>
            @if (count($chats))
                @foreach ($chats as $chat)
                    <tr>
                        <td><a href="{{ route('chat.show', $chat->slug) }}">{{ $chat->title }}</a></td>
                        <td>{{ $chat->slug }}</td>
                        <td>{{ count($chat->messages) }}</td>
                        <td class="text-center">
                            <a href="{{ route('chat.show', $chat->slug) }}" class="btn btn-success btn-circle"
                               title="View Chat" data-toggle="tooltip" data-placement="top">
                                <i class="glyphicon glyphicon-eye-open"></i>
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
    </div>
@stop
