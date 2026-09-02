<?php

namespace Tests\Feature;

use App\Enums\InquiryReason;
use App\Mail\InquiryReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
