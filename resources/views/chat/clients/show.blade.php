@extends('layouts.app')

@section('page-title', 'Chat')

@section('content')
    <style>
        #chatMessages {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 100px;
            list-style: none;
            padding: 10px;
            height: 300px;
            overflow-y: auto;
        }

        .message.system span.who {
            color: #d43f3a;
        }

        .message.user span.who {
            color: #337ab7;
        }

        .message.mine span.who {
            font-weight: bold;
        }

        .chat-nav-tabs {
            border-bottom: none !important;
        }

        .chat-msg,
        .chat-text {
            display: block;
        }

        .chat-msg {
            margin-bottom: 10px;
        }

        .chat-msg:before,
        .chat-msg:after {
            content: " ";
            display: table;
        }

        .chat-msg:after {
            clear: both;
        }

        .chat-text {
            border-radius: 5px;
            position: relative;
            padding: 5px 10px;
            background: #d2d6de;
            border: 1px solid #d2d6de;
            margin: 5px 0 0 50px;
            color: #444444;
        }

        .chat-text:after,
        .chat-text:before {
            position: absolute;
            right: 100%;
            top: 15px;
            border: solid transparent;
            border-right-color: #d2d6de;
            content: ' ';
            height: 0;
            width: 0;
            pointer-events: none;
        }

        .chat-text:after {
            border-width: 5px;
            margin-top: -5px;
        }

        .chat-text:before {
            border-width: 6px;
            margin-top: -6px;
        }

        .right .chat-text {
            margin-right: 50px;
            margin-left: 0;
        }

        .right .chat-text:after,
        .right .chat-text:before {
            right: auto;
            left: 100%;
            border-right-color: transparent;
            border-left-color: #d2d6de;
        }

        .chat-img {
            border-radius: 50%;
            float: left;
            width: 40px;
            height: 40px;
        }

        .right .chat-img {
            float: right;
        }

        .chat-info {
            display: block;
            margin-bottom: 2px;
            font-size: 12px;
        }

        .chat-name {
            font-weight: 600;
        }

        .chat-timestamp {
            color: #999;
        }

        .chat-primary .right > .chat-text {
            background: #3c8dbc;
            border-color: #3c8dbc;
            color: #ffffff;
        }

        .chat-primary .right > .chat-text:after,
        .chat-primary .right > .chat-text:before {
            border-left-color: #3c8dbc;
        }
    </style>

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

    @if (count($chats))
        <ul class="nav nav-tabs chat-nav-tabs">
            @foreach ($chats as $id => $item)
                <li class="{{ basename(request()->path()) === $item->slug ? 'active' : '' }}">
                    <a href="{{ $item->slug }}">
                        {{ $item->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <em>@lang('app.no_records_found')</em>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body chat-primary" id="chat">
                    <div style="display:table; width: 100%; margin-bottom: 10px;"></div>
                    <div id="chatMessages">
                        @foreach ($chat->messages as $message)
                            @if ($message->user->email === $user->email)
                                <div class="chat-msg">
                                    <div class="chat-info clearfix">
                                        <span class="chat-name pull-left">
                                            <span class="message mine">
                                                <span class="who">
                                                     Me
                                                    <br>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="chat-timestamp pull-right">{{ Carbon\Carbon::parse($message->created_at)->format('d-m-y H:i') }}</span>
                                    </div>
                                    <img class="chat-img img-circle avatar"
                                         src="{{ $message->user->present()->avatar }}" alt="User image">
                                    <div class="chat-text">
                                        {!! nl2br(e($message->body)) !!}
                                    </div>
                                </div>
                            @else
                                <div class="chat-msg right">
                                    <div class="chat-info clearfix">
                                        <span class="chat-name pull-right">
                                            <span class="message user ">
                                                <span class="who">
                                                    {{ $message->user->username ?: $message->user->first_name . $message->user->last_name }}
                                                    <br>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="chat-timestamp pull-left">{{ Carbon\Carbon::parse($message->created_at)->format('d-m-y H:i') }}</span>
                                    </div>
                                    <img class="chat-img img-circle avatar"
                                         src="{{ $message->user->present()->avatar }}" alt="User image">
                                    <div class="chat-text">
                                        {!! nl2br(e($message->body)) !!}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <div v-for="message in messages" class="message" :class="message.class">
                            <div class="chat-msg">
                                <div class="chat-info clearfix">
                                        <span class="chat-name pull-left">
                                             <span class="who" v-text="message.who"></span><br>
                                        </span>
                                    <span class="chat-timestamp pull-right" v-text="message.created_at"></span>
                                </div>
                                <img class="chat-img img-circle avatar" :src="message.avatar" alt="User image">
                                <div class="chat-text" style="white-space: pre;" v-text="message.msg"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div style="display:table; width: 100%; margin-bottom: 10px;"></div>
                        <div class="form-group">
                            <textarea rows="3" class="form-control" placeholder="Type your message here..."
                                      v-model="newMessage"></textarea>
                        </div>
                        <div class="form-group">
                            <a @click="sendMessage" :disabled="isClosed" class="pull-right btn btn-success">Send</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.4.2/vue.min.js"></script>
    <script>
        var vue = new Vue({
            el: '#chat',
            data: {
                messages: [],
                newMessage: "",
                userName: "",
                userAvatar: "",
                uri: "",
                conn: {},
                isClosed: false
            },
            mounted: function () {
                this.userName = "{{ $user->username ?: $user->first_name . ' ' . $user->last_name }}";
                this.userAvatar = "{{ $user->present()->avatar }}";
                this.uri = "{{ $url }}";
                this.conn = new WebSocket('wss://' + this.uri + ':8020');
                var me = this;
                this.conn.onclose = function (e) {
                    me.addSystemMessage("Connection closed");
                    this.isClosed = true;
                    console.log(e.code);
                }.bind(this);
                this.conn.onopen = function (event) {
                    this.scrollMessagesDown();
                    setInterval(function () {
                        this.conn.send('ping');
                    }.bind(this), 15000);
                }.bind(this);
                this.conn.onmessage = function (event) {
                    me.addServerMessage(event.data);
                };
            },
            methods: {
                addSystemMessage: function (message) {
                    this.addMessage({
                        "created_at": moment().utc().format('DD-MM-YY H:mm'),
                        "avatar": "/assets/img/vanguard-logo-no-text.png",
                        "msg": message,
                        "class": "system",
                        "who": "System"
                    });
                },
                addServerMessage: function (message) {
                    message = JSON.parse(message);
                    this.addMessage({
                        "created_at": message.created_at,
                        "avatar": message.avatar,
                        "msg": message.body,
                        "class": "user",
                        "who": message.name
                    });
                },
                addMeAmessage: function (message, avatar) {
                    this.addMessage({
                        "created_at": moment().utc().format('DD-MM-YY H:mm'),
                        "avatar": avatar,
                        "msg": message,
                        "class": "mine",
                        "who": "Me"
                    });
                },
                addMessage: function (message) {
                    this.messages.push(message);
                    Vue.nextTick(function () {
                        this.scrollMessagesDown();
                    }.bind(this));
                },
                scrollMessagesDown: function () {
                    var chatMessages = document.getElementById('chatMessages');
                    chatMessages.scrollTop = 1000000;
                },
                sendMessage: function () {
                    if (!this.newMessage.length)
                        return;
                    var msgToSend = {
                        'name': this.userName,
                        'body': this.newMessage,
                        'avatar': this.userAvatar,
                        'created_at': moment().utc().format('DD-MM-YY H:mm')
                    };
                    this.conn.send(JSON.stringify(msgToSend));
                    this.addMeAmessage(this.newMessage, this.userAvatar);
                    this.newMessage = "";
                }
            }
        });
    </script>
@stop

@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
@stop