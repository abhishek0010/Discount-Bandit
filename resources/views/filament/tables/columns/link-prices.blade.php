@php
    use Illuminate\Support\Number;
    use App\Helpers\LinkHelper;

    $user     = auth()->user();
    $userRate = $user->currency_id && $user->currency?->rate ? $user->currency->rate : 1;
    $userCode =  $user->currency_id && $user->currency?->rate ?$user->currency->code : null;
@endphp

<div class="flex flex-col w-full space-y-4">

    <div class="grid grid-cols-3 md:grid-cols-4 w-full gap-x-4   dark:border-white/10 pb-1 text-xs font-semibold">
        <div class="hidden md:block!">Store</div>
        <div>Highest</div>
        <div>Current</div>
        <div>Best</div>
    </div>

    {{-- One row per link --}}
    @foreach ($getState() as $link)
        @php
            $storeRate = $link->store->currency->rate ?: 1;
            $factor  = $userRate / $storeRate;

            $code      = $userCode ?? $link->store->currency->code;
            $highest   = $link->highest_price * $factor;
            $current   = $link->price * $factor;
            $lowest    = $link->lowest_price * $factor;

        @endphp

        <div class="grid grid-cols-3 md:grid-cols-4 gap-4 text-xs hover:opacity-80 ">
            <div class="col-span-full md:col-span-1">
                <a href="{{ LinkHelper::get_url($link) }}"
                   target="_blank"
                   class="underline text-primary-400 hover:text-primary-300">
                    {{ $link->store->name }} ({{$code}})
                </a>
            </div>
            <div class="text-danger-500">{{ Number::format($highest) }}</div>
            <div>{{ Number::format($current) }}</div>
            <div class="text-success-500">{{ Number::format($lowest) }}</div>
        </div>
    @endforeach
</div>
