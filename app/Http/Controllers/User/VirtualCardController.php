<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\VirtualCard;
use Illuminate\Support\Facades\Auth;

class VirtualCardController extends Controller
{
    // A logged-in user asks for a card. The user id always comes from the session, never the request.
    public function store()
    {
        $card = VirtualCard::where('user_id', Auth::id())->first();

        if ($card && $card->status !== 'rejected') {
            return redirect()->route('dashboard')->with('message', 'You already have a card request or an issued card.');
        }

        VirtualCard::updateOrCreate(
            ['user_id' => Auth::id()],
            ['status' => 'pending', 'last4' => null, 'expiry' => null, 'admin_note' => null, 'approved_at' => null]
        );

        return redirect()->route('dashboard')->with('success', 'Card request submitted. You will see your card here once it is approved.');
    }
}
