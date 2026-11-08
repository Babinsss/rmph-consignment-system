<?php

namespace Database\Seeders;

use App\Models\Consignor;
use Illuminate\Database\Seeder;

class ConsignorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $consignors = [
            [
                'name' => 'Zuellig Pharma Corporation',
                'code' => 'ZUELLIG',
                'contact_person' => 'Juan Dela Cruz',
                'contact_number' => '+63 33 336 1234',
                'email' => 'consign.zuellig@example.com',
                'address' => 'Iloilo City Distribution Center, Western Visayas',
            ],
            [
                'name' => 'Metro Drug Inc.',
                'code' => 'METRODRUG',
                'contact_person' => 'Maria Santos',
                'contact_number' => '+63 33 337 5678',
                'email' => 'orders.metrodrug@example.com',
                'address' => 'Mandurriao, Iloilo City',
            ],
            [
                'name' => 'Endure Medical, Inc.',
                'code' => 'ENDURE',
                'contact_person' => 'Roberto Tan',
                'contact_number' => '+63 33 320 9012',
                'email' => 'visayas.endure@example.com',
                'address' => 'Jaro, Iloilo City',
            ],
            [
                'name' => 'Phil Pharmawealth, Inc.',
                'code' => 'PHARMAWEALTH',
                'contact_person' => 'Elena Reyes',
                'contact_number' => '+63 33 508 3456',
                'email' => 'sales.pharmawealth@example.com',
                'address' => 'Pavia, Iloilo',
            ],
            [
                'name' => 'Globan Medical Supplies',
                'code' => 'GLOBAN',
                'contact_person' => 'Carlos Gomez',
                'contact_number' => '+63 36 621 7890',
                'email' => 'globan.roxas@example.com',
                'address' => 'Roxas City, Capiz',
            ],
            [
                'name' => 'RBC-MDC Corporation',
                'code' => 'RBCMDC',
                'contact_person' => 'Patricia Lim',
                'contact_number' => '+63 33 329 1122',
                'email' => 'rbcmdc.consign@example.com',
                'address' => 'La Paz, Iloilo City',
            ],
            [
                'name' => 'Healthsolutions Enterprises Inc.',
                'code' => 'HEALTHSOL',
                'contact_person' => 'Antonio Ramos',
                'contact_number' => '+63 33 335 4433',
                'email' => 'hsolutions.orders@example.com',
                'address' => 'City Proper, Iloilo City',
            ],
            [
                'name' => 'Berovan Marketing Inc.',
                'code' => 'BEROVAN',
                'contact_person' => 'Grace Fernandez',
                'contact_number' => '+63 36 620 5566',
                'email' => 'berovan.panay@example.com',
                'address' => 'Arnaldo Boulevard, Roxas City, Capiz',
            ],
            [
                'name' => 'Biomedics Inc.',
                'code' => 'BIOMEDICS',
                'contact_person' => 'Ferdinand Dizon',
                'contact_number' => '+63 33 338 7788',
                'email' => 'biomedics.visayas@example.com',
                'address' => 'Molo, Iloilo City',
            ],
            [
                'name' => 'Dynamic Care Pharma',
                'code' => 'DYNAMICCARE',
                'contact_person' => 'Lourdes Bautista',
                'contact_number' => '+63 33 501 9900',
                'email' => 'dynamic.pharma@example.com',
                'address' => 'Leganes, Iloilo',
            ],
            [
                'name' => 'B. Braun Medical Supplies, Inc.',
                'code' => 'BBRAUN',
                'contact_person' => 'Mark Joseph Villanueva',
                'contact_number' => '+63 33 321 3344',
                'email' => 'bbraun.visayas@example.com',
                'address' => 'Mandurriao, Iloilo City',
            ],
            [
                'name' => 'Terumo Philippines Corporation',
                'code' => 'TERUMO',
                'contact_person' => 'Carmela Ocampo',
                'contact_number' => '+63 33 330 5566',
                'email' => 'terumo.orders@example.com',
                'address' => 'Iloilo Business Park, Iloilo City',
            ],
            [
                'name' => 'Fresenius Medical Care Philippines',
                'code' => 'FRESENIUS',
                'contact_person' => 'Danilo Castro',
                'contact_number' => '+63 36 621 3322',
                'email' => 'fmc.dialysis.panay@example.com',
                'address' => 'Roxas City, Capiz',
            ],
            [
                'name' => 'Abbott Laboratories Philippines',
                'code' => 'ABBOTT',
                'contact_person' => 'Jessica Morales',
                'contact_number' => '+63 33 336 8899',
                'email' => 'abbott.hospital.panay@example.com',
                'address' => 'Iloilo City',
            ],
            [
                'name' => 'United Laboratories, Inc.',
                'code' => 'UNILAB',
                'contact_person' => 'Eduardo Mendoza',
                'contact_number' => '+63 33 337 2211',
                'email' => 'unilab.hospital.sales@example.com',
                'address' => 'Jaro, Iloilo City',
            ],
            [
                'name' => 'Sanofi-Aventis Philippines Inc.',
                'code' => 'SANOFI',
                'contact_person' => 'Regina Soriano',
                'contact_number' => '+63 33 320 6677',
                'email' => 'sanofi.visayas@example.com',
                'address' => 'Mandurriao, Iloilo City',
            ],
        ];

        foreach ($consignors as $data) {
            Consignor::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
