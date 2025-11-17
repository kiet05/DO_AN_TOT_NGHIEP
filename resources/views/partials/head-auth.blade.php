<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'EGA') }}</title>

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #a8e6cf 0%, #7ed3b2 100%);
        min-height: 100vh;
    }
    .auth-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }
    .auth-logo {
        width: 80px;
        height: 80px;
        background: transparent;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        padding: 5px;
    }
    .auth-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .auth-title {
        color: #2d3748;
        font-weight: 700;
        font-size: 28px;
        margin-bottom: 8px;
    }
    .auth-subtitle {
        color: #718096;
        font-size: 14px;
        margin-bottom: 30px;
    }
    .form-label {
        color: #2d3748;
        font-weight: 500;
        font-size: 14px;
        margin-bottom: 8px;
    }
    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 14px;
        transition: all 0.3s;
    }
    .form-control:focus {
        border-color: #4ecdc4;
        box-shadow: 0 0 0 0.2rem rgba(78, 205, 196, 0.25);
    }
    .btn-primary {
        background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(78, 205, 196, 0.4);
        background: linear-gradient(135deg, #44a08d 0%, #4ecdc4 100%);
    }
    .auth-link {
        color: #4ecdc4;
        text-decoration: none;
        font-weight: 500;
    }
    .auth-link:hover {
        color: #44a08d;
        text-decoration: underline;
    }
    .input-group-text {
        background: #f7fafc;
        border: 2px solid #e2e8f0;
        border-right: none;
        border-radius: 10px 0 0 10px;
    }
    .input-group .form-control {
        border-left: none;
        border-radius: 0 10px 10px 0;
    }
    .input-group:focus-within .input-group-text {
        border-color: #4ecdc4;
    }
</style>

<!-- Livewire Styles -->
@livewireStyles

