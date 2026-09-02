<?php

namespace Tests\Feature;

use App\Enums\InquiryReason;
use App\Mail\InquiryReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class InquiryFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_submission_sends_mail_and_shows_success(): void
    {
        Mail::fake();

        Livewire::test('pages::inquiry-form')
            ->set('name', 'Jane Doe')
            ->set('phone', '555-0100')
            ->set('email', 'jane@example.com')
            ->set('reason', InquiryReason::General->value)
            ->set('body', 'Hello, I would like to talk.')
            ->set('renderedAt', now()->subSeconds(5)->timestamp)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        Mail::assertSent(InquiryReceived::class);
    }

    public function test_validation_errors_are_shown_for_missing_required_fields(): void
    {
        Mail::fake();

        Livewire::test('pages::inquiry-form')
            ->set('renderedAt', now()->subSeconds(5)->timestamp)
            ->call('submit')
            ->assertHasErrors(['name', 'phone', 'email', 'body']);

        Mail::assertNotSent(InquiryReceived::class);
    }

    public function test_an_invalid_email_is_rejected(): void
    {
        Mail::fake();

        Livewire::test('pages::inquiry-form')
            ->set('name', 'Jane Doe')
            ->set('phone', '555-0100')
            ->set('email', 'not-an-email')
            ->set('reason', InquiryReason::General->value)
            ->set('body', 'Hello, I would like to talk.')
            ->set('renderedAt', now()->subSeconds(5)->timestamp)
            ->call('submit')
            ->assertHasErrors(['email']);

        Mail::assertNotSent(InquiryReceived::class);
    }

    public function test_an_over_long_body_is_rejected(): void
    {
        Mail::fake();

        Livewire::test('pages::inquiry-form')
            ->set('name', 'Jane Doe')
            ->set('phone', '555-0100')
            ->set('email', 'jane@example.com')
            ->set('reason', InquiryReason::General->value)
            ->set('body', str_repeat('a', 5001))
            ->set('renderedAt', now()->subSeconds(5)->timestamp)
            ->call('submit')
            ->assertHasErrors(['body']);

        Mail::assertNotSent(InquiryReceived::class);
    }

    public function test_honeypot_fill_silently_fakes_success_without_sending_mail(): void
    {
        Mail::fake();

        Livewire::test('pages::inquiry-form')
            ->set('name', 'Bot')
            ->set('phone', '555-0100')
            ->set('email', 'bot@example.com')
            ->set('reason', InquiryReason::General->value)
            ->set('body', 'spam')
            ->set('website', 'https://spam.example')
            ->set('renderedAt', now()->subSeconds(5)->timestamp)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        Mail::assertNotSent(InquiryReceived::class);
    }

    public function test_time_trap_rejects_submissions_faster_than_a_human(): void
    {
        Mail::fake();

        Livewire::test('pages::inquiry-form')
            ->set('name', 'Fast Bot')
            ->set('phone', '555-0100')
            ->set('email', 'fast@example.com')
            ->set('reason', InquiryReason::General->value)
            ->set('body', 'too fast')
            ->call('submit')
            ->assertHasErrors(['body']);

        Mail::assertNotSent(InquiryReceived::class);
    }

    public function test_a_failed_turnstile_challenge_rejects_the_submission(): void
    {
        Mail::fake();
        config(['services.turnstile.secret_key' => 'test-secret']);
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);

        Livewire::test('pages::inquiry-form')
            ->set('name', 'Jane Doe')
            ->set('phone', '555-0100')
            ->set('email', 'jane@example.com')
            ->set('reason', InquiryReason::General->value)
            ->set('body', 'Hello, I would like to talk.')
            ->set('turnstileToken', 'placeholder-token')
            ->set('renderedAt', now()->subSeconds(5)->timestamp)
            ->call('submit')
            ->assertHasErrors(['turnstileToken']);

        Mail::assertNotSent(InquiryReceived::class);
    }

    public function test_a_passing_turnstile_challenge_allows_the_submission(): void
    {
        Mail::fake();
        config(['services.turnstile.secret_key' => 'test-secret']);
        Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

        Livewire::test('pages::inquiry-form')
            ->set('name', 'Jane Doe')
            ->set('phone', '555-0100')
            ->set('email', 'jane@example.com')
            ->set('reason', InquiryReason::General->value)
            ->set('body', 'Hello, I would like to talk.')
            ->set('turnstileToken', 'placeholder-token')
            ->set('renderedAt', now()->subSeconds(5)->timestamp)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        Http::assertSentCount(1);
        Mail::assertSent(InquiryReceived::class);
    }
}
