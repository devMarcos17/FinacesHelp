<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserService
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
        $user = User::where('id', $idUser)->firstOrFail();

        $user->delete();

        return $user;
    }
    public function disableUser(int $idUser): User
    {
        $user = User::where('id', $idUser)->where('status', 'ativo')
            ->firstOrFail();

        $user->status = 'inativo';
        $user->save();

        return $user;
    }
    public function activeUser(int $idUser): User
    {
        $user = User::where('id', $idUser)->where('status', 'inativo')
            ->firstOrFail();

        $user->status = 'ativo';
        $user->save();

        return $user;
    }
    public function getActiveUsers(): Collection
    {
        $users = User::where('status', 'ativo')->orderBy('name', 'asc')->get();
        return $users;
    }
    public function getDisableUsers(): Collection
    {
        $users = User::where('status', 'inativo')->orderBy('name', 'asc')->get();
        return $users;
    }
    public function getAdminsTotal(): int
    {
        $user = User::where('role', 'admin')->count();
        return $user;
    }
    public function getUsersTotal(): int
    {
        $usersTotal = User::count('users');
        return $usersTotal;
    }
    public function getDisableUsersTotal()
    {
        $userDisable = User::where('status', 'inativo')->count();
        return $userDisable;
    }
    public function getActiveUsersTotal(): int
    {
        $userActive = User::where('status', 'ativo')->count();
        return $userActive;
    }
    public function search(string $search): Collection|bool
    {
        $user = User::whereAny([
            'name',
            'email',
            'status',
            'id'
        ], 'LIKE', "%{$search}%")->get();

        return $user;
    }
    public  function filterUsersMonth(): Collection|bool
    {
        $users = User::where(
            'created_at',
            '>=',
            Carbon::now()->subDays(30)
        )
            ->get();

        if ($users->isEmpty()) {
            return false;
        }
        return $users;
    }
    public function filterDataOld(): Collection
    {
        $users = User::orderBy('created_at', 'ASC')->get();
        return $users;
    }
    public function filterDataRecent(): Collection
    {
        $users = User::orderBy('crated_at', 'DESC')->get();
        return $users;
    }
    public function forgotPassword(string $email): bool
    {
        $status = Password::sendResetLink(['email' => $email]);

        return $status === Password::RESET_LINK_SENT;
    }
    public function resetPassword(string $email, string $token, string $password): bool
    {
        $status = Password::reset([
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password
        ], function (User $user, string $password){
            $user->forceFill(['password' => Hash::make($password)])->setRememberToken(Str::random(60));
            $user->save();
        });
        if ($status == Password::PASSWORD_RESET){
            return true;
        }
        return false;
    }

}
