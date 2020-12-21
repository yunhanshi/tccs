<?php

use App\Models\User\User;
use App\Models\Auth\Role;
use App\Models\Todo\Todo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'yunhanshi33@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        $adminRole = Role::findByName(\App\Models\Auth\Acl::ROLE_ADMIN);

        $admin->syncRoles($adminRole);

        for($i=0; $i<50; $i++) {
            Todo::create([
                'user_id' => 1,
                'task' => 'task-' . ($i +1),
                'description' => 'description-' . ($i +1),
                'finished_at' => Null,
            ]);
        }
    }
}
