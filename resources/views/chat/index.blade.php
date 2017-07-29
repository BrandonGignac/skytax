@extends('layouts.app')

@section('content')
    <style>
        #chatMessages {
            width: 100%;
            border: 1px solid #ddd;
            min-height: 100px;
            list-style: none;
            padding-left: 0;
            height: 400px;
            overflow-y: auto;
        }

        #chatMessages li {
            width: 100%;
            padding: 10px;
        }

        li.message.system span.who {
            color: #d43f3a;
        }

        li.message.user span.who {
            color: #337ab7;
        }

        li.message.mine span.who {
            font-weight: bold;
        }
    </style>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                @lang('app.welcome') <?= Auth::user()->username ?: Auth::user()->first_name ?>!
                <div class="pull-right">
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                        <li class="active">@lang('app.dashboard')</li>
                    </ol>
                </div>
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Chat</div>
                <div class="panel-body" id="chat">
                <div style="display:table; width: 100%; margin-bottom: 10px;"></div>
                <ul id="chatMessages">
                    <li v-for="message in messages" class="message" :class="message.class">
                        <span class="who" v-text="message.who">: </span>
                        <span v-text="message.msg"></span>
                    </li>
                </ul>
                <div class="form-group">
                    <textarea rows="4" type="text" class="form-control" placeholder="Say something..."
                           v-model="newMessage" @keyup.enter="sendMessage"></textarea>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- Latest Vue JS CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.16/vue.min.js"></script>
    <script>
        var vue = new Vue({
            el: '#chat',
            data: {
                messages: [],
                newMessage: "",
                userName: "{{ $userName }}",
                uri: "{{ $url }}",
                conn: false
            },
            ready: function () {
                this.conn = new WebSocket('wss://' + this.uri + ':8020');
                var me = this;
                this.conn.onclose = function (e) {
                    var reason = 'Unknown error';
                    switch (e.code) {
                        case 1000:
                            reason = 'Normal closure';
                            break;
                        case 1001:
                            reason = 'An endpoint is going away';
                            break;
                        case 1002:
                            reason = 'An endpoint is terminating the connection due to a protocol error.';
                            break;
                        case 1003:
                            reason = 'An endpoint is terminating the connection because it has received a ' +
                                    'type of data it cannot accept';
                            break;
                        case 1004:
                            reason = 'Reserved. The specific meaning might be defined in the future.';
                            break;
                        case 1005:
                            reason = 'No status code was actually present';
                            break;
                        case 1006:
                            reason = 'The connection was closed abnormally';
                            break;
                        case 1007:
                            reason = 'The endpoint is terminating the connection because a message was received ' +
                                    'that contained inconsistent data';
                            break;
                        case 1008:
                            reason = 'The endpoint is terminating the connection because it received a message ' +
                                    'that violates its policy';
                            break;
                        case 1009:
                            reason = 'The endpoint is terminating the connection because a data frame was received ' +
                                    'that is too large';
                            break;
                        case 1010:
                            reason = 'The client is terminating the connection because it expected the server to ' +
                                    'negotiate one or more extension, but the server didn\'t.';
                            break;
                        case 1011:
                            reason = 'The server is terminating the connection because it encountered an unexpected ' +
                                    'condition that prevented it from fulfilling the request.';
                            break;
                        case 1012:
                            reason = 'The server is terminating the connection because it is restarting';
                            break;
                        case 1013:
                            reason = 'The server is terminating the connection due to a temporary condition';
                            break;
                        case 1015:
                            reason = 'The connection was closed due to a failure to perform a TLS handshake';
                            break;
                    }
                    me.addSystemMessage("Connection closed: " + reason);
                };
                this.conn.onopen = function (event) {
                    me.addSystemMessage("Connection established!");
                    this.conn.send(this.userName + ":Hi! I'm now connected!");
                }.bind(this);
                this.conn.onmessage = function (event) {
                    me.addServerMessage(event.data);
                };
            },
            methods: {
                addSystemMessage: function (message) {
                    this.addMessage({
                        "msg": message,
                        "class": "system",
                        "who": "System"
                    });
                },
                addServerMessage: function (message) {
                    this.addMessage({
                        "msg": message.split(':')[1].trim(),
                        "class": "user",
                        "who": message.split(':')[0].trim()
                    });
                },
                addMeAmessage: function (message) {
                    this.addMessage({
                        "msg": message,
                        "class": "mine",
                        "who": "Me"
                    });
                },
                addMessage: function (message) {
                    this.messages.push(message);
                    // allow the DOM to get updated
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
                    var msgToSend = this.userName + ":" + this.newMessage;
                    this.conn.send(msgToSend);
                    this.addMeAmessage(this.newMessage);
                    this.newMessage = "";
                }
            }
        });
    </script>
@stop
