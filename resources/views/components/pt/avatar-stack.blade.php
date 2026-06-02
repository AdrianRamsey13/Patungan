@props(['members', 'max' => 4, 'size' => 'sm', 'ring' => '#fff'])

@php
    // $members: koleksi User models atau array ['name' => ...]
    $shown = collect($members)->take($max);
    $extra = collect($members)->count() - $shown->count();

    $sizePx = match($size) { 'xs' => 24, 'sm' => 28, 'md' => 36, default => 28 };
    $offset = round($sizePx * 0.34);
@endphp

<div class="flex items-center">
    @foreach ($shown as $i => $member)
        <div style="margin-left:{{ $i === 0 ? 0 : -$offset }}px;z-index:{{ $shown->count() - $i }}">
            <x-pt.avatar :name="is_object($member) ? $member->name : $member['name']" :size="$size" :ring="$ring" />
        </div>
    @endforeach

    @if ($extra > 0)
        <div style="margin-left:-{{ $offset }}px;width:{{ $sizePx }}px;height:{{ $sizePx }}px;border-radius:50%;background:#fff;box-shadow:0 0 0 2px {{ $ring }};font-size:{{ round($sizePx * 0.34) }}px;z-index:0"
             class="flex items-center justify-center font-extrabold text-ink-soft">
            +{{ $extra }}
        </div>
    @endif
</div>
