<?php

namespace Database\Factories;

use App\Enums\FiscalTypeEnum;
use App\Enums\TaxRegimeEnum;
use App\Models\Fiscal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fiscal>
 */
class FiscalFactory extends Factory
{
    protected $model = Fiscal::class;

    /**
     * Generate a fake but format-valid Mexican RFC.
     * Format: 4 letters + 6 digits (date YYMMDD) + 3 alphanumeric homoclave
     */
    private function generateRfc(string $fiscalType): string
    {
        $letters = fn(int $n) => strtoupper(fake()->lexify(str_repeat('?', $n)));
        $date    = fake()->dateTimeBetween('-60 years', '-18 years')->format('ymd');
        $homo    = strtoupper(fake()->bothify('###'));

        // Persona Física: 4 letters + 6 digits + 3 homoclave (13 chars)
        // Persona Moral:  3 letters + 6 digits + 3 homoclave (12 chars)
        $prefix = $fiscalType === FiscalTypeEnum::NATURAL_PERSON->value
            ? $letters(4)
            : $letters(3);

        return $prefix . $date . $homo;
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fiscalType = fake()->randomElement(FiscalTypeEnum::values());
        $taxRegimes = TaxRegimeEnum::values();

        return [
            'rfc'          => $this->generateRfc($fiscalType),
            'fiscal_type'  => $fiscalType,
            'company_name' => $fiscalType === FiscalTypeEnum::LEGAL_PERSON->value
                ? fake()->company()
                : null,
            'tax_regime'   => fake()->randomElement($taxRegimes),
            'user_id'      => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }

    /**
     * State for natural persons (Persona Física).
     */
    public function naturalPerson(): static
    {
        return $this->state(fn(array $attributes) => [
            'fiscal_type'  => FiscalTypeEnum::NATURAL_PERSON->value,
            'company_name' => null,
        ]);
    }

    /**
     * State for legal persons (Persona Moral).
     */
    public function legalPerson(): static
    {
        return $this->state(fn(array $attributes) => [
            'fiscal_type'  => FiscalTypeEnum::LEGAL_PERSON->value,
            'company_name' => fake()->company(),
        ]);
    }
}

