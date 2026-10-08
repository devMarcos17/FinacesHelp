<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
        $this->userService = new UserService();
    }
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
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = request()->only([
            'name',
            'email',
            'password',
            'phone',
            'cpf',
            'date_of_birt'
        ]);

        $user = $this->userService->registerUser($data);

        return response()->json(['message' => $user], 201);
    }

    public function list(): JsonResponse
    {
        $users = User::orderBy('id', 'asc')->get();

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
            return response()->json(['errors' => $validator->errors()], 422);
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
            'expires_in'   => JWTAuth::factory()->getTTL() * 60,
            'role'         => auth('api')->user()->role,
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
        $validator = Validator::make(
            request()->all(),
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $filterData = array_filter($request->only([
            'name',
            'email',
            'password',
            'phone',
            'cpf',
            'status',
            'date_of_birt'
        ]), function ($value) {
            return $value !== null && $value !== '';
        });

        if (isset($filterData['password'])) {
            $filterData['password'] = Hash::make($filterData['password']);
        }

        $user = $this->userService->updateUser($request->id, $filterData);

        return response()->json(['user' => $user], 200);
    }

    public function delete(Request $request): JsonResponse
    {
        $validator = Validator::make(
            request()->all(),
            [
                'id' => 'required|integer'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 422]);
        }
        $user = $this->userService->deleteUser($request->id);

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
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = $this->userService->disableUser($request->id);
        return response()->json(['user' => $user]);
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $this->userService->activeUser($request->id);
        return response()->json(['user' => $user], 200);
    }
    public function getActiveUsers(): JsonResponse
    {
        $users = $this->userService->getActiveUsers();
        return response()->json(['users' => $users], 200);
    }
    public function getDisableUsers(): JsonResponse
    {
        $users = $this->userService->getDisableUsers();
        return response()->json(['users' => $users], 200);
    }
    public function getAdminsTotal(): JsonResponse
    {
        $userAdm = $this->userService->getAdminsTotal();
        return response()->json(['users' => $userAdm], 200);
    }
    public function getUsersTotal(): JsonResponse
    {
        $usersTotal = $this->userService->getUsersTotal();
        return response()->json(['users' => $usersTotal], 200);
    }
    public function getDisableUsersTotal(): JsonResponse
    {
        $usersDisable = $this->userService->getDisableUsersTotal();
        return response()->json(['users' => $usersDisable], 200);
    }
    public function getActiveUsersTotal(): JsonResponse
    {
        $usersActive = $this->userService->getActiveUsersTotal();
        return response()->json(['users' => $usersActive], 200);
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
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $search = $request->search;

        $userSearch = $this->userService->search($search);
        return response()->json(['user' => $userSearch], 200);
    }

    public function filterUsersMonth(): JsonResponse
    {
        $usersFilter = $this->userService->filterUsersMonth();
        return response()->json(['users' => $usersFilter], 200);
    }
    public function filterDataOld(): JsonResponse
    {
        $usersOld = $this->userService->filterDataOld();

        return response()->json(['users' => $usersOld], 200);
    }
    public function filterDataRecent(): JsonResponse
    {
        $usersRecent = $this->userService->filterDataRecent();
        return response()->json(['users' => $usersRecent], 200);
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
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $forgot = $this->userService->forgotPassword($request->email);
        if ($forgot) {
            return response()->json(
                ['message' =>
                'Password reset link sent successfully.'],
                200
            );
        }
        return response()->json(['message' => 'Unable to send password reset link.'], 400);
    }
    public function resetPassword(Request $request): JsonResponse
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
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = $this->userService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );
        if($status){
            return response()->json(['message' => 'Password has been reset successfully.'], 200);
        }
        return response()->json(['message' => 'Invalid token or password reset failed.'], 400);
    }
}
