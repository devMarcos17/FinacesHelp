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
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }
        $goal = new Goal();
        $goal->id_user = $this->getUserId();
        $goal->title = $request->title;
        $goal->description = $request->description;
        $goal->target_year = $request->target_year;
        $goal->target_date = $request->target_date;
        $goal->current_amount = 0;
        $goal->target_amount = $request->target_amount;

        if ($request->hasFile('image_path') && $request->file('image_path')->isValid()) {
            $file = $request->file('image_path');

            $extension = $file->getClientOriginalExtension();


            $imageName = md5($file->getClientOriginalName()) . '_' . time() . '.' . $extension;

            $file->move(public_path('img/goals'), $imageName);

            $goal->image_path = $imageName;
        }

        $goal->save();

        return response()->json(['goal' => $goal], 201);
    }
    public function updateGoal(Request $request): JsonResponse
    {
        $goal = Goal::find($request->id);
        if (!$goal) {
            return response()->json(['message' => 'not found'], 404);
        }
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'id_user' => 'nullable|integer',
                'title' => 'nullable|string',
                'description' => 'nullable|string',
                'target_year' => 'nullable|integer',
                'target_date' => 'nullable|date',
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
                'current_amount'
            ]),
            function ($value) {
                return $value !== null && $value !== '';
            }
        );
        if ($request->hasFile('image_path') && $request->file('image_path')->isValid()) {
            $file = $request->file('image_path');

            $extension = $file->getClientOriginalExtension();

            $imageName = md5($file->getClientOriginalName())
                . '_' . time()
                . '.' . $extension;

            $file->move(public_path('img/goals'), $imageName);

            $filterData['image_path'] = $imageName;
        }

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
    public function listGoalUser(): JsonResponse
    {
        $goals = Goal::where('id_user', $this->getUserId())->get();
        return response()->json(['goal' => $goals], 200);
    }
    public function deleteGoal(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'id_user' => 'nullable|integer',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $goal = Goal::find($request->id);
        if (!$goal) {
            return response()->json(['message' => 'not found'], 404);
        }
        $goal = Goal::where('id', $request->id)->where('id_user', $this->getUserId())->firstOrFail();
        $goal->delete();
        return response()->json(['goal' => $goal], 200);
    }
    public function deposit(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'current_amount' => 'required|numeric',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $goal = Goal::where('id', $request->id)->where('id_user', $this->getUserId())->firstOrFail();
        $goal->current_amount += $request->current_amount;
        $goal->save();

        return response()->json(['goal' => $goal], 200);
    }
    public function progress(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        $goal = Goal::where('id', $request->id)
            ->where('id_user', $this->getUserId())
            ->firstOrFail();

        if ($goal->current_amount <= 0) {
            return response()->json([
                'percentage' => 0
            ], 200);
        }

        $percentage = min(
            100,
            ($goal->current_amount / $goal->target_amount) * 100
        );

        return response()->json([
            'percentage' => round($percentage, 2)
        ], 200);
    }
}
