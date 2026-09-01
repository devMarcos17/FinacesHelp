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
    public function __construct(private GoalService $goalService)
    {
        $this->goalService = new GoalService();
    }
    //
    public function createGoal(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id_user' => 'required|integer|exists:users,id',
                'title' => 'required|string',
                'description' => 'nullable|string',
                'target_year' => 'required|integer',
                'target_date' => 'nullable|date',
                'image_path' => 'nullable|string',
                'target_amount' => 'required|numeric',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }
        $goal = new Goal();
        $goal->id_user = $request->id_user;
        $goal->title = $request->title;
        $goal->description = $request->description;
        $goal->target_year = $request->target_year;
        $goal->target_date = $request->target_date;
        $goal->image_path = $request->image_path;
        $goal->current_amount = 0;
        $goal->target_amount = $request->target_amount;

        $goal->save();

        return response()->json(['goal' => $goal], 201);
    }
    public function updateGoal(int $id, Request $request)
    {
        $goal = Goal::find($id);
        if (!$goal) {
            return response()->json(['message' => 'not found'], 404);
        }
        $validator = Validator::make(
            request()->all(),
            [
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'target_year' => 'nullable|integer',
                'target_date' => 'nullable|date',
                'image_path' => 'nullable|string',
                'current_amount' => 'nullable|numeric',

            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }
        $filterData = array_filter(
            $request->only([
                'title',
                'description',
                'target_year',
                'target_date',
                'image_path',
                'current_amount'
            ]),
            function ($value) {
                return $value !== null && $value !== '';
            }
        );
        $goal->update($filterData);

        return response()->json(['goal' => $goal], 200);
    }
    public function listGoal(int $id): JsonResponse
    {
        $goal = Goal::find($id);
        if (!$goal) {
            return response()->json(['message' => 'not found'], 404);
        }

        return response()->json(['goal' => $goal], 200);
    }
    public function deleteGoal(int $id): JsonResponse
    {
        $goal = Goal::find($id);
        if (!$goal) {
            return response()->json(['message' => 'not found'], 404);
        }
        $goal->delete();
        return response()->json(['goal' => $goal], 200);
    }
    public function deposit(int $id, Request $request)
    {
        $validator = Validator::make(request()->all(),
        [
            'target_amount' => 'required|numeric',
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 400);
        }
        $goal = Goal::where('id', $id)->where('id_user', $this->getUserId())->firstOrFail();
        $goal->current_amount += $request->target_amount;
        $goal->save();

        return response()->json(['goal' => $goal], 200);
    }
    public function progress(int $id): JsonResponse
    {
        $percentage  = $this->goalService->progress($id);
        return response()->json(['progress' => $percentage], 200);

    }
}
