<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['id', 'id_user', 'title', 'description', 'target_year', 'target_date', 'status', 'image_path'])]
class Goal extends Model
{
    
}
