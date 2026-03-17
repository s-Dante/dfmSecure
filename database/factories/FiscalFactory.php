<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Fiscal;
use App\Enums\FiscalTypeEnum;
use App\Enums\TaxRegimeEnum;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fiscal>
 */
class FiscalFactory extends Factory
{
    protected $model = Fiscal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fiscalType = fake()->randomElement(FiscalTypeEnum::values());
        $taxRegime = fake()->randomElement(TaxRegimeEnum::values());

        return [
            'rfc' => $this->generateRFC($fiscalType),
            'fiscal_type' => $fiscalType,
            'company_name' => $fiscalType === FiscalTypeEnum::LEGAL_PERSON->value
                ? fake()->company()
                : null,
            'tax_regime' => $taxRegime,
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }

    private function generateRFC(string $fiscalType): string
    {
        $letters = fn(int $n) => strtoupper(fake()->lexify(str_repeat('?', $n)));
        $date = fake()->dateTimeBetween('-70 years', '-18 years')->format('ymd');
        $homo = strtoupper(fake()->bothify('###'));

        $prefix = $fiscalType === FiscalTypeEnum::NATURAL_PERSON->value
            ? $letters(4)
            : $letters(3);

        return $prefix . $date . $homo;
    }

    public function naturalPerson(): static
    {
        return $this->state(fn(array $attributes) => [
            'fiscal_type' => FiscalTypeEnum::NATURAL_PERSON->value,
            'company_name' => null,
        ]);
    }

    public function legalPerson(): static
    {
        return $this->state(fn(array $attributes) => [
            'fiscal_type' => FiscalTypeEnum::LEGAL_PERSON->value,
            'company_name' => fake()->company(),
        ]);
    }
}
