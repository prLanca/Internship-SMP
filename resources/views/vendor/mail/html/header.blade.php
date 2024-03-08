@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Motherson Portal')
<img src="https://companieslogo.com/img/orig/MOTHERSON.NS-67605199.png?t=1604067040" class="logo" alt="Motherson">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
