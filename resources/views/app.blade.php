<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Fonts -->
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description"
        content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <!-- Title -->
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    <!-- StyleSheets  -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    @routes
    @vite('resources/js/app.js')
    @inertiaHead
</head>
<body class="nk-body npc-default has-apps-sidebar has-sidebar">
    @inertia
    <script src="{{ asset('assets/js/bundle.js?ver=2.2.0') }}"></script>
    <script src="{{ asset('assets/js/scripts.js?ver=2.2.0') }}"></script>
    <script src="{{ asset('assets/js/charts/chart-ecommerce.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="https://momentjs.com/downloads/moment.js"></script>
      <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    {{-- &callback=initAutocomplete --}}
</body>
</html>