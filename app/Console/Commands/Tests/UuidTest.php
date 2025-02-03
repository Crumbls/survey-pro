<?php

namespace App\Console\Commands\Tests;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Services\SchemaService;
use Ramsey\Uuid\Uuid;

class UuidTest extends Command
{
    protected $signature = 'test:uuid';
    protected $description = 'Test our UUID generator.';



    public function handle()
    {
        $temp = $this->generateMultiple(10);
        for($i = 0; $i < 10; $i++){
            $temp[] = $this->generateBusinessName();
        }
        foreach($temp as $name) {
            $this->info($name.' '.static::generateUniqueUuid($name));
        }
    }

    private $firstNames = ['James', 'Mary', 'John', 'Patricia', 'Robert', 'Jennifer', 'Michael', 'Linda', 'William', 'Elizabeth'];
    private $businessTypes = ['Cafe', 'Bakery', 'Restaurant', 'Diner', 'Shop', 'Store', 'Market', 'Boutique', 'Bistro', 'Bar'];
    private $adjectives = ['Global', 'Dynamic', 'Innovative', 'Premier', 'Elite', 'Advanced', 'Modern', 'Professional', 'Strategic', 'Superior'];
    private $nouns = ['Solutions', 'Systems', 'Services', 'Industries', 'Enterprises', 'Group', 'Corporation', 'Associates', 'Partners', 'International'];

    public function generateName(): string {
        if (rand(1, 100) <= 30) { // 30% chance for possessive names
            return $this->generatePossessiveName();
        }
        return $this->generateStandardName();
    }

    private function generatePossessiveName(): string {
        $firstName = $this->firstNames[array_rand($this->firstNames)];
        $businessType = $this->businessTypes[array_rand($this->businessTypes)];
        return "$firstName's $businessType";
    }

    private function generateStandardName(): string {
        if (rand(0, 1)) {
            $adj = $this->adjectives[array_rand($this->adjectives)];
            $noun = $this->nouns[array_rand($this->nouns)];
            return "$adj $noun";
        }
        $parts = [
            $this->adjectives[array_rand($this->adjectives)],
            $this->nouns[array_rand($this->nouns)],
            ['Inc', 'LLC', 'Co', ''][array_rand([0,1,2,3])]
        ];
        return trim(implode(' ', $parts));
    }

    public function generateMultiple(int $count): array {
        $names = [];
        for ($i = 0; $i < $count; $i++) {
            $names[] = $this->generateName();
        }
        return $names;
    }

    protected function generateBusinessName() {
        $faker = fake();
        $types = [
            'standard' => fn() => $faker->company(),
            'possessive' => fn() => $faker->firstName() . "'s " . $faker->words(2, true),
            'llc' => fn() => $faker->company() . ' LLC',
            'inc' => fn() => $faker->company() . ' Inc',
            'corp' => fn() => $faker->company() . ' Corp',
            'ltd' => fn() => $faker->company() . ' Ltd',
            'descriptive' => fn() => $faker->catchPhrase() . ' ' . $faker->companySuffix()
        ];

        $type = array_rand($types);
        return $types[$type]();
    }


    protected static function generateUniqueUuid(string $input): string
    {
        $suffixes = ['plc','llc', 'inc', 'ltd', 'co', 'corp'];

        // Replace suffixes with temporary tokens
        foreach ($suffixes as $suffix) {
            $input = preg_replace('/\b' . $suffix . '\b/i', "____{$suffix}____", $input);
        }

            $uuid = Str::kebab($input);
            $uuid = preg_replace('/[^a-zA-Z0-9-]/', '-', $uuid);
            $uuid = preg_replace('/-+/', '-', $uuid);
            $uuid = str_replace('-s-', 's-', $uuid);
            $uuid = rtrim($uuid, '-');

        return $uuid;

    }
}
