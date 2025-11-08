<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = [
            [
                'code' => '20104001',
                'name' => 'SMA Negeri 1 Banda Aceh',
                'address' => 'Jl. Sultan Alauddin No. 1, Banda Aceh',
                'province' => 'Aceh',
                'district' => 'Banda Aceh',
                'metadata' => [
                    'description' => 'Sekolah menengah atas negeri unggulan di Banda Aceh',
                    'established_year' => 1963,
                    'accreditation' => 'A'
                ]
            ],
            [
                'code' => '20104002',
                'name' => 'SMA Negeri 2 Banda Aceh',
                'address' => 'Jl. T. Panglima Polem No. 2, Banda Aceh',
                'province' => 'Aceh',
                'district' => 'Banda Aceh',
                'metadata' => [
                    'description' => 'Sekolah menengah atas negeri dengan program unggulan',
                    'established_year' => 1975,
                    'accreditation' => 'A'
                ]
            ],
            [
                'code' => '10101001',
                'name' => 'SMA Negeri 1 Jakarta Pusat',
                'address' => 'Jl. Budi Kemuliaan No. 1, Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'district' => 'Jakarta Pusat',
                'metadata' => [
                    'description' => 'Sekolah menengah atas negeri di pusat Jakarta',
                    'established_year' => 1950,
                    'accreditation' => 'A'
                ]
            ],
            [
                'code' => '33010001',
                'name' => 'SMA Negeri 1 Semarang',
                'address' => 'Jl. Taman Sari No. 1, Semarang',
                'province' => 'Jawa Tengah',
                'district' => 'Semarang',
                'metadata' => [
                    'description' => 'Sekolah menengah atas negeri favorit di Semarang',
                    'established_year' => 1952,
                    'accreditation' => 'A'
                ]
            ],
            [
                'code' => '35010001',
                'name' => 'SMA Negeri 1 Surabaya',
                'address' => 'Jl. Wijaya Kusuma No. 1, Surabaya',
                'province' => 'Jawa Timur',
                'district' => 'Surabaya',
                'metadata' => [
                    'description' => 'Sekolah menengah atas negeri terbaik di Surabaya',
                    'established_year' => 1947,
                    'accreditation' => 'A'
                ]
            ]
        ];

        foreach ($schools as $schoolData) {
            School::create($schoolData);
        }
    }
}
