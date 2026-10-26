<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    app()->setLocale('bn');
@endphp
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ get_setting('app_name', config('app.name')) }} | @lang('kazitds::kazitds.Invoice not found')</title>
    <link rel="icon" href="{{ get_image('app_ico') ?? asset('assets/img/favicon/Encodex.ico') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        html, body { height: 100%; margin: 0; background: #f1f5f9; }
        .not-found { max-width: 420px; margin: auto; position: relative; top: 50%; transform: translateY(-50%); }
        .not-found-icon { font-size: 4.5rem; color: #1d4ed8; }
    </style>
</head>
<body>
    <div class="not-found text-center p-4">
        <i class="fas fa-file-circle-xmark not-found-icon mb-3"></i>
        <h1 class="h3 fw-bold">@lang('kazitds::kazitds.Invoice not found')</h1>
        <p class="text-muted mb-0">@lang('kazitds::kazitds.This invoice link is invalid or no longer available.')</p>
    </div>
</body>
</html>
