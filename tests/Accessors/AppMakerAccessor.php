<?php

namespace QCod\AppSettings\Tests\Accessors;

class AppMakerAccessor
{
    public function handle($value, $key)
    {
        return 'class-accessed-'.$value;
    }
}
