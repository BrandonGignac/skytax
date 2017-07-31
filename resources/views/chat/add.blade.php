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

    {!! Form::open(['route' => 'chat.store', 'id' => 'chat-form']) !!}
    <div class="row">
        <div class="col-lg-6 col-md-12 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">Chat Details</div>
                <div class="panel-body">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input id="title" type="text" name="title" class="form-control" value="{{ old('title') }}">
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
@stop

@section('scripts')
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Chat\CreateChatRequest', '#chat-form') !!}
@stop
