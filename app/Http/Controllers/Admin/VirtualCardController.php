<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VirtualCard;
use Illuminate\Http\Request;

class VirtualCardController extends Controller
{
    private function rules()
    {
        return [
            'last4'  => ['required', 'digits:4'],
            'expiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
        ];
    }

    public function index()
    {
        return view('admin.virtualcards')->with([
            'title'  => 'Virtual cards',
            'cards'  => VirtualCard::with('user')->orderByRaw("status = 'pending' desc")->orderBy('id', 'desc')->get(),
            'users'  => User::orderBy('name')->get(['id', 'name', 'l_name', 'email']),
        ]);
    }

    // Admin issues (and approves) a card for any user. Overwrites a rejected/pending record for that user.
    public function issue(Request $request)
    {
        $data = $request->validate(array_merge($this->rules(), ['user_id' => ['required', 'exists:users,id']]));

        VirtualCard::updateOrCreate(
            ['user_id' => $data['user_id']],
            ['status' => 'approved', 'last4' => $data['last4'], 'expiry' => $data['expiry'], 'admin_note' => null, 'approved_at' => now()]
        );

        return back()->with('message', 'Card issued.');
    }

    public function approve(Request $request, $id)
    {
        $data = $request->validate($this->rules());
        $card = VirtualCard::findOrFail($id);
        $card->update(['status' => 'approved', 'last4' => $data['last4'], 'expiry' => $data['expiry'], 'approved_at' => now()]);

        return back()->with('message', 'Card approved.');
    }

    public function reject($id)
    {
        $card = VirtualCard::findOrFail($id);
        $card->update(['status' => 'rejected', 'last4' => null, 'expiry' => null, 'approved_at' => null]);

        return back()->with('message', 'Card rejected / revoked.');
    }
}
