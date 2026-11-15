@php
    $types = [
        'success' => 'success',
        'warning' => 'warning',
        'error' => 'danger',
    ];
@endphp

<div style="position: fixed; top: 70px; right: 20px; z-index: 2000; width: 90%; max-width: 420px;">
    @foreach ($types as $key => $alertType)
        @if (session($key))
            <div class="alert alert-{{ $alertType }}" data-auto-dismiss>
                <button type="button" class="close" onclick="this.parentElement.remove()">&times;</button>
                {{ session($key) }}
            </div>
        @endif
    @endforeach

    @error('error')
        <div class="alert alert-danger" data-auto-dismiss>
            <button type="button" class="close" onclick="this.parentElement.remove()">&times;</button>
            {{ $message }}
        </div>
    @enderror
</div>

<script>
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (el) {
        setTimeout(function () { el.remove(); }, 5000);
    });
</script>