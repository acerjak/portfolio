<x-mail::message>
# New inquiry

**From:** {{ $name }} ({{ $email }})

@if ($company)
**Company:** {{ $company }}
@endif

**Phone:** {{ $phone }}

**Reason:** {{ $reason->label() }}

**Message:**

{{ $body }}

<x-mail::button :url="'mailto:'.$email">
Reply to {{ $name }}
</x-mail::button>

Sent from the portfolio inquiry form.
</x-mail::message>
