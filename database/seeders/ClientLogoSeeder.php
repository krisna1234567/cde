<?php

namespace Database\Seeders;

use App\Models\ClientLogo;
use Illuminate\Database\Seeder;

class ClientLogoSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            ['name' => 'Heinz ABC', 'logo_path' => 'images/clients/heinz-abc.svg'],
            ['name' => 'SuryaTech', 'logo_path' => 'images/clients/suryatech.svg'],
            ['name' => 'Polaris', 'logo_path' => 'images/clients/polaris.svg'],
            ['name' => 'BumiEnergi', 'logo_path' => 'images/clients/bumienergi.svg'],
            ['name' => 'AstraNova', 'logo_path' => 'images/clients/astranova.svg'],
            ['name' => 'Cleo', 'logo_path' => 'images/clients/cleo.svg'],
            ['name' => 'Kirana', 'logo_path' => 'images/clients/kirana.svg'],
            ['name' => 'Lumen', 'logo_path' => 'images/clients/lumen.svg'],
            ['name' => 'Nusantara', 'logo_path' => 'images/clients/nusantara.svg'],
        ];

        foreach ($clients as $index => $client) {
            ClientLogo::query()->updateOrCreate(
                ['name' => $client['name']],
                array_merge($client, [
                    'logo_alt' => $client['name'].' logo',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ])
            );
        }
    }
}
