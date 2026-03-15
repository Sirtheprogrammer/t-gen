<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hub System</title>
    <!-- Add your CSS rules here. Let's use vanilla CSS as requested. -->
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fdfdfd; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .auth-card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { color: #333; margin-bottom: 0.5rem; text-align: center; }
        p.subtitle { color: #666; text-align: center; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; color: #555; font-weight: 500; text-transform: uppercase; font-size: 0.85rem;}
        input { width: 100%; padding: 0.75rem 1rem; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; transition: border-color 0.3s; }
        input:focus { outline: none; border-color: #007bff; box-shadow: 0 0 0 3px rgba(0,123,255,0.1); }
        button { width: 100%; background: #007bff; color: white; border: none; padding: 0.8rem; border-radius: 6px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: background 0.3s, transform 0.2s; }
        button:hover { background: #0056b3; transform: translateY(-1px); }
        .error { color: #e3342f; font-size: 0.875rem; margin-top: 0.25rem; }
        .login-link { display: block; text-align: center; margin-top: 1.5rem; color: #007bff; text-decoration: none; font-size: 0.9rem; }
        .login-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h1>Create Account</h1>
        <p class="subtitle">Register to start managing your pages.</p>
        
        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                @error('password')<div class="error">{{ $message }}</div>@enderror
            </div>
            
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit">Register</button>
        </form>
        <a href="{{ route('login') }}" class="login-link">Already have an account? Login here.</a>
    </div>
</body>
</html>
