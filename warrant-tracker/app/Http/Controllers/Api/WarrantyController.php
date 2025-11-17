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
    $rows = Auth::user()
                ->warranties()          // relationship
                ->with('product')       // eager-load product name/brand
                ->get();

    // wrap so the JS can keep using  (await request('/warranties')).data
    return response()->json(['data' => $rows]);
}

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'product_id'      => 'required|exists:products,id',
        'customer_name'   => 'required|string|max:255',
        'serial_number'   => 'nullable|string|max:255|unique:warranties',
        'purchase_date'   => 'required|date',
        'duration_months' => 'required|integer|min:1',
        'provider'        => 'required|string|max:255',
        'notes'           => 'nullable|string',
        'status' => ['nullable', Rule::in(['active','claimed','expired'])],

    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    $data = $validator->validated();

    // calculate expiry
    $purchase = new \DateTime($data['purchase_date']);
    $purchase->add(new \DateInterval('P' . $data['duration_months'] . 'M'));
    $data['expiry_date'] = $purchase->format('Y-m-d');

    $warranty = Auth::user()->warranties()->create($data);
    $warranty->load('product');

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
            'customer_name' => 'string|max:255',
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
            'status' => ['sometimes', Rule::in(['active','claimed','expired'])],
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
    public function claim(Request $request, $id)
{
    $warranty = Warranty::findOrFail($id);

    // Optionally check if already claimed
    if ($warranty->status === 'claimed') {
        return response()->json(['message' => 'Already claimed', 'data' => $warranty], Response::HTTP_OK);
    }

    $warranty->status = 'claimed';
    $warranty->save();

    return response()->json(['data' => $warranty], Response::HTTP_OK);
}

}
