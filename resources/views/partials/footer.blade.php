<!-- Begin Footer -->
<footer class="footer d-flex align-items-center text-center">
    <div class="container-fluid">
        @php
            $footerText = \App\Models\Setting::get(
                'footer_text',
                'Bhandari Packers & Movers. Your Trusted Moving Partner. <a href=https://bhandaripackersandmovers.in/ target="_blank">❤️Bhandari Packers</a>.',
            );
        @endphp
        <p class="mb-0">
            ©
            <script>
                document.write(new Date().getFullYear())
            </script>
            {!! $footerText !!}
        </p>
    </div>
</footer>
<!-- END Footer -->
