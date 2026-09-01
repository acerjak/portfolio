<?php

use App\Enums\InquiryReason;
use App\Mail\InquiryReceived;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component {
    public string $name = '';

    public string $company = '';

    public string $phone = '';

    public string $email = '';

    public string $reason = 'general';

    public string $body = '';

    /** Honeypot — real visitors never see or fill this field. */
    public string $website = '';

    public int $renderedAt;

    public string $turnstileToken = '';

    public bool $sent = false;

    public function mount(): void
    {
        $this->renderedAt = now()->timestamp;
    }

    public function submit(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'reason' => ['required', Rule::enum(InquiryReason::class)],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        // Honeypot: bots tend to fill every field, humans never see this one.
        if (filled($this->website)) {
            $this->resetForm();
            $this->sent = true;

            return;
        }

        // Time-trap: reject submissions faster than a human could plausibly fill the form.
        if (now()->timestamp - $this->renderedAt < 3) {
            $this->addError('body', 'Please try submitting again.');

            return;
        }

        if ($siteSecret = config('services.turnstile.secret_key')) {
            $verified = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $siteSecret,
                'response' => $this->turnstileToken,
                'remoteip' => request()->ip(),
            ])->json('success', false);

            if (! $verified) {
                $this->addError('turnstileToken', 'Please complete the verification challenge.');

                return;
            }
        }

        Mail::to(config('inquiry.to_address'))->send(new InquiryReceived(
            name: $validated['name'],
            company: $validated['company'] ?: null,
            phone: $validated['phone'],
            email: $validated['email'],
            reason: InquiryReason::from($validated['reason']),
            body: $validated['body'],
        ));

        $this->resetForm();
        $this->sent = true;
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'company', 'phone', 'email', 'reason', 'body', 'website', 'turnstileToken']);
        $this->renderedAt = now()->timestamp;
    }
}; ?>

<div>
    @if ($sent)
        <div class="rounded-2xl border border-teal-100 bg-teal-50 p-6 text-center">
            <p class="font-serif text-xl font-medium text-ink">Thanks for reaching out!</p>
            <p class="mt-2 text-sm text-ink-soft">I'll get back to you as soon as I can.</p>
        </div>
    @else
        <form wire:submit="submit" class="space-y-5">
            <div style="position:absolute; left:-9999px;" aria-hidden="true">
                <label for="website">Website</label>
                <input type="text" id="website" wire:model="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-ink">Name</label>
                    <input
                        type="text"
                        id="name"
                        wire:model="name"
                        class="w-full rounded-xl border border-mustard-300 bg-paper px-4 py-2.5 text-sm text-ink placeholder:text-ink-soft/60 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 focus:outline-none"
                        placeholder="Jane Doe"
                    >
                    @error('name') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="company" class="mb-1.5 block text-sm font-medium text-ink">Company <span class="text-ink-soft">(optional)</span></label>
                    <input
                        type="text"
                        id="company"
                        wire:model="company"
                        class="w-full rounded-xl border border-mustard-300 bg-paper px-4 py-2.5 text-sm text-ink placeholder:text-ink-soft/60 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 focus:outline-none"
                        placeholder="Acme Inc."
                    >
                    @error('company') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="mb-1.5 block text-sm font-medium text-ink">Phone</label>
                    <input
                        type="tel"
                        id="phone"
                        wire:model="phone"
                        class="w-full rounded-xl border border-mustard-300 bg-paper px-4 py-2.5 text-sm text-ink placeholder:text-ink-soft/60 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 focus:outline-none"
                        placeholder="(555) 555-0100"
                    >
                    @error('phone') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-ink">Email</label>
                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        class="w-full rounded-xl border border-mustard-300 bg-paper px-4 py-2.5 text-sm text-ink placeholder:text-ink-soft/60 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 focus:outline-none"
                        placeholder="jane@acme.com"
                    >
                    @error('email') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="reason" class="mb-1.5 block text-sm font-medium text-ink">Reason for inquiry</label>
                <select
                    id="reason"
                    wire:model="reason"
                    class="w-full rounded-xl border border-mustard-300 bg-paper px-4 py-2.5 text-sm text-ink focus:border-teal-500 focus:ring-2 focus:ring-teal-100 focus:outline-none"
                >
                    @foreach (\App\Enums\InquiryReason::cases() as $case)
                        <option value="{{ $case->value }}">{{ $case->label() }}</option>
                    @endforeach
                </select>
                @error('reason') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="body" class="mb-1.5 block text-sm font-medium text-ink">Message</label>
                <textarea
                    id="body"
                    wire:model="body"
                    rows="5"
                    class="w-full rounded-xl border border-mustard-300 bg-paper px-4 py-2.5 text-sm text-ink placeholder:text-ink-soft/60 focus:border-teal-500 focus:ring-2 focus:ring-teal-100 focus:outline-none"
                    placeholder="What's on your mind?"
                ></textarea>
                @error('body') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
            </div>

            @if ($siteKey = config('services.turnstile.site_key'))
                <div wire:ignore x-data x-init="window.onTurnstileVerified = (token) => $wire.set('turnstileToken', token)">
                    <div class="cf-turnstile" data-sitekey="{{ $siteKey }}" data-callback="onTurnstileVerified"></div>
                </div>
                @error('turnstileToken') <p class="mt-1 text-xs text-pink-600">{{ $message }}</p> @enderror
                @once
                    @push('scripts')
                        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                    @endpush
                @endonce
            @endif

            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="submit"
                class="inline-flex items-center gap-2 rounded-full bg-ink px-6 py-2.5 text-sm font-medium text-paper transition hover:bg-pink-600 disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="submit">Send message</span>
                <span wire:loading wire:target="submit">Sending&hellip;</span>
            </button>
        </form>
    @endif
</div>
