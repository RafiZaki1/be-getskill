<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contact::create([
            'whatsapp' => '08123456789',
            'twitter' => 'hummatech.telegram',
            'facebook' => 'url hummasoft',
            'email' => 'info@hummatech.com',
            'phone_number' => '0987654321',
            'description' => 'when an unknown printer took galley of type and scrambled it to make pspecimen bookt has.'
        ]);
    }
}
