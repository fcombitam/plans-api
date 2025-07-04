<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $per_page = $request->get('per_page', 10);

        $tenant = Auth::guard('tenant')->user();

        $users = $tenant->users()->paginate($per_page);

        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $tenant = Auth::guard('tenant')->user();

        $activePlan = $tenant->activePlan();

        if (!isset($activePlan) || empty($activePlan) || !$activePlan) {
            return response()->json([
                'message' => 'Debes activar un nuevo plan',
                'status' => false
            ], 400);
        }

        $data['tenant_id'] = Auth::guard('tenant')->id();

        $user = User::create($data);

        $activePlan->remaining_users -= 1;
        if ($activePlan->remaining_users <= 0) {
            $activePlan->status = Subscription::STATUS_INACTIVE;
        }
        $activePlan->save();

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'user' => $user,
            'status' => true
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $user->fill($data);

        $tenant = Auth::guard('tenant')->user();

        if ($user->tenant_id !== $tenant->id) {
            return response()->json([
                'message' => 'No puedes modificar informacion',
                'status' => false
            ], 400);
        }

        if (!$user->isDirty()) {
            return response()->json([
                'message' => 'No se modificaron campos',
                'status' => false
            ], 400);
        }

        $user->save();

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user' => $user,
            'status' => true
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $tenant = Auth::guard('tenant')->user();

        if ($user->tenant_id !== $tenant->id) {
            return response()->json([
                'message' => 'No puedes eliminar informacion',
                'status' => false
            ], 400);
        }

        $oldUser = $user;

        $user->delete();

        return response()->json([
            'message' => "El usuario $oldUser->name ha sido eliminado correctamente",
            'status' => true
        ], 200);
    }
}
