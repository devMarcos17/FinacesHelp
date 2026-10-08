<?php

namespace App\Services;

use App\Models\Goal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class GoalService
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
    public function createGoal(array $data, ?UploadedFile $file = null): Goal
    {
        $goal = new Goal();
        $goal->id_user = $this->getUserId();
        $goal->title = $data['title'];
        $goal->description = $data['description'] ?? null;
        $goal->target_year = $data['target_year'];
        $goal->target_date = $data['target_date'] ?? null;
        $goal->target_amount = $data['target_amount'];
        $goal->current_amount = 0;
        $goal->status = 'in_progress';

        if ($file && $file->isValid()) {

            $extension = $file->getClientOriginalExtension();

            $imageName = md5($file->getClientOriginalName()) . '_' . time() . '.' . $extension;

            $file->move(public_path('img/goals'), $imageName);

            $goal->image_path = $imageName;
        }
        $goal->save();

        return $goal;
    }
    public function updateGoal(array $data, ?UploadedFile $file = null, int $idGoal)
    {
        $goal = Goal::where('id', $idGoal)
            ->where('id_user', $this->getUserId())
            ->firstOrFail();

        if ($file && $file->isValid()) {
            $extension = $file->getClientOriginalExtension();

            $imageName = md5($file->getClientOriginalName()) . '_' . time() . '.' . $extension;

            $file->move(public_path('img/goals'), $imageName);

            $data['image_path'] = $imageName;
        }
        $goal->update($data);

        return $goal;
    }
    public function listGoalUser(): Collection
    {
        $goal = Goal::where('id_user', $this->getUserId())->get();
        return $goal;
    }
    public function deleteGoal(int $idGoal): Collection
    {
        $goal = Goal::where('id', $idGoal)
            ->where('id_user', $this->getUserId())
            ->firstOrFail();

        $goal->delete();
        return $goal;
    }
    public function deposit(int $idGoal, float $deposit): Goal|bool
    {
        $goal = Goal::where('id', $idGoal)
            ->where('id_user', $this->getUserId())
            ->firstOrFail();

        $currentAmount =  $goal->current_amount;
        $depositAmount = $deposit;
        $targetAmount = $goal->target_amount;

        $newAmount = $currentAmount + $depositAmount;
        $goal->current_amount = $newAmount;

        if ($newAmount >= $targetAmount) {
            $goal->status = 'completed';
        }

        $goal->save();

        return $goal;
    }
    public function withDraw(int $idGoal, float $devolutionAmount): Goal|bool
    {
        $goal = Goal::where('id', $idGoal)
            ->where('id_user', $this->getUserId())
            ->firstOrFail();

        $currentAmount = $goal->current_amount;
        $withdrawAmount = $devolutionAmount;
        $targetAmount = $goal->target_amount;

        if ($withdrawAmount > $currentAmount){
            return false;
        }

        $newAmount = $currentAmount - $withdrawAmount;
        $goal->current_amount = $newAmount;

        if($newAmount < $targetAmount){
            $goal->status = 'in_progress';
        }

        $goal->save();

        return $goal;
    }
    public function progress(int $idGoal): float|int
    {
        $goal = Goal::where('id', $idGoal)->where('id_user', $this->getUserId())
        ->firstOrFail();

        if($goal->current_amount <= 0 || $goal->target_amount <= 0) {
        return 0;
    }

        $percentage = min(100, ($goal->current_amount / $goal->target_amount) * 100);

        return round($percentage, 2);
    }
    public function listGoalCompleted(): Collection
    {
        $goal = Goal::where('id_user', $this->getUserId())
        ->where('status', 'completed')
        ->get();

        return $goal;
    }
    public function listGoalCancelled(): Collection
    {
        $goal = Goal::where('id_user', $this->getUserId())
        ->where('status', 'cancelled')
        ->get();

        return $goal;
    }
}
