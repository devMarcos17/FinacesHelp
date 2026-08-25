<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:7',
                'phone' => 'required|string|regex:/^\+[1-9]\d{7,14}$/',
                'cpf' => 'required|string|regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
                'date_of_birt' => 'required|date_format:d/m/Y',

            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson());
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->phone = $request->phone;
        $user->cpf = $request->cpf;
        $user->date_of_birt = $request->date_of_birt;
        $user->status = 'ativo';
        $user->role = 'user';

        $user->save();

        return response()->json(['message' => $user], 201);
    }
    public function list(): JsonResponse
    {
        $user = User::all();
        if (!$user) {
            return response()->json(['message' => 'not foud'], 404);
        }
        return response()->json(['user' => $user], 200);
    }
    public function listId(int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'not found'], 404);
        }

        return response()->json(['user' => $user], 200);
    }
    public function login()
    {
        $credentiais = request(['email', 'password']);
        if (!$token = auth()->attempt($credentiais)) {
            return response()->json([
                'erro' => 'Invalid credentials'
            ], 401);
        }
        return $this->respondWithToken($token);
    }
    public function me(): JsonResponse
    {
        return response()->json(auth()->user());
    }
    public function logout(): JsonResponse
    {
        auth()->logout();
        return response()->json([
            'message' => 'Sucessully logged out'
        ]);
    }
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
    public function respondWithToken(mixed $token): JsonResponse
    {
        return response()->json([
            'acess_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
    public function update(int $id, Request $request): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'not found'], 404);
        }
        $validator = Validator::make(
            request()->all(),
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:7',
                'phone' => 'required|string|regex:/^\+[1-9]\d{7,14}$/',
                'cpf' => 'required|string|regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
                'date_of_birt' => 'required|date|regex:/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/([0-9]{4})$/',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson());
        }
        $filterData = array_filter($request->only(['name', 'email', 'password', 'phone', 'cpf', 'status', 'date_of_birt']), function ($value) {
            return $value !== null && $value !== '';
        });

        $user->update($filterData);
        return response()->json(['user' => $user], 200);
    }
    public function delete(int $id): JsonResponse
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'not found'], 404);
        }
        $user->delete();
        return response()->json(['user' => $user], 200);
    }
}
