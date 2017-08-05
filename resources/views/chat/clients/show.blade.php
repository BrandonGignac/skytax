@extends('layouts.app')

@section('page-title', 'Chat')

@section('styles')
    {!! HTML::style('assets/css/chat.css') !!}
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                {{ $chat->title }}
                <small>click toggle menu to see all the conversations</small>
                <div class="pull-right">
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                        <li class="active">Chat</li>
                    </ol>
                </div>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body chat-primary" id="chat">
                    <div class="sidebar-submenu">
                        <div @click="showMenu = !showMenu" class="mini-submenu" style="display:block;">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </div>
                        @if (count($chats))
                            <div class="list-group" v-if="showMenu">
                                <span class="list-group-item">
                                     <b>My Conversations</b>
                                </span>
                                @foreach ($chats as $item)
                                    <a class="list-group-item {{ basename(request()->path()) === $item->slug ? 'active' : '' }}"
                                       href="{{ $item->slug }}">{{ $item->title }}</a>
                                @endforeach
                            </div>
                        @else
                            <em>@lang('app.no_records_found')</em>
                        @endif
                    </div>
                    <div id="chatMessages" ref="chatMessages">
                        @foreach ($chat->messages as $message)
                            @if ($message->user->email === $user->email)
                                <div class="chat-msg right">
                                    <div class="chat-info clearfix">
                                        <span class="chat-name pull-right">
                                            <span class="message mine">
                                                <span class="who">
                                                     Me
                                                    <br>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="chat-timestamp pull-left">{{ Carbon\Carbon::parse($message->created_at)->format('d-m-y H:i') }}</span>
                                    </div>
                                    <img class="chat-img img-circle avatar"
                                         src="{{ $message->user->present()->avatar }}" alt="User image">
                                    <div class="chat-text" style="word-wrap:break-word;">
                                        {!! nl2br(e($message->body)) !!}
                                    </div>
                                </div>
                            @else
                                <div class="chat-msg">
                                    <div class="chat-info clearfix">
                                        <span class="chat-name">
                                            <span class="message user pull-left">
                                                <span class="who">
                                                    {{ $message->user->username ?: $message->user->first_name . $message->user->last_name }}
                                                    <br>
                                                </span>
                                            </span>
                                        </span>
                                        <span class="chat-timestamp pull-right">{{ Carbon\Carbon::parse($message->created_at)->format('d-m-y H:i') }}</span>
                                    </div>
                                    <img class="chat-img img-circle avatar"
                                         src="{{ $message->user->present()->avatar }}" alt="User image">
                                    <div class="chat-text" style="word-wrap:break-word;">
                                        {!! nl2br(e($message->body)) !!}
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        <div v-for="message in messages" class="message" :class="message.class">
                            <div v-if="message.who === 'Me'">
                                <div class="chat-msg right">
                                    <div class="chat-info clearfix">
                                            <span class="chat-name pull-right">
                                                 <span class="who" v-text="message.who"></span><br>
                                            </span>
                                        <span class="chat-timestamp pull-left" v-text="message.created_at"></span>
                                    </div>
                                    <img class="chat-img img-circle avatar" :src="message.avatar" alt="User image">
                                    <div class="chat-text" style="white-space:pre-wrap;word-wrap:break-word;"
                                         v-text="message.msg"></div>
                                </div>
                            </div>
                            <div v-else>
                                <div class="chat-msg">
                                    <div class="chat-info clearfix">
                                            <span class="chat-name pull-left">
                                                 <span class="who" v-text="message.who"></span><br>
                                            </span>
                                        <span class="chat-timestamp pull-right" v-text="message.created_at"></span>
                                    </div>
                                    <img class="chat-img img-circle avatar" :src="message.avatar" alt="User image">
                                    <div class="chat-text" style="white-space:pre-wrap;word-wrap:break-word;"
                                         v-text="message.msg"></div>
                                </div>
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
                isClosed: false,
                showMenu: false
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
                    var chatMessages = this.$refs.chatMessages;
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