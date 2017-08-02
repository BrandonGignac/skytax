@extends('layouts.app')

@section('page-title', 'Create Chat')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                Create New Chat
                <small>Chat Details</small>
                <div class="pull-right">
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                        <li><a href="{{ route('chat.list') }}">Chats</a></li>
                        <li class="active">Create</li>
                    </ol>
                </div>
            </h1>
        </div>
    </div>

    @include('partials.messages')

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#permissions" aria-controls="details" role="tab" data-toggle="tab">
                <i class="fa fa-unlock" aria-hidden="true"></i>
                Permissions
            </a>
        </li>
        <li role="presentation">
            <a href="#details" aria-controls="details" role="tab" data-toggle="tab">
                <i class="glyphicon glyphicon-th"></i>
                Details
            </a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="permissions">
            <div class="row tab-search">
                <div class="col-md-2"></div>
                <div class="col-md-5"></div>
                <form method="GET" action="" accept-charset="UTF-8" id="users-form">
                    <div class="col-md-2"></div>
                    <div class="col-md-3">
                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" name="search" value="{{ Input::get('search') }}"
                                   placeholder="@lang('app.search_for_users')">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit" id="search-users-btn">
                        <span class="glyphicon glyphicon-search"></span>
                    </button>
                    @if (Input::has('search') && Input::get('search') != '')
                        <a href="{{ route('chat.create') }}" class="btn btn-danger" type="button">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                    @endif
                </span>
                        </div>
                    </div>
                </form>
            </div>
            {!! Form::open(['route' => 'chat.store', 'id' => 'chat-form']) !!}
            <div class="row">
                <div class="col-lg-12">
                    <div class="table-responsive top-border-table" id="users-table-wrapper">
                        <table class="table">
                            <thead>
                            <th>@lang('app.username')</th>
                            <th>@lang('app.full_name')</th>
                            <th>@lang('app.email')</th>
                            <th>@lang('app.registration_date')</th>
                            <th>Role</th>
                            <th>Permission to talk</th>
                            </thead>
                            <tbody>
                            @if (count($users))
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->username ?: trans('app.n_a') }}</td>
                                        <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $user->role->name }}</td>
                                        <td class="text-center">
                                            <div class="checkbox">
                                                {!! Form::checkbox('users[]', $user->id ) !!}
                                                <label class="no-content"></label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6"><em>@lang('app.no_records_found')</em></td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        {!! $users->render() !!}
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="details">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">Chat Details</div>
                        <div class="panel-body">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input id="title" type="text" name="title" class="form-control"
                                       value="{{ old('title') }}">
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i>
                                            Create Chat
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('scripts')
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Chat\ChatRequest', '#chat-form') !!}
@stop
