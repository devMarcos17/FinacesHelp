<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['id', 'id_user', 'type', 'amount', 'category'])]

class Transaction extends Model
{
    //
}
