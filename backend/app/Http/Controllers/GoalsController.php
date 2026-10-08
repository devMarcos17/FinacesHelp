<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Services\GoalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GoalsController extends Controller
{
    public function __construct(private GoalService $goalService)
    {
        $this->goalService = new GoalService();
    }
    /**
     *
     * @return int
     */
    private function getUserId(): int
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return (int) ($user ? $user->id : Auth::id());
    }
    //
    public function createGoal(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'title' => 'required|string',
                'description' => 'nullable|string',
                'target_year' => 'required|integer',
                'target_date' => 'nullable|date',
                'image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'target_amount' => 'required|numeric',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'title',
            'description',
            'target_year',
            'target_date',
            'target_amount'
        ]);

        $goal = $this->goalService->createGoal($data, $request->file('image_path'));
        return response()->json(['goal' => $goal], 201);
    }
    public function updateGoal(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'target_year' => 'nullable|integer',
                'target_date' => 'nullable|date',
                'current_amount' => 'nullable|numeric',

            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $filterData = array_filter(
            $request->only([
                'title',
                'description',
                'target_year',
                'target_date',
                'current_amount'
            ]),
            function ($value) {
                return $value !== null && $value !== '';
            }
        );

        $goal = $this->goalService->updateGoal(
            $filterData, 
            $request->image_path, 
            (int)$request->id);

        return response()->json(['goal' => $goal], 200);
    }
    public function listGoalId(Request $request): JsonResponse
    {
        $goal = Goal::find($request->id);
        if (!$goal) {
            return response()->json(['message' => 'not found'], 404);
        }

        return response()->json(['goal' => $goal], 200);
    }
    public function listGoalUser(): JsonResponse
    {
        $goals = $this->goalService->listGoalUser();
        return response()->json(['goal' => $goals], 200);
    }
    public function deleteGoal(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $goal = Goal::find($request->id);
        if (!$goal) {
            return response()->json(['message' => 'not found'], 404);
        }
        $goal = $this->goalService->deleteGoal($request->id);
        return response()->json(['goal' => $goal], 200);
    }
    public function deposit(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required|integer',
                'current_amount' => 'required|numeric|min:0.01',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $goal = $this->goalService->deposit($request->id, (float)$request->current_amount);

        return response()->json([
            'goal' => $goal
        ], 200);
    }
    public function withDraw(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'devolution_amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $goal = $this->goalService->withDraw($request->id, $request->devolution_amount);
        if(!$goal){
            return response()->json(['message' => 'insufficient balance'], 400);
        }

        return response()->json([
            'goal' => $goal
        ], 200);
    }
    public function progress(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $goalPercentage = $this->goalService->progress($request->id);

        return response()->json([
            'percentage' => $goalPercentage
        ], 200);
    }
    public function listGoalCompleted(): JsonResponse
    {
        $goal = $this->goalService->listGoalCompleted();
        if (!$goal) {
            return response()->json(['message' => 'not found'], 404);
        }
        return response()->json(['goal' => $goal], 200);
    }
    public function listGoalCancelled(): JsonResponse
    {
       $goal = $this->goalService->listGoalCancelled();
        if (!$goal) {
            return response()->json(['message' => 'not found'], 404);
        }
        return response()->json(['goal' => $goal], 200);
    }
}
