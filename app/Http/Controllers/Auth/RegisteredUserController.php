<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Organizations\RegisterOrganizationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreOrganizerRegistrationRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly RegisterOrganizationAction $registerOrganizationAction,
    ) {
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     */
    public function store(StoreOrganizerRegistrationRequest $request): RedirectResponse
    {
        $registration = $this->registerOrganizationAction->execute($request->validated());
        $user = $registration['user'];
        $organization = $registration['organization'];

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('organizer.dashboard', $organization);
    }
}
