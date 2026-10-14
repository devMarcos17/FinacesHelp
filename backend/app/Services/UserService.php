<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class UserService
{
    public function registerUser(array $data): User
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->cpf = $data['cpf'];
        $user->phone = $data['phone'];
        $user->date_of_birt = $data['date_of_birt'];

        $user->status = 'ativo';
        $user->role = 'user';

        $user->save();

        return $user;
    }

    public function updateUser(int $idUser, array $data): User
    {
        $user = User::findOrFail($idUser);

        $user->update($data);

        return $user;
    }
    public function deleteUser(int $idUser): User
    {
        $user = User::findOrFail($idUser);

        $user->delete();

        return $user;
    }
    public function disableUser(int $idUser)
    {
        $user = User::where('id', $idUser)
        ->where('status', 'ativo')
        ->firstOrFail();
        
        $user->status = 'inativo';
        $user->save();

        return $user;
    }
    public function activeUser(int $idUser)
    {
        $user = User::where('id', $idUser)
        ->where('status', 'inativo')
        ->firstOrFail();

        $user->status = 'ativo';

        $user->save();
        return $user;
    }
    public function getActiveUser(): Collection
    {
        $user = User::where('status', 'ativo')
        ->orderBy('id', 'asc')->get();

        return $user;
    }
    public function getDisablesUser(): Collection
    {
        $user = User::where('status', 'inativo')
        ->orderBy('id', 'asc')->get();

        return $user;
    }
    public function getUsersTotal(): int
    {
        $user = User::count();
        return $user;
    }
    public function getAdminTotal(): int
    {
        $user = User::where('role', 'admin')->count();
        return $user;
    }
    public function getDisableUserTotal(): int
    {
        $user = User::where('status', 'inativo')->count();
        return $user;
    }
    public function getActiveUserTotal(): int
    {
        $user = User::where('status', 'ativo')->count();
        return $user;
    }
    public function search(string $search)
    {
        $search = User::whereAny([
            'id',
            'name',
            'email',
        ], 'LIKE', "%{$search}%")->get();

        return $search;
    }
    public function filterUsersMonth(): Collection|bool
    {
       $user = User::where('created_at', '>=', Carbon::now()->subDays(30))
    ->get();
        if($user->isEmpty()){
            return false;
        }
        return $user;
    }
    public function filterDataOld(): Collection
    {
        $user = User::orderBy('created_at', 'ASC')->get();
        return $user;
    }
    public function filterDataRecent(): Collection
    {
        $user = User::OrderBy('created_at', 'DESC')->get();
        return $user;
    }
    public function forgotPassword(string $email): bool
    {
        $status = Password::sendResetLink([
            'email' => $email
        ]);

        return $status === Password::RESET_LINK_SENT;
    }
    public function resetPassword(string $email, string $token, string $password)
    {
        $status = Password::reset([
            'email' => $email,
            'token' => $token,
            'password' => $password
        ], function(User $user, string $password){
            $user->forceFill(['password' => Hash::make($password)])
            ->setRememberToken(Str::random(60));
        });

        return $status === Password::PASSWORD_RESET;
        
    }
}
