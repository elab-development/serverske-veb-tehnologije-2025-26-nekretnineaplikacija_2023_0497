<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        
        $newPassword = Str::random(10);

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return response()->json([
            'message'      => 'Lozinka je uspešno resetovana.',
            'new_password' => $newPassword,
        ]);
    }

    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email|exists:users,email',
            'current_password'      => 'required',
            'new_password'          => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Trenutna lozinka nije ispravna.',
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Lozinka je uspešno promenjena.',
        ]);
    }
}