<?php

namespace App\Http\Controllers;

use App\Models\Investiment;
use App\Services\InvestimentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InvestimentController extends Controller
{
    public function __construct(private InvestimentService $investimentService)
    {
        $this->investimentService = new InvestimentService();
    }
    //
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

    public function createInvestiment(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'title' => 'required|string',
                'amount_invested' => 'required|numeric',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()]);
        }
        $investiment = new Investiment();

        $investiment->id_user = $this->getUserId();
        $investiment->title = $request->title;
        $investiment->amount_invested = $request->amount_invested;
        $investiment->current_amount = $request->amount_invested;

        $investiment->save();

        return response()->json(['investiment' => $investiment], 201);
    }
    public function updateInvestiment(Request $request): JsonResponse
    {
        $investiment = Investiment::find($request->id);
        if (!$investiment) {
            return response()->json(['message' => 'not found'], 404);
        }
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'title' => 'nullable|string',

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
            ]),
            function ($value) {
                return $value !== null && $value !== '';
            }
        );
        $investiment->update($filterData);

        return response()->json(['investiment' => $investiment], 200);
    }
    public function deleteInvestiment(Request $request): JsonResponse
    {
        $investiment = Investiment::find($request->id);
        if (!$investiment) {
            return response()->json(['message' => 'not found'], 404);
        }
        $investiment->delete();
        return response()->json(['investiment' => $investiment], 200);
    }
    public function listInvestiment(): JsonResponse
    {
        $investiment = Investiment::where('id_user', $this->getUserId())->get();
        return response()->json(['investiments' => $investiment], 200);
    }
    public function listInvestimentId(int $id): JsonResponse
    {
        $investiment = Investiment::find($id);
        if (!$investiment) {
            return response()->json(['message' => 'not found'], 404);
        }
        return response()->json(['investiment' => $investiment], 200);
    }
    public function profitability(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:investiments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $investiment = Investiment::where('id', $request->id)
            ->where('id_user', $this->getUserId())
            ->first();

        if (!$investiment || $investiment->amount_invested <= 0) {
            return response()->json([
                'profitability' => 0.0
            ], 200);
        }

        $profitability = (($investiment->current_amount - $investiment->amount_invested) / $investiment->amount_invested) * 100;

        return response()->json([
            'investiment_id' => $investiment->id,
            'profitability' => round($profitability, 2)
        ], 200);
    }
    public function deposit(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'amount' => 'required|numeric|gt:0',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $investiment = Investiment::where('id', $request->id)->where('id_user', $this->getUserId())->firstOrFail();
        $investiment->amount_invested += $request->amount;
        $investiment->current_amount += $request->amount;
        $investiment->save();

        return response()->json(['investiment' => $investiment], 200);
    }
}
