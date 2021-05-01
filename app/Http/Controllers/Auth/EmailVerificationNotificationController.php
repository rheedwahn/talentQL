<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            if($request->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'email already verified'], 200);
            }
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $request->user()->sendEmailVerificationNotification();

        if($request->wantsJson()) {
            return response()->json(['status' => 'success', 'message' => 'An activation link has been sent to your email'], 200);
        }

        return back()->with('status', 'verification-link-sent');
    }
}
