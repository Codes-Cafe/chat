<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-container button:hover {
            background-color: #218838;
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: 10px;
        }

        .success {
            color: green;
            font-size: 14px;
            margin-top: 10px;
        }

        .login-container .register-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }

        .login-container .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>

        <!-- Display success message -->
        @if (session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif

        <!-- Display error message -->
        @if (session('error'))
            <div class="message error">{{ session('error') }}</div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('admin.checklogin') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
            <input type="password" name="password" placeholder="Password" required>

            <!-- Show validation errors for email -->
            @if ($errors->has('email'))
                <div class="error">{{ $errors->first('email') }}</div>
            @endif

            <button type="submit">Login</button>
        </form>

        <!-- Link to Admin Registration Page -->
        <a href="{{ route('admin.register') }}" class="register-link">Don't have an account? Register here</a>
    </div>
</body>

</html>
