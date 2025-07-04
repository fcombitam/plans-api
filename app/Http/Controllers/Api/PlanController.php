<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $per_page = $request->get('per_page', 10);

        $plans = Plan::paginate($per_page);

        return response()->json($plans, 200);
    }

    public function store(StorePlanRequest $request)
    {
        $data = $request->validated();

        if (isset($data['features']) && !empty($data['features']) && $data['features']) {
            $data['features'] = json_encode($data['features']);
        }

        $plan = Plan::create($data);

        return response()->json([
            'message' => 'Plan creado correctamente',
            'plan' => $plan,
            'status' => true
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanRequest $request)
    {
        $data = $request->validated();

        if (isset($data['features']) && !empty($data['features']) && $data['features']) {
            $data['features'] = json_encode($data['features']);
        }

        $plan = Plan::find($request->plan_id);

        $plan->fill($data);

        if (!$plan->isDirty()) {
            return response()->json([
                'message' => 'No se ha actualizado ningun dato',
                'status' => false
            ], 400);
        }

        $plan->save();

        return response()->json([
            'message' => 'Plan actualizado correctamente',
            'status' => true,
            'data' => $plan
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        $oldPlan = $plan;

        $plan->delete();

        return response()->json([
            'message' => "El plan $oldPlan->name ha sido eliminado correctamente",
            'status' => true
        ], 200);
    }
}
