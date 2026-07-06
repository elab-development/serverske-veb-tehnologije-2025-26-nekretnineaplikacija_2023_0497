<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserPropertyController extends Controller
{
    public function index(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        
        if (!$request->user()->isAdmin() && $request->user()->id != $id) {
            return response()->json(['message' => 'Nemate dozvolu.'], 403);
        }

        $properties = $user->properties()->paginate(10);

        return response()->json($properties);
    }
}