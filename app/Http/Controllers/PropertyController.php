<?php
namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    // GET /api/properties — javno, svi mogu da vide
    public function index()
    {
        $properties = Property::with('user')->get();

        return response()->json($properties);
    }

    // GET /api/properties/{id}
    public function show($id)
    {
        $property = Property::with(['user', 'features', 'inquiries'])->find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return response()->json($property);
    }

    // POST /api/properties
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'location'    => 'required|string',
            'type'        => 'required|in:apartment,house,commercial',
            'bedrooms'    => 'nullable|integer|min:0',
            'bathrooms'   => 'nullable|integer|min:0',
            'area_sqm'    => 'nullable|numeric|min:0',
            'status'      => 'nullable|in:available,sold,rented',
        ]);

        $property = Property::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message'  => 'Property created successfully',
            'property' => $property,
        ], 201);
    }

    // PUT /api/properties/{id}
    public function update(Request $request, $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        // Samo vlasnik može da menja
        if ($property->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric|min:0',
            'location'    => 'sometimes|string',
            'type'        => 'sometimes|in:apartment,house,commercial',
            'bedrooms'    => 'nullable|integer|min:0',
            'bathrooms'   => 'nullable|integer|min:0',
            'area_sqm'    => 'nullable|numeric|min:0',
            'status'      => 'nullable|in:available,sold,rented',
        ]);

        $property->update($request->validated());

        return response()->json([
            'message'  => 'Property updated successfully',
            'property' => $property,
        ]);
    }

    // DELETE /api/properties/{id}
    public function destroy(Request $request, $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        // Samo vlasnik može da briše
        if ($property->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully']);
    }

    // GET /api/my-properties — samo ulogovani korisnik vidi svoje
    public function myProperties(Request $request)
    {
        $properties = Property::where('user_id', $request->user()->id)
            ->with('features')
            ->get();

        return response()->json($properties);
    }
}