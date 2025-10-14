<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Pepsiman')</title>
  @vite(['resources/css/navbar.css','resources/js/navbar.js'])
  @stack('styles')
</head>
<body>
  @include('partials.navbar')

  <main class="container">
    @yield('content')
  </main>

  @stack('scripts')
</body>
</html>

