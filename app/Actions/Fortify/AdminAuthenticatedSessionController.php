<?php

namespace App\Actions\Fortify;

use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Pipeline;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;

class AdminAuthenticatedSessionController
{
    protected $guard;

    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    public function create(Request $request)
    {
        return view('admin.auth.login');
    }

    public function store(LoginRequest $request)
    {
        return $this->loginPipeline($request)->then(function ($request) {
            return redirect(RouteServiceProvider::ADMIN_HOME);
        });
    }

    protected function loginPipeline(LoginRequest $request)
    {
        if (Fortify::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                call_user_func(Fortify::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(config('fortify.pipelines.login'))) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                config('fortify.pipelines.login')
            ));
        }

        return (new Pipeline(app()))->send($request)->through(array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AdminAttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }

    public function destroy(Request $request)
    {
        $this->guard->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
