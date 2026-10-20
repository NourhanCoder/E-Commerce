@php
    $types = [
        'success' => 'success',
        'error' => 'danger',
        'warning' => 'warning',
    ];
@endphp

<div style="position: fixed; top: 140px; left: 50%; transform: translateX(-50%); z-index: 1080; width: 90%; max-width: 420px;">

    @foreach ($types as $key => $alertType)
        @if (session($key))
            <div class="alert alert-{{ $alertType }} alert-dismissible fade show shadow" role="alert" data-auto-dismiss>
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach

    @error('quantity')
        <div class="alert alert-danger alert-dismissible fade show shadow" role="alert" data-auto-dismiss>
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @enderror

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-auto-dismiss]').forEach(function (el) {
            setTimeout(function () {
                bootstrap.Alert.getOrCreateInstance(el).close();
            }, 5000);
        });
    });
</script>