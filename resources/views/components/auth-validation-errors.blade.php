@props(['errors'])

@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700']) }}>
        <div class="font-semibold">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-2 list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
