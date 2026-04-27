@props(['url'])
<tr>
<td class="header">
@php
    $siteName = \App\Models\Setting::get('site_name', 'Digirepo');
@endphp
<a href="{{ $url }}" style="display: inline-block; color: #3d4852; font-size: 19px; font-weight: bold; text-decoration: none;">
    {{ $siteName }}
</a>
</td>
</tr>
