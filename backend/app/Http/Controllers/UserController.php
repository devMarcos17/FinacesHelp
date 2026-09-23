<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class UserController extends Controller
{
    private function getUserId(): int
    {
        /** @var User|null $user */
        $user = Auth::user();

        return (int) ($user ? $user->id : Auth::id());
    }
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:7',
                'phone' => 'required|string',
                'cpf' => 'required|string',
                'date_of_birt' => 'required|date_format:d/m/Y',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
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
        $users = User::orderBy('name', 'asc')->get();

        return response()->json(['user' => $users], 200);
    }

    public function listById(Request $request): JsonResponse
    {
        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['message' => 'not found'], 404);
        }

        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        return response()->json(['user' => $user], 200);
    }

    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);

        /** @var string|false $token */
        $token = Auth::guard()->attempt($credentials);

        if (!$token) {
            return response()->json([
                'erro' => 'Invalid credentials'
            ], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'role' => auth('api')->user()->role,
        ]);
    }

    public function me(): JsonResponse
    {
        return response()->json(Auth::user());
    }

    public function logout(): JsonResponse
    {
        Auth::guard()->logout();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function refresh(): JsonResponse
    {
        /** @var mixed $auth */
        $auth = Auth::guard();

        return $this->respondWithToken($auth->refresh());
    }

    public function respondWithToken(mixed $token): JsonResponse
    {
        /** @var mixed $auth */
        $auth = Auth::guard();

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $auth->factory()->getTTL() * 60
        ]);
    }
    public function update(Request $request): JsonResponse
    {
        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['message' => 'not found'], 404);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required|integer',
                'id_user' => 'nullable|integer',
                'name' => 'nullable|string',
                'email' => 'nullable|email|unique:users',
                'password' => 'nullable|min:7',
                'phone' => 'nullable|string',
                'cpf' => 'nullable|string',
                'date_of_birt' => 'nullable|date_format:d/m/Y',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $filterData = array_filter($request->only(['name', 'email', 'password', 'phone', 'cpf', 'status', 'date_of_birt']), function ($value) {
            return $value !== null && $value !== '';
        });

        $user->update($filterData);
        return response()->json(['user' => $user], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
                'id_user' => 'nullable|integer',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['message' => 'not found'], 404);
        }

        $user->delete();
        return response()->json(['user' => $user], 200);
    }
    public function disableUser(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $user = User::where('id', $request->id)->where('status', 'ativo')->first();
        if ($user) {
            $user->status = 'inativo';
            $user->save();

            return response()->json(['user' => $user], 200);
        }
        return response()->json(['message' => 'User not found or already active'], 404);
    }
    public function activeUser(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = User::where('id', $request->id)->where('status', 'inativo')->first();
        if ($user) {
            $user->status = 'ativo';
            $user->save();
            return response()->json(['user' => $user], 200);
        }
        return response()->json(['message' => 'User not found or already disable'], 404);
    }
    public function getActiveUsers(): JsonResponse
    {
        $users = User::where('status', 'ativo')->orderBy('name', 'asc')->get();
        return response()->json(['users' => $users], 200);
    }
    public function getDisableUsers(): JsonResponse
    {
        $users = User::where('status', 'inativo')->orderBy('name', 'asc')->get();
        return response()->json(['users' => $users], 200);
    }
    public function getAdminsTotal(): JsonResponse
    {
        $usersAdmin = User::where('role', 'admin')->count('users');
        return response()->json(['users' => $usersAdmin], 200);
    }
    public function getUsersTotal(): JsonResponse
    {
        $users = User::count('users');
        return response()->json(['users' => $users], 200);
    }
    public function getDisableUsersTotal(): JsonResponse
    {
        $users = User::where('status', 'inativo')->count('users');
        return response()->json(['users' => $users], 200);
    }
    public function getActiveUsersTotal(): JsonResponse
    {
        $users = User::where('status', 'ativo')->count('users');
        return response()->json(['users' => $users], 200);
    }
    public function search(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'search' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $search = $request->search;

        $user = User::where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('email', 'LIKE', '%' . $search . '%')
                ->orWhere('status', 'LIKE', '%' . $search . '%');
        })->get();
        if (!$user) {
            return response()->json(['message' => 'not found'], 404);
        }
        return response()->json(['user' => $user], 200);
    }

    public function filterUsersMonth(): JsonResponse
    {
        $users = User::where('created_at', '>=', Carbon::now()->subDays(30))->get();
        return response()->json(['users' => $users], 200);
    }
    public function filterDataOld(): JsonResponse
    {
        $users = User::orderBy('created_at', 'ASC')->get();

        return response()->json(['users' => $users], 200);
    }
    public function filterDataRecent(): JsonResponse
    {
        $users = User::orderBy('created_at', 'DESC')->get();
        return response()->json(['users' => $users], 200);
    }
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'email' => 'required|email',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $password = Password::sendResetLink($request->only('email'));
        return response()->json(['password' => $password], 200);
    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make(
            request()->all(),
            [
                'email' => 'required|email',
                'token' => 'required|string',
                'password' => 'required|confirmed',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $status = Password::reset(
            $request->only('email', 'token', 'password', 'password_confirmation'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
                $user->save();
            }
        );
        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['status' => $status], 200);
        }
        return response()->json(['error' => 'failed to redefine password'], 400);
    }
}
