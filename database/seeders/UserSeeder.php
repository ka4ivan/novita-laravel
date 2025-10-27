<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Client',
                'email' => 'client@app.com',
                'password' => 'password',
                'email_verified_at' => now(),
                'registered_at' => now(),
            ],
        ];

        $table = [];
        foreach ($users as $data) {
            /** @var User $user */
            $user = User::firstOrCreate(Arr::only($data, 'email'), $data);

            $table[] = [$user->id, $user->email, 'password'];
        }

        $this->command->getOutput()->newLine();
        $this->command->alert('Users');
        $this->command->table(['#', 'Email', 'Password',], $table);
        $this->command->getOutput()->newLine(2);
    }
}
