<?php

namespace App\Http\Controllers;

use App\Models\Investiment;
use App\Models\Transaction;
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
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = request()->only([
            'id_user',
            'title',
            'amount_invested'
        ]);

        $investiment = $this->investimentService->createInvestiment(
            $data,
            $request->amount_invested
        );

        return response()->json(['investiment' => $investiment], 201);
    }
    public function updateInvestiment(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'title' => 'nullable|string',

            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $filterData = array_filter(
            $request->only([
                'title',
            ]),
            function ($value) {
                return $value !== null && $value !== '';
            }
        );
        $investiment = $this->investimentService->updateInvestiment(
            $request->id,
            $filterData
        );

        return response()->json(['investiment' => $investiment], 200);
    }
    public function deleteInvestiment(Request $request): JsonResponse
    {
        $investiment = $this->investimentService->deleteInvestiment($request->id);
        return response()->json(['investiment' => $investiment], 200);
    }
    public function listInvestiment(): JsonResponse
    {
        $investiment = $this->investimentService->listInvestiment();
        return response()->json(['investiments' => $investiment], 200);
    }
    public function listInvestimentById(Request $request): JsonResponse
    {
        $investiment = Investiment::find($request->id);
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
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $investiment = $this->investimentService->profitability($request->id);
        
        return response()->json([
            'profitability' => $investiment['profitability'],
            'investiment_id' => $investiment['investiment_id']
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
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $investiment = Investiment::where('id', $request->id)->where('id_user', $this->getUserId())->firstOrFail();
        $investiment->amount_invested += $request->amount;
        $investiment->current_amount += $request->amount;
        $investiment->save();

        return response()->json(['investiment' => $investiment], 200);
    }
    public function withDraw(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'devolution_amount' => 'required|numeric|gt:0',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $investiment = $this->investimentService->withDraw($request->id, $request->devolution_amount);
        return response()->json([
            'investiment' => $investiment['investiment'],
            'transaction' => $investiment['transaction']
        ], 200);
    }
}
