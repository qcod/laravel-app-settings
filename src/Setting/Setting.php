<?php

namespace QCod\AppSettings\Setting;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = ['updated_at', 'id'];

    protected $table = 'settings';
}
