<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssigPlanToTenantRequest;
use App\Http\Requests\DestroyTenantRequest;
use App\Http\Requests\UpdateTenantRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTenantRequest $request)
    {
        $data = $request->validated();

        if (isset($data['password']) || !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $tenant = Tenant::find(Auth::guard('tenant')->id());

        $tenant->fill($data);

        if (!$tenant->isDirty()) {
            return response()->json([
                'message' => 'No se ha actualizado ningun dato',
                'status' => false
            ], 400);
        }

        $tenant->save();

        return response()->json([
            'message' => 'Empresa actualizada correctamente',
            'status' => true,
            'data' => $tenant
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyTenantRequest $request)
    {
        $tenant = Tenant::find(Auth::guard('tenant')->id());

        if ($tenant->tenant_code !== $request->tenant_code) {
            return response()->json([
                'message' => 'No se realiza la eliminacion',
                'status' => false
            ], 400);
        }

        $oldTenant = $tenant->name;

        $tenant->tokens()->delete();

        $tenant->delete();

        return response()->json([
            'message' => "La empresa $oldTenant ha sido eliminada correctamente",
            'status' => true
        ], 200);
    }

    public function assignPlan(AssigPlanToTenantRequest $request)
    {
        $tenant = Auth::guard('tenant')->user();
        $validatePlan = $tenant->activePlan();

        if (isset($validatePlan) && !empty($validatePlan) && $validatePlan) {
            return response()->json([
                'message' => "No se realiza la asignacion ya que aun cuenta con un plan activo, restan $validatePlan->remaining_users usuarios en su plan",
                'status' => false
            ], 400);
        }

        $plan = Plan::find($request->plan_id);

        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'remaining_users' => $plan->users_limit,
            'date_assign' => now(),
            'status' => Subscription::STATUS_ACTIVE
        ]);

        return response()->json([
            'message' => "El plan $plan->name ha sido asignado correctamente",
            'status' => true,
            'subscription' => $subscription
        ], 200);
    }
}
