<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
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

        .register-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .register-container input[type="text"],
        .register-container input[type="email"],
        .register-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .register-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .register-container button:hover {
            background-color: #0056b3;
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

        .register-container .login-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }

        .register-container .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h2>Register</h2>

        <!-- Display success message -->
        @if (session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif

        <!-- Display error message -->
        @if (session('error'))
            <div class="message error">{{ session('error') }}</div>
        @endif

        <!-- Registration Form -->
        <form action="{{ route('user.checkregister') }}" method="POST">
            @csrf
            <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" required>
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            <input type="password" name="password" placeholder="Password" required>

            <!-- Validation Errors -->
            @if ($errors->has('name'))
                <div class="error">{{ $errors->first('name') }}</div>
            @endif

            @if ($errors->has('email'))
                <div class="error">{{ $errors->first('email') }}</div>
            @endif

            @if ($errors->has('password'))
                <div class="error">{{ $errors->first('password') }}</div>
            @endif

            <button type="submit">Register</button>
        </form>

        <!-- Link to Login Page -->
        <a href="{{ route('user.login') }}" class="login-link">Already have an account? Login here</a>
    </div>
</body>

</html>
