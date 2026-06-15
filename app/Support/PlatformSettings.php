<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class PlatformSettings
{
    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $defaults = $this->defaults();

        if (! Schema::hasTable('settings')) {
            return $defaults;
        }

        $storedSettings = Setting::query()
            ->where('scope', 'platform')
            ->where('scope_id', 0)
            ->get()
            ->mapWithKeys(fn (Setting $setting): array => [$setting->key => $setting->value])
            ->all();

        return array_merge($defaults, $storedSettings);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function update(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(
                [
                    'scope' => 'platform',
                    'scope_id' => 0,
                    'key' => $key,
                ],
                [
                    'value' => $value,
                ],
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'product_name' => 'SmartCast Tickets',
            'support_email' => 'support@smartcast.test',
            'support_phone' => '+233200000999',
            'currency_code' => 'GHS',
            'timezone' => 'Africa/Accra',
            'date_format' => 'd M Y',
            'contact_address' => 'Accra, Ghana',
        ];
    }
}
