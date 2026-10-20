<?php

namespace App\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Http\Response;


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
    public function disableUser(int $idUser): User
    {
        $user = User::where('id', $idUser)
        ->where('status', 'ativo')
        ->firstOrFail();
        
        $user->status = 'inativo';
        $user->save();

        return $user;
    }
    public function activeUser(int $idUser): User
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
    public function search(string $search): Collection
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
    public function resetPassword(string $email, string $token, string $password): bool
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
    public function createPdfUserTotal(): Response
    {
        $date = Carbon::now()->format('d-m-y');

        $user = User::select('id', 'name', 'email', 'phone', 'status', 'role', 'created_at')
        ->orderBy('id', 'ASC')
        ->get();

        $pdf = Pdf::loadView('pdf.pdfDownloadUserTotal', compact(
            'user'
        ));
        
        return $pdf->download('pdf-listagem-usuario' . $date . '.pdf');

    }
    public function createPdfUserActive(): Response
    {
        $date = Carbon::now()->format('d-m-y');
        
        $user = User::select('id', 'name', 'email', 'phone', 'status', 'role', 'created_at')
        ->where('status', 'ativo')
        ->orderBy('id', 'ASC')
        ->get();

        $pdf = Pdf::loadView('pdf.pdfDownloadUserActive',compact(
            'user'
        ));

        return $pdf->download('pdf-listagem-usuario-ativo' . $date . '.pdf');
    }
    public function createPdfUserDisable()
    {
        $date = Carbon::now()->format('d-m-y');

        $user = User::select('name', 'email', 'phone', 'status', 'role')
        ->where('status', 'inativo')
        ->where('role', 'user')
        ->orderBy('id', 'ASC')
        ->get();

        $pdf = Pdf::loadView('pdf.pdfDownloadUserDisable', compact(
            'user'
        ));

        return $pdf->download('pdf-listagem-usuario-inativo' . $date . '.pdf');
    }
}
