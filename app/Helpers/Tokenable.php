<?php

namespace App\Helpers;

use Illuminate\Support\Str;

Trait Tokenable
{
    public function generateAndSaveApiToken()
    {
        $token = Str::random(60);

        $this->api_token = $token;
        $this->save();

        return $this;
    }
}
