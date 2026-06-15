<?php

namespace App\Services\Otp;

use App\Enums\OtpChannel;
use App\Enums\OtpPurpose;
use App\Models\OtpRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    public function __construct(
        private readonly Request $request,
    ) {
    }

    /**
     * @param  array<string, mixed>  $meta
     * @return array{otp_request: OtpRequest, code: string}
     */
    public function issue(
        string $phoneNumber,
        OtpPurpose $purpose,
        OtpChannel $channel = OtpChannel::SMS,
        ?User $user = null,
        array $meta = [],
        int $ttlMinutes = 10,
    ): array {
        return DB::transaction(function () use ($phoneNumber, $purpose, $channel, $user, $meta, $ttlMinutes): array {
            $normalizedPhoneNumber = $this->normalizePhoneNumber($phoneNumber);

            OtpRequest::query()
                ->where('phone_number', $normalizedPhoneNumber)
                ->where('purpose', $purpose->value)
                ->whereNull('consumed_at')
                ->update([
                    'consumed_at' => now(),
                ]);

            $code = (string) random_int(100000, 999999);

            $otpRequest = OtpRequest::query()->create([
                'user_id' => $user?->id,
                'phone_number' => $normalizedPhoneNumber,
                'channel' => $channel,
                'purpose' => $purpose,
                'code_hash' => Hash::make($code),
                'expires_at' => now()->addMinutes($ttlMinutes),
                'attempts' => 0,
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
                'meta' => $meta,
            ]);

            return [
                'otp_request' => $otpRequest,
                'code' => $code,
            ];
        });
    }

    public function verify(string $phoneNumber, OtpPurpose $purpose, string $code): ?OtpRequest
    {
        $otpRequest = OtpRequest::query()
            ->where('phone_number', $this->normalizePhoneNumber($phoneNumber))
            ->where('purpose', $purpose->value)
            ->whereNull('consumed_at')
            ->latest('id')
            ->first();

        if ($otpRequest === null || $otpRequest->expires_at->isPast()) {
            return null;
        }

        if (! Hash::check($code, $otpRequest->code_hash)) {
            $otpRequest->increment('attempts');

            return null;
        }

        $otpRequest->forceFill([
            'consumed_at' => now(),
        ])->save();

        return $otpRequest->fresh();
    }

    private function normalizePhoneNumber(string $phoneNumber): string
    {
        $normalizedPhoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber) ?? '';

        if ($normalizedPhoneNumber === '') {
            return $normalizedPhoneNumber;
        }

        if (str_starts_with($normalizedPhoneNumber, '+')) {
            return '+'.ltrim(substr($normalizedPhoneNumber, 1), '+');
        }

        return $normalizedPhoneNumber;
    }
}
