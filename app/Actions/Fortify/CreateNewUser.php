<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\CryptoAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        // Pasted or auto-filled usernames can carry spaces; referral links break on them.
        $input['username'] = preg_replace('/\s+/', '', (string) ($input['username'] ?? ''));

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'alpha_dash', 'max:50', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'password' => $this->passwordRules(),
            'ref_by' => ['nullable', 'string', 'exists:users,username'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ], [
            'username.alpha_dash' => 'Your username can only contain letters, numbers, dashes and underscores.',
            'username.unique' => 'That username is taken, please choose another one.',
            'email.unique' => 'An account with this email already exists. Please log in instead.',
            'ref_by.exists' => 'We could not find that Referral ID. Check it or leave the field empty.',
        ])->validate();

        $referrer = session('ref_by') ?: ($input['ref_by'] ?? null);
        $ref_by_id = $referrer ? optional(User::where('username', $referrer)->first())->id : null;

        // All or nothing: a half-created account would lock the person out with "email taken".
        $user = DB::transaction(function () use ($input, $ref_by_id) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'username' => $input['username'],
                'country' => $input['country'],
                'ref_by' => $ref_by_id,
                'status' => 'active',
                'password' => Hash::make($input['password']),
            ]);

            $cryptoaccnt = new CryptoAccount();
            $cryptoaccnt->user_id = $user->id;
            $cryptoaccnt->save();

            return $user;
        });

        session()->forget('ref_by');

        return $user;
    }
}
