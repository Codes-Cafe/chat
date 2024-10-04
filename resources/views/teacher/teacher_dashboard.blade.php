<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Teacher Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
</head>

<body>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <div class="container">
        <div class="row clearfix">
            <div class="col-lg-12">
                <div class="card chat-app">
                    <div id="plist" class="people-list">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Search...">
                        </div>
                        <ul class="list-unstyled chat-list mt-2 mb-0" id="user-list">
                            <!-- Dynamic user list will be added here -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script>
            $(document).ready(function() {
                function fetchTeacherChatList() {
                    $.ajax({
                        type: 'GET',
                        url: '/teacher/fetch-teacher-chat-list',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            var users = data.users;
                            var chatListHtml = '';

                            // Check if there are users in the response
                            if (users.length > 0) {
                                // Loop through each user and append to the chat list
                                $.each(users, function(index, user) {
                                    var chatUrl = '/teacher/chats/' + user.id;
                                    chatListHtml += '<li class="clearfix">';
                                    chatListHtml +=
                                        '<img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="avatar">';
                                    chatListHtml += '<div class="about">';
                                    chatListHtml += '<div class="name"><a href="' + chatUrl + '">' +
                                        user.name + '</a></div>';
                                    chatListHtml +=
                                        '<div class="status"> <i class="fa fa-circle offline"></i> User</div>';
                                    chatListHtml += '</div>';
                                    chatListHtml += '</li>';
                                });
                            } else {
                                chatListHtml = '<li>No users found</li>';
                            }

                            // Replace the content of the user list with the generated HTML
                            $('#user-list').html(chatListHtml);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }

                // Call the function to fetch the chat list when the page loads
                fetchTeacherChatList();
            });
        </script>
    </div>
</body>

</html>
