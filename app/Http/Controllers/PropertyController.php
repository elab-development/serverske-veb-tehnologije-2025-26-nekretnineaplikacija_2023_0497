<?php
namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\PropertyCollection;
use Symfony\Component\HttpFoundation\StreamedResponse;


class PropertyController extends Controller
{
    
    public function index()
    {
        $properties = Property::with('user')->get();

        return new PropertyCollection($properties);
    }

    
    public function show($id)
    {
        $property = Property::with(['user', 'features', 'inquiries'])->find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        return new PropertyResource($property);
    }

    
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

    
    public function update(Request $request, $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        
        if ($property->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
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

        $property->update($validated);

        return response()->json([
            'message'  => 'Property updated successfully',
            'property' => $property,
        ]);
    }

    
    public function destroy(Request $request, $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['message' => 'Property not found'], 404);
        }

        
        if ($property->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully']);
    }

    
    public function myProperties(Request $request)
    {
        $properties = Property::where('user_id', $request->user()->id)
            ->with('features')
            ->paginate(10);

        return new PropertyCollection($properties);
    }

    
    public function export(Request $request): StreamedResponse
    {
        $properties = Property::with('user')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="properties.csv"',
        ];

        $callback = function () use ($properties) {
            $file = fopen('php://output', 'w');

            
            fputcsv($file, [
                'ID', 'Title', 'Price', 'Location',
                'Type', 'Bedrooms', 'Bathrooms',
                'Area (sqm)', 'Status', 'Owner', 'Created At'
            ]);

            
            foreach ($properties as $property) {
                fputcsv($file, [
                    $property->id,
                    $property->title,
                    $property->price,
                    $property->location,
                    $property->type,
                    $property->bedrooms,
                    $property->bathrooms,
                    $property->area_sqm,
                    $property->status,
                    $property->user->name,
                    $property->created_at->format('d.m.Y'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}