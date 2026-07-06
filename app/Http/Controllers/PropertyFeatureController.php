<?php
namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyFeature;
use Illuminate\Http\Request;

class PropertyFeatureController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $features = PropertyFeature::with(['property.user'])->get();
        } else {
            $features = PropertyFeature::whereHas('property', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['property.user'])->get();
        }

        return response()->json(['features' => $features]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id'   => 'required|exists:properties,id',
            'feature_name'  => 'required|string|max:255',
            'feature_value' => 'nullable|string|max:255',
        ]);

        $property = Property::findOrFail($validated['property_id']);

        if ($request->user()->role !== 'admin' && $property->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $feature = PropertyFeature::create($validated);

        return response()->json([
            'message' => 'Feature added successfully',
            'feature' => $feature,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $feature = PropertyFeature::with('property')->findOrFail($id);

        if ($request->user()->role !== 'admin' && $feature->property->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($feature);
    }

    public function update(Request $request, $id)
    {
        $feature = PropertyFeature::with('property')->findOrFail($id);

        if ($request->user()->role !== 'admin' && $feature->property->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'feature_name'  => 'sometimes|string|max:255',
            'feature_value' => 'nullable|string|max:255',
        ]);

        $feature->update($validated);

        return response()->json([
            'message' => 'Feature updated successfully',
            'feature' => $feature,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $feature = PropertyFeature::with('property')->findOrFail($id);

        if ($request->user()->role !== 'admin' && $feature->property->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $feature->delete();

        return response()->json(['message' => 'Feature deleted successfully']);
    }
}
