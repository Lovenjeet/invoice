<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) - {{ config('app.name', 'Laravel') }}</title>
    
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin-layout">
    <div class="wrapper">
        @include('layouts.partials.sidebar')
        
        <div class="main-content">
            @include('layouts.partials.navbar')
            
            <main class="content-wrapper">
                @if(session('success'))
                    <x-ui.alert type="success" :message="session('success')" />
                @endif
                
                @if(session('error'))
                    <x-ui.alert type="danger" :message="session('error')" />
                @endif
                
                @if($errors->any())
                    <x-ui.alert type="danger" :message="'Please fix the errors below.'" />
                @endif
                
                @yield('content')
            </main>
            
            @include('layouts.partials.footer')
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>

