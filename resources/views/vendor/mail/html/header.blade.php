@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if (trim($slot) !== '')
<span style="font-size: 20px; font-weight: 600; color: #18181b;">{!! $slot !!}</span>
@else
<span style="font-size: 20px; font-weight: 600; color: #18181b;">{{ config('app.name') }}</span>
@endif
</a>
</td>
</tr>
