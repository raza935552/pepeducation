@php
    $css = \App\Services\ThemeService::generateCssVariables();
@endphp
<style>{!! $css !!}</style>
