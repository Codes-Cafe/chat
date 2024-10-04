<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>User Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <style>
        chat-app {
            margin-top: 30px;
            height: 500px;
            display: flex;
            flex-direction: column;
        }

        .chat-history {
            overflow-y: auto;
            height: 400px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f8f8f8;
        }

        .chat-history ul {
            list-style: none;
            padding: 0;
        }

        .chat-history ul li {
            margin-bottom: 15px;
            clear: both;
        }

        /* Sender's message aligned to the right */
        .my-message {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            display: inline-block;
            max-width: 70%;
            float: right;
            clear: both;
        }

        /* Receiver's message aligned to the left */
        .other-message {
            background-color: #ddd;
            color: #333;
            padding: 10px 15px;
            border-radius: 20px;
            display: inline-block;
            max-width: 70%;
            float: left;
            clear: both;
        }

        .message-data {
            display: block;
            margin-bottom: 5px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>

<body>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <div class="container">
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="chat">
                    <div class="chat-header clearfix">
                        <div class="row">
                            <div class="col-lg-6">
                                <a href="javascript:void(0);" data-toggle="modal" data-target="#view_info">
                                    <img src="https://bootdey.com/img/Content/avatar/avatar2.png" alt="avatar">
                                </a>
                                <div class="chat-about">
                                    <h6 class="m-b-0">{{ $teacher->name }}</h6>
                                    <small>Teacher</small>
                                </div>
                            </div>
                            <div class="col-lg-6 hidden-sm text-right">
                                <a href="javascript:void(0);" class="btn btn-outline-secondary"><i
                                        class="fa fa-camera"></i></a>
                                <a href="javascript:void(0);" class="btn btn-outline-primary"><i
                                        class="fa fa-image"></i></a>
                                <a href="javascript:void(0);" class="btn btn-outline-info"><i
                                        class="fa fa-cogs"></i></a>
                                <a href="javascript:void(0);" class="btn btn-outline-warning"><i
                                        class="fa fa-question"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="chat-history">
                        <ul>
                            <!-- Messages will be appended here -->
                        </ul>
                    </div>

                    <div class="chat-message clearfix">
                        <div class="input-group mb-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-send"></i></span>
                            </div>
                            <input type="text" id="messageInput" class="form-control"
                                placeholder="Enter text here..." required>
                            <div class="input-group-append">
                                <button id="sendMessageButton" class="btn btn-primary" type="button">Send</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                var teacherId = {{ $teacher->id }};
                var loggedInUserId = {{ session('LoggedUserInfo') }};

                function fetchMessages() {
                    $.ajax({
                        type: 'POST',
                        url: '/user/fetch-teacher-messages',
                        data: {
                            receiver_id: teacherId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            var messages = data.messages;
                            $('.chat-history ul').empty();
                            $.each(messages, function(index, message) {
                                var messageHtml = '<li class="clearfix">';
                                messageHtml += '<div class="message-data">';
                                messageHtml += '<span class="message-data-time">' + message
                                    .created_at + '</span>';
                                messageHtml += '</div>';
                                messageHtml += '<div class="message ' + (message.sender_id ==
                                        loggedInUserId ? 'my-message' : 'other-message') + '">' +
                                    message.message + '</div>';
                                messageHtml += '</li>';
                                $('.chat-history ul').append(messageHtml);
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }


                fetchMessages();
                setInterval(() => {
                    fetchMessages();
                }, 10000);

                $('#sendMessageButton').on('click', function() {
                    var message = $('#messageInput').val();

                    if (message.trim() === '') {
                        alert('Please enter a message.');
                        return;
                    }

                    $.ajax({
                        type: 'POST',
                        url: '/user/send-message-to-teacher',
                        data: {
                            message: message,
                            receiver_id: teacherId, // Assuming the teacher's ID is the receiver ID
                            _token: '{{ csrf_token() }}' // Add CSRF token
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#messageInput').val(''); // Clear input after sending
                                fetchMessages(); // Fetch updated messages
                            } else {
                                alert('Message sending failed.'); // Handle failure
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                });
            });
        </script>

</body>

</html>
