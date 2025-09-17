<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EnvTest extends TestCase
{
    public function test_database_connection()
    {
        $database = env('DB_DATABASE');
        dump(env('DB_DATABASE'));

        $this->assertNotNull($database);
    }
}