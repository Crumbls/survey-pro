@php($code = config('services.google.analytics_id'))
@if($code)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $code }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '{{ $code }}', {
        user_id: {{ auth()->id() ?? 'null' }},
    });
</script>
@endif
