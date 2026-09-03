<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['id', 'id_user', 'title', 'amount_invested', 'current_amount'])]
class Investiment extends Model
{
    //
}
