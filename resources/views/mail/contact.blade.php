<x-mail::message>
# New contact form submission

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

Sent from the portfolio contact form.
</x-mail::message>
