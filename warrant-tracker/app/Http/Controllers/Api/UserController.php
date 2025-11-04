<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     * (Assuming only an admin should do this)
     */
    public function index()
    {
        $this->authorizeIndex();

        // Basic search, sort and pagination support
        $perPage = (int) request()->query('per_page', 15);
        $q = request()->query('q');
        $sort = request()->query('sort', 'created_at');
        $direction = request()->query('direction', 'desc');

        $query = User::query();

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $query->orderBy($sort, $direction);

        // A paginator object is already a perfect JSON response. No change needed.
        // It already contains 'data', 'links', 'meta', etc.
        return $query->paginate($perPage);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorizeIndex();
        
        // <-- CHANGED: Wrap the response
        return response()->json([
            'status' => 'success',
            'data' => $user,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorizeIndex();

        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Non-admins cannot change is_admin
        if (!Auth::user()->is_admin && array_key_exists('is_admin', $data)) {
            unset($data['is_admin']);
        }

        $user->update($data);

        // <-- CHANGED: Wrap the response and use 200 OK
        return response()->json([
            'status' => 'success',
            'data' => $user,
        ], 200);
    }

    /**
     * Store a newly created user (admin only).
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorizeIndex();

        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        // Only admin can set is_admin (authorizeIndex ensures caller is admin)
        $user = User::create($data);

        // <-- CHANGED: Wrap the response (status code 201 is correct)
        return response()->json([
            'status' => 'success',
            'data' => $user,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorizeIndex();

        // Don't let a user delete themselves via this endpoint
        if ($user->id === Auth::id()) {
            
            // <-- CHANGED: Wrap this "fail" response
            return response()->json([
                'status' => 'fail',
                'data' => ['message' => 'Cannot delete self'],
            ], 422);
        }

        $user->delete();

        // <-- CHANGED: Use a 200 with a message instead of 204.
        // A 204 response *must* have an empty body, which isn't as helpful.
        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully',
        ], 200);
    }

    /**
     * Basic admin check — extracted for reuse.
     */
    protected function authorizeIndex()
    {
        $u = Auth::user();
        if (!$u || !$u->is_admin) {
            
            // <-- CHANGED: This is a more robust way to force a JSON response.
            // abort(403) can sometimes return an HTML error page.
            // This *throws* a response, which stops execution just like abort().
            response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. You must be an admin to perform this action.',
            ], 403)->throwResponse();
        }
    }
}

