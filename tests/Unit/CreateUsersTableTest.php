<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CreateUsersTableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the users table has the correct columns.
     *
     * @return void
     */
    public function test_users_table_columns()
    {
        $this->assertTrue(Schema::hasTable('users') , 'User table does\'t exist.');

        $expectedColumns = [
            'id',
            'prefixname',
            'firstname',
            'middlename',
            'lastname',
            'username',
            'email',
            'email_verified_at',
            'password',
            'photo',
            'remember_token',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        foreach ($expectedColumns as $column){
            $this->assertTrue(
                Schema::hasColumn('users', $column),
                "The users table is missing the {$column} column."
            );
        }
    }
}
