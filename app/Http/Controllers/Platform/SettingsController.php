<?php

namespace App\Http\Controllers\Platform;

use App\Actions\Platform\UpdatePlatformSettingsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\UpdatePlatformSettingsRequest;
use App\Models\Setting;
use App\Support\PlatformSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingsController extends Controller
{
    public function edit(PlatformSettings $platformSettings): View
    {
        $this->authorize('viewPlatformSettings', Setting::class);

        return view('platform.settings.edit', [
            'settings' => $platformSettings->all(),
        ]);
    }

    public function update(
        UpdatePlatformSettingsRequest $request,
        UpdatePlatformSettingsAction $action,
    ): RedirectResponse {
        $this->authorize('updatePlatformSettings', Setting::class);

        $action->execute($request->user(), $request->validated());

        return redirect()
            ->route('platform.settings.edit')
            ->with('status', 'Platform settings updated.');
    }
}
