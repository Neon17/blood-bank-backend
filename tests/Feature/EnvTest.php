<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EnvTest extends TestCase
{
    public function test_database_connection()
    {
        dump(env('DB_DATABASE'));
    }
}