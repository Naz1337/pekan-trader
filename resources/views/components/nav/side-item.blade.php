@props([
    'active' => null,
    'url' => request()->fullUrl()
])

@php
if (is_null($active)) {
//    if (request()->fullUrl() == $url) {
    if (str_starts_with(request()->fullUrl(), $url)) {
        $active = true;
    }
}
@endphp

<a @class([
    'btn',
    'btn-ghost' => is_null($active),
    'btn-soft btn-primary' => $active,
    'h-[48px]',
    'justify-start',
    'ps-10'
]) href="{{ $url }}">{{ $slot }} </a>
{{--<div>{{ $url }}</div>--}}
{{--<div>{{ '/' . request()->fullUrl() }}</div>--}}

