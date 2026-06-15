<?php

namespace Tests\Feature\Phase1;

use App\Enums\OtpChannel;
use App\Enums\OtpPurpose;
use App\Models\User;
use App\Services\Otp\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OtpServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_creates_hashed_otp_request_with_normalized_phone_number(): void
    {
        $user = User::factory()->create();

        request()->server->set('REMOTE_ADDR', '127.0.0.55');
        request()->headers->set('User-Agent', 'OtpServiceTestAgent/1.0');

        $issuedOtp = app(OtpService::class)->issue(
            phoneNumber: '+233 20 000 2222',
            purpose: OtpPurpose::LOGIN,
            channel: OtpChannel::SMS,
            user: $user,
            meta: ['source' => 'phase1-test'],
            ttlMinutes: 5,
        );

        $otpRequest = $issuedOtp['otp_request'];

        $this->assertSame('+233200002222', $otpRequest->phone_number);
        $this->assertNotSame($issuedOtp['code'], $otpRequest->code_hash);
        $this->assertTrue(Hash::check($issuedOtp['code'], $otpRequest->code_hash));
        $this->assertSame('127.0.0.55', $otpRequest->ip_address);
        $this->assertSame('OtpServiceTestAgent/1.0', $otpRequest->user_agent);
        $this->assertSame('phase1-test', $otpRequest->meta['source']);
    }

    public function test_issue_invalidates_previous_pending_otp_for_same_phone_and_purpose(): void
    {
        $service = app(OtpService::class);

        $firstIssuedOtp = $service->issue(
            phoneNumber: '+233200002300',
            purpose: OtpPurpose::LOGIN,
        );

        $secondIssuedOtp = $service->issue(
            phoneNumber: '+233200002300',
            purpose: OtpPurpose::LOGIN,
        );

        $this->assertNotNull($firstIssuedOtp['otp_request']->fresh()->consumed_at);
        $this->assertNull($secondIssuedOtp['otp_request']->fresh()->consumed_at);
    }

    public function test_verify_consumes_matching_active_otp_request(): void
    {
        $issuedOtp = app(OtpService::class)->issue(
            phoneNumber: '+233200002400',
            purpose: OtpPurpose::LOGIN,
        );

        $verifiedOtpRequest = app(OtpService::class)->verify(
            phoneNumber: '+233200002400',
            purpose: OtpPurpose::LOGIN,
            code: $issuedOtp['code'],
        );

        $this->assertNotNull($verifiedOtpRequest);
        $this->assertNotNull($verifiedOtpRequest->consumed_at);
        $this->assertSame($issuedOtp['otp_request']->id, $verifiedOtpRequest->id);
    }

    public function test_verify_rejects_wrong_code_and_tracks_attempts(): void
    {
        $issuedOtp = app(OtpService::class)->issue(
            phoneNumber: '+233200002500',
            purpose: OtpPurpose::LOGIN,
        );

        $verifiedOtpRequest = app(OtpService::class)->verify(
            phoneNumber: '+233200002500',
            purpose: OtpPurpose::LOGIN,
            code: '000000',
        );

        $this->assertNull($verifiedOtpRequest);
        $this->assertSame(1, $issuedOtp['otp_request']->fresh()->attempts);
        $this->assertNull($issuedOtp['otp_request']->fresh()->consumed_at);
    }

    public function test_verify_rejects_expired_otp_request(): void
    {
        $issuedOtp = app(OtpService::class)->issue(
            phoneNumber: '+233200002600',
            purpose: OtpPurpose::LOGIN,
            ttlMinutes: 1,
        );

        $this->travel(2)->minutes();

        $verifiedOtpRequest = app(OtpService::class)->verify(
            phoneNumber: '+233200002600',
            purpose: OtpPurpose::LOGIN,
            code: $issuedOtp['code'],
        );

        $this->assertNull($verifiedOtpRequest);
        $this->assertNull($issuedOtp['otp_request']->fresh()->consumed_at);
    }
}
