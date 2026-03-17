<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Sinister;
use App\Models\User;
use App\Models\Policy;
use App\Models\Role;
use App\Models\SinisterComment;
use App\Enums\RoleEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SinisterComment>
 */
class SinisterCommentFactory extends Factory
{
    protected $model = SinisterComment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sinister = Sinister::inRandomOrder()->first() ?? Sinister::factory()->create();

        $policy = Policy::find($sinister->policy_id);
        $insuredId = $policy->insured_id;
        $adjusterId = $sinister->adjuster_id;

        $supervisorRoleId = Role::where('name', RoleEnum::SUPERVISOR->value)->value('id');
        $supervisorId = User::where('role_id', $supervisorRoleId)->inRandomOrder()->value('id');

        $possibleUsers = array_filter([
            $insuredId,
            $adjusterId,
            $supervisorId
        ]);
        $userId = fake()->randomElement($possibleUsers);

        return [
            'comment' => fake()->paragraph(),
            'sinister_id' => $sinister->id,
            'user_id' => $userId,
        ];
    }
}
