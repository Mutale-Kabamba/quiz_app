<?php

namespace Database\Seeders;

use App\Models\Deanery;
use App\Models\Parish;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DioceseParishesAndDeaneriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deaneriesData = [
            [
                'name' => 'Livingstone Deanery',
                'code' => 'LIV',
                'parishes' => [
                    [
                        'name' => 'St. Francis of Assisi Parish (Dambwa)',
                        'code' => 'SFA',
                        'location' => 'Dambwa, Livingstone',
                    ],
                    [
                        'name' => 'Christ the King Parish (Maramba)',
                        'code' => 'CKM',
                        'location' => 'Maramba, Livingstone',
                    ],
                    [
                        'name' => 'St. Peter the Apostle Parish (Airport)',
                        'code' => 'SPA',
                        'location' => 'Airport, Livingstone',
                    ],
                    [
                        'name' => 'St. Theresa Cathedral Parish',
                        'code' => 'STC',
                        'location' => 'Livingstone Town',
                    ],
                    [
                        'name' => "St. Paul's Parish (Ngwenya)",
                        'code' => 'SPN',
                        'location' => 'Ngwenya, Livingstone',
                    ],
                    [
                        'name' => 'Maria Regina Parish (Linda)',
                        'code' => 'MRL',
                        'location' => 'Linda, Livingstone',
                    ],
                    [
                        'name' => "St. Joseph's Parish (Mukuni)",
                        'code' => 'SJM',
                        'location' => 'Mukuni',
                    ],
                    [
                        'name' => 'Our Lady of Angels',
                        'code' => 'OLA',
                        'location' => 'Livingstone',
                    ],
                    [
                        'name' => 'St. Stephen the Martyr Parish (Kazungula)',
                        'code' => 'SSK',
                        'location' => 'Kazungula',
                    ],
                    [
                        'name' => 'Holy Childhood Parish (Makunka)',
                        'code' => 'HCM',
                        'location' => 'Makunka',
                    ],
                ],
            ],
            [
                'name' => 'Sesheke Deanery',
                'code' => 'SES',
                'parishes' => [
                    [
                        'name' => "St. Fidelis' Parish (Sichili)",
                        'code' => 'SFS',
                        'location' => 'Sichili',
                    ],
                    [
                        'name' => "St. Kizito's Parish (Sesheke)",
                        'code' => 'SKS',
                        'location' => 'Sesheke',
                    ],
                    [
                        'name' => "St. Paul's (Nawinda) Parish",
                        'code' => 'SPW',
                        'location' => 'Nawinda',
                    ],
                    [
                        'name' => "St. Mary's Parish (Njoko)",
                        'code' => 'SMN',
                        'location' => 'Njoko',
                    ],
                    [
                        'name' => "St. Arnold Janssen's Parish (Mwandi)",
                        'code' => 'SAJ',
                        'location' => 'Mwandi',
                    ],
                ],
            ],
            [
                'name' => 'Sioma Deanery',
                'code' => 'SIO',
                'parishes' => [
                    [
                        'name' => 'St. Joseph the Worker Parish (Lusu)',
                        'code' => 'SJL',
                        'location' => 'Lusu',
                    ],
                    [
                        'name' => 'St. Anthony of Padua Parish (Sioma)',
                        'code' => 'SAP',
                        'location' => 'Sioma',
                    ],
                    [
                        'name' => 'St. Leopold Parish (Shangombo)',
                        'code' => 'SLS',
                        'location' => 'Shangombo',
                    ],
                ],
            ],
        ];

        foreach ($deaneriesData as $deaneryInfo) {
            $deanery = Deanery::firstOrCreate(
                ['code' => $deaneryInfo['code']],
                ['name' => $deaneryInfo['name']]
            );

            // Ensure name matches latest
            if ($deanery->name !== $deaneryInfo['name']) {
                $deanery->update(['name' => $deaneryInfo['name']]);
            }

            foreach ($deaneryInfo['parishes'] as $parishInfo) {
                Parish::updateOrCreate(
                    [
                        'name' => $parishInfo['name'],
                    ],
                    [
                        'deanery_id' => $deanery->id,
                        'code' => $parishInfo['code'] ?? Str::slug($parishInfo['name']),
                        'location' => $parishInfo['location'] ?? null,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
