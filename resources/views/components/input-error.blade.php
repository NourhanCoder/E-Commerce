@props(['messages'])
<style>
    .text-danger svg {
        color: #dc3545;
    }

    .text-danger span {
        font-family: 'Cairo', sans-serif; 
        font-size: 14px;
    }
</style>

@if ($messages)
    <div {{ $attributes->merge(['class' => 'text-danger text-sm mt-1']) }}>
        @foreach ((array) $messages as $message)
            <div class="d-flex align-items-center mb-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-12.75a.75.75 0 00-1.5 0v4.5a.75.75 0 001.5 0v-4.5zm0 6.75a.75.75 0 00-1.5 0v.5a.75.75 0 001.5 0v-.5z" clip-rule="evenodd" />
                </svg>
                <span>{{ $message }}</span>
            </div>
        @endforeach
    </div>
@endif