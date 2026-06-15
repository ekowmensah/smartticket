<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditLogger
{
    private const MASKED_VALUE = '[REDACTED]';

    public function __construct(
        private readonly Request $request,
    ) {
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function log(
        ?Model $actor,
        string $description,
        string $event,
        ?Model $subject = null,
        ?int $organizationId = null,
        array $properties = [],
    ): void {
        $logger = activity('audit')
            ->event($event)
            ->withProperties($this->sanitizeProperties($properties));

        if ($actor !== null) {
            $logger->causedBy($actor);
        }

        if ($subject !== null) {
            $logger->performedOn($subject);
        }

        $activity = $logger->log($description);

        $activity->forceFill([
            'organization_id' => $organizationId,
            'request_id' => $this->request->headers->get('X-Request-ID') ?: (string) Str::uuid(),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ])->saveQuietly();
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    private function sanitizeProperties(array $properties): array
    {
        $sanitized = [];

        foreach ($properties as $key => $value) {
            $sanitized[$key] = $this->shouldMask((string) $key)
                ? self::MASKED_VALUE
                : $this->sanitizeValue($value);
        }

        return $sanitized;
    }

    private function sanitizeValue(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        return collect($value)
            ->mapWithKeys(function (mixed $nestedValue, int|string $nestedKey): array {
                $key = (string) $nestedKey;

                return [
                    $nestedKey => $this->shouldMask($key)
                        ? self::MASKED_VALUE
                        : $this->sanitizeValue($nestedValue),
                ];
            })
            ->all();
    }

    private function shouldMask(string $key): bool
    {
        $normalizedKey = (string) Str::of($key)
            ->replace(['-', ' '], '_')
            ->lower();

        if (in_array($normalizedKey, [
            'password',
            'password_confirmation',
            'current_password',
            'remember_token',
            'code_hash',
            'token_hash',
        ], true)) {
            return true;
        }

        return Str::contains($normalizedKey, [
            'password_',
            '_password',
            'token_',
            '_token',
            'secret',
        ]);
    }
}
