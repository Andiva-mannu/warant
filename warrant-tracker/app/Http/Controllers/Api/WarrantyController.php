<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warranty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WarrantyController extends Controller
{
    /**
     * Display a listing of the user's warranties.
     */
    public function index()
    {
        // Return only the warranties belonging to the authenticated user
        return Auth::user()->warranties()->with('product')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'serial_number' => 'nullable|string|max:255|unique:warranties',
            'purchase_date' => 'required|date',
            'duration_months' => 'required|integer|min:1',
            'provider' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create the warranty and assign it to the logged-in user
        $warranty = Auth::user()->warranties()->create($validator->validated());
        $warranty->load('product'); // Load product info for the response

        return response()->json($warranty, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Warranty $warranty)
    {
        // Check if the user is authorized to see this warranty
        if ($warranty->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        $warranty->load('product');
        return $warranty;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warranty $warranty)
    {
        // Check authorization
        if ($warranty->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => 'exists:products,id',
            'serial_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('warranties')->ignore($warranty->id),
            ],
            'purchase_date' => 'date',
            'duration_months' => 'integer|min:1',
            'provider' => 'string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $warranty->update($validator->validated());
        $warranty->load('product');

        return response()->json($warranty);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warranty $warranty)
    {
        // Check authorization
        if ($warranty->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $warranty->delete();
        return response()->json(null, 204);
    }
}
