<?php

namespace QCod\AppSettings\Tests\Mutators;

class AppMakerMutator
{
    public function handle($value, $key)
    {
        return 'class-mutated-'.$value;
    }
}
