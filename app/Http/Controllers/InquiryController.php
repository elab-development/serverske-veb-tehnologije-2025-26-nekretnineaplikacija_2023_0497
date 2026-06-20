<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Property;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $inquiries = Inquiry::with(['user', 'property'])->get();
        } else {
            $inquiries = Inquiry::with(['user', 'property'])
                ->where('user_id', $user->id)
                ->get();
        }

        return response()->json($inquiries);
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'message'     => 'required|string|max:1000',
        ]);

        
        $property = Property::findOrFail($validated['property_id']);

        if ($property->user_id === $request->user()->id) {
            return response()->json([
                'message' => 'Ne možete poslati upit za svoju nekretninu.',
            ], 403);
        }

        $inquiry = Inquiry::create([
            'user_id'     => $request->user()->id,
            'property_id' => $validated['property_id'],
            'message'     => $validated['message'],
            'status'      => 'pending',
        ]);

        return response()->json([
            'message' => 'Upit uspešno poslat.',
            'inquiry' => $inquiry->load(['user', 'property']),
        ], 201);
    }

    
    public function show(Request $request, $id)
    {
        $inquiry = Inquiry::with(['user', 'property'])->findOrFail($id);

        $user = $request->user();

        
        if (
            $user->role !== 'admin' &&
            $inquiry->user_id !== $user->id &&
            $inquiry->property->user_id !== $user->id
        ) {
            return response()->json(['message' => 'Zabranjen pristup.'], 403);
        }

        return response()->json($inquiry);
    }

     public function update(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);

        $user = $request->user();

        if ($user->role !== 'admin' && $inquiry->user_id !== $user->id) {
            return response()->json(['message' => 'Zabranjen pristup.'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $inquiry->update($validated);

        return response()->json([
            'message' => 'Upit uspešno izmenjen.',
            'inquiry' => $inquiry->load(['user', 'property']),
        ]);
    }

    
    public function updateStatus(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);

        $user = $request->user();

        
        if (
            $user->role !== 'admin' &&
            $inquiry->property->user_id !== $user->id
        ) {
            return response()->json(['message' => 'Zabranjen pristup.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,answered,closed',
        ]);

        $inquiry->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Status upita ažuriran.',
            'inquiry' => $inquiry,
        ]);
    }

    
    public function destroy(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);

        $user = $request->user();

        if ($user->role !== 'admin' && $inquiry->user_id !== $user->id) {
            return response()->json(['message' => 'Zabranjen pristup.'], 403);
        }

        $inquiry->delete();

        return response()->json(['message' => 'Upit obrisan.']);
    }
}