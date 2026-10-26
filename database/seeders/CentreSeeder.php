<?php

namespace Database\Seeders;

use App\Models\Centre;
use App\Models\CentreWallet;
use App\Models\District;
use App\Models\State;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CentreSeeder extends Seeder
{
    public function run(): void
    {
        $centres = [
            [
                'code' => 'DCE-1001',
                'name' => 'TECH ERA COMPUTER - Sheikhpura Main Campus',
                'director' => 'Er. Ranjeet Kumar',
                'email' => 'centre1001@techeracomputer.in',
                'mobile' => '9162384669',
                'state' => 'Bihar',
                'district' => 'Sheikhpura',
                'city' => 'Sheikhpura',
                'address' => 'Sasbahana, Sheikhpura, Bihar - 801105',
                'pincode' => '801105',
                'lab_capacity' => 50,
            ],
            [
                'code' => 'DCE-1002',
                'name' => 'Tech Era IT Institute - Patna Central Branch',
                'director' => 'Prof. Amit Sharma',
                'email' => 'centre1002@techeracomputer.in',
                'mobile' => '9835012345',
                'state' => 'Bihar',
                'district' => 'Patna',
                'city' => 'Patna',
                'address' => '4th Floor, Shanti Complex, Boring Road Crossing',
                'pincode' => '800001',
                'lab_capacity' => 35,
            ],
            [
                'code' => 'DCE-1003',
                'name' => 'Tech Era Vocational Computer Academy - Lucknow City',
                'director' => 'Smt. Vandana Singh',
                'email' => 'centre1003@techeracomputer.in',
                'mobile' => '9415098765',
                'state' => 'Uttar Pradesh',
                'district' => 'Lucknow',
                'city' => 'Lucknow',
                'address' => 'Plot 18, Hazratganj Main Market, Near Metro Station',
                'pincode' => '226001',
                'lab_capacity' => 30,
            ],
            [
                'code' => 'DCE-1004',
                'name' => 'Tech Era Digital Skill Centre - Ranchi Main',
                'director' => 'Sri Manoj Tiwari',
                'email' => 'centre1004@techeracomputer.in',
                'mobile' => '9771234567',
                'state' => 'Jharkhand',
                'district' => 'Ranchi',
                'city' => 'Ranchi',
                'address' => 'Circular Road, Lalpur Chowk, Opposite Plaza Cinema',
                'pincode' => '834001',
                'lab_capacity' => 28,
            ],
            [
                'code' => 'DCE-1005',
                'name' => 'Tech Era InfoTech Hub - New Delhi',
                'director' => 'Dr. Rohit Verma',
                'email' => 'centre1005@techeracomputer.in',
                'mobile' => '9811099887',
                'state' => 'Delhi',
                'district' => 'New Delhi',
                'city' => 'New Delhi',
                'address' => 'Shop 12-14, Commercial Complex, Laxmi Nagar',
                'pincode' => '110092',
                'lab_capacity' => 40,
            ],
        ];

        foreach ($centres as $c) {
            $user = User::firstOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['director'],
                    'mobile' => $c['mobile'],
                    'password' => Hash::make('Centre@12345'),
                    'role' => 'centre',
                    'is_active' => true,
                ]
            );

            $state = State::where('name', $c['state'])->first();
            $district = District::where('state_id', $state?->id)->where('name', 'like', '%' . explode(' ', $c['district'])[0] . '%')->first();

            $centre = Centre::firstOrCreate(
                ['centre_code' => $c['code']],
                [
                    'user_id' => $user->id,
                    'centre_name' => $c['name'],
                    'director_name' => $c['director'],
                    'email' => $c['email'],
                    'mobile' => $c['mobile'],
                    'state_id' => $state ? $state->id : 1,
                    'district_id' => $district ? $district->id : 1,
                    'city' => $c['city'],
                    'address' => $c['address'],
                    'pincode' => $c['pincode'],
                    'lab_capacity' => $c['lab_capacity'],
                    'affiliation_date' => '2023-01-15',
                    'valid_upto' => '2028-01-15',
                    'status' => 'active',
                ]
            );

            CentreWallet::firstOrCreate(
                ['centre_id' => $centre->id],
                [
                    'balance' => 5000.00,
                    'total_credited' => 10000.00,
                    'total_debited' => 5000.00,
                    'status' => 'active',
                ]
            );
        }
    }
}
