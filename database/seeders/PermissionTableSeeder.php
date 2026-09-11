<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('permissions')->delete();
        \DB::table('permissions')->insert(array(
            0   => ['id' => '1', 'name' => 'dashboard', 'group_name' => 'dashboard', 'guard_name' => 'web','created_at' => '2025-08-08 05:32:38','updated_at' => '2025-08-08 05:32:38'],
            1   => ['id' => '2', 'name' => 'roles', 'group_name' => 'role', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            2   => ['id' => '3', 'name' => 'add_roles', 'group_name' => 'role', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            3   => ['id' => '4', 'name' => 'edit_roles', 'group_name' => 'role', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            4   => ['id' => '5', 'name' => 'permissions', 'group_name' => 'permission', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            5   => ['id' => '6', 'name' => 'add_permissions', 'group_name' => 'permission', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            6   => ['id' => '7', 'name' => 'edit_permissions', 'group_name' => 'permission', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            7   => ['id' => '8', 'name' => 'role_has_permission', 'group_name' => 'role&permission', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            8   => ['id' => '9', 'name' => 'edit_role_has_permission', 'group_name' => 'role&permission', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            9   => ['id' => '10', 'name' => 'country', 'group_name' => 'country', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            10  => ['id' => '11', 'name' => 'add_country', 'group_name' => 'country', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            11  => ['id' => '12', 'name' => 'edit_country', 'group_name' => 'country', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            12  => ['id' => '13', 'name' => 'state', 'group_name' => 'state', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            13  => ['id' => '14', 'name' => 'add_state', 'group_name' => 'state', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            14  => ['id' => '15', 'name' => 'edit_state', 'group_name' => 'state', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            15  => ['id' => '16', 'name' => 'city', 'group_name' => 'city', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            16  => ['id' => '17', 'name' => 'add_city', 'group_name' => 'city', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            17  => ['id' => '18', 'name' => 'edit_city', 'group_name' => 'city', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            18  => ['id' => '19', 'name' => 'auto_enquiry_list', 'group_name' => 'auto_enquiry', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            19  => ['id' => '20', 'name' => 'auto_enquiry_update', 'group_name' => 'auto_enquiry', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            20  => ['id' => '21', 'name' => 'auto_emergency_list', 'group_name' => 'auto_emergency', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            21  => ['id' => '22', 'name' => 'auto_emergency_update', 'group_name' => 'auto_emergency', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            22  => ['id' => '23', 'name' => 'solid_autos_list', 'group_name' => 'solid_autos', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            23  => ['id' => '24', 'name' => 'auto_driver_list', 'group_name' => 'auto_driver', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            24  => ['id' => '25', 'name' => 'auto_driver_approved', 'group_name' => 'auto_driver', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            25  => ['id' => '26', 'name' => 'auto_driver_delete', 'group_name' => 'auto_driver', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            26  => ['id' => '27', 'name' => 'auto_brand', 'group_name' => 'auto_brand', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            27  => ['id' => '28', 'name' => 'add_auto_brand', 'group_name' => 'auto_brand', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            28  => ['id' => '29', 'name' => 'edit_auto_brand', 'group_name' => 'auto_brand', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            29  => ['id' => '30', 'name' => 'delete_auto_brand', 'group_name' => 'auto_brand', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            30  => ['id' => '31', 'name' => 'auto_brand_model', 'group_name' => 'auto_brand_model', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            31  => ['id' => '32', 'name' => 'add_auto_brand_model', 'group_name' => 'auto_brand_model', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            32  => ['id' => '33', 'name' => 'edit_auto_brand_model', 'group_name' => 'auto_brand_model', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            33  => ['id' => '34', 'name' => 'delete_auto_brand_model', 'group_name' => 'auto_brand_model', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            34  => ['id' => '35', 'name' => 'auto_fuel_type', 'group_name' => 'auto_fuel_type', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            35  => ['id' => '36', 'name' => 'add_auto_fuel_type', 'group_name' => 'auto_fuel_type', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            36  => ['id' => '37', 'name' => 'edit_auto_fuel_type', 'group_name' => 'auto_fuel_type', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            37  => ['id' => '38', 'name' => 'delete_auto_fuel_type', 'group_name' => 'auto_fuel_type', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            38  => ['id' => '39', 'name' => 'auto_seller', 'group_name' => 'auto_seller', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            39  => ['id' => '40', 'name' => 'add_auto_seller', 'group_name' => 'auto_seller', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            40  => ['id' => '41', 'name' => 'edit_auto_seller', 'group_name' => 'auto_seller', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            41  => ['id' => '42', 'name' => 'delete_auto_seller', 'group_name' => 'auto_seller', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            42  => ['id' => '43', 'name' => 'auto_finance', 'group_name' => 'auto_finance', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            43  => ['id' => '44', 'name' => 'add_auto_finance', 'group_name' => 'auto_finance', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            44  => ['id' => '45', 'name' => 'edit_auto_finance', 'group_name' => 'auto_finance', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            45  => ['id' => '46', 'name' => 'delete_auto_finance', 'group_name' => 'auto_finance', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            46  => ['id' => '47', 'name' => 'gas_station', 'group_name' => 'gas_station', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            47  => ['id' => '48', 'name' => 'add_gas_station', 'group_name' => 'gas_station', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            48  => ['id' => '49', 'name' => 'edit_gas_station', 'group_name' => 'gas_station', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            49  => ['id' => '50', 'name' => 'delete_gas_station', 'group_name' => 'gas_station', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            50  => ['id' => '51', 'name' => 'mechanic', 'group_name' => 'mechanic', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            51  => ['id' => '52', 'name' => 'add_mechanic', 'group_name' => 'mechanic', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            52  => ['id' => '53', 'name' => 'edit_mechanic', 'group_name' => 'mechanic', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            53  => ['id' => '54', 'name' => 'delete_mechanic', 'group_name' => 'mechanic', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            54  => ['id' => '55', 'name' => 'insurance', 'group_name' => 'insurance', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            55  => ['id' => '56', 'name' => 're_finance', 'group_name' => 're_finance', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            56  => ['id' => '57', 'name' => 'rto', 'group_name' => 'rto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            57  => ['id' => '58', 'name' => 'user_list', 'group_name' => 'user management', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            58  => ['id' => '59', 'name' => 'user_info', 'group_name' => 'user management', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            59  => ['id' => '60', 'name' => 'used_auto', 'group_name' => 'used auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            60  => ['id' => '61', 'name' => 'add_used_auto', 'group_name' => 'used auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            61  => ['id' => '62', 'name' => 'edit_used_auto', 'group_name' => 'used auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            62  => ['id' => '63', 'name' => 'sell_used_auto', 'group_name' => 'used auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            63  => ['id' => '64', 'name' => 'delete_used_auto', 'group_name' => 'used auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            64  => ['id' => '65', 'name' => 'view_details_used_auto', 'group_name' => 'used auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            65  => ['id' => '66', 'name' => 'private_cargo_auto', 'group_name' => 'private cargo auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            66  => ['id' => '67', 'name' => 'add_private_cargo_auto', 'group_name' => 'private cargo auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            67  => ['id' => '68', 'name' => 'edit_private_cargo_auto', 'group_name' => 'private cargo auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            68  => ['id' => '69', 'name' => 'sell_private_cargo_auto', 'group_name' => 'private cargo auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            69  => ['id' => '70', 'name' => 'delete_private_cargo_auto', 'group_name' => 'private cargo auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            70  => ['id' => '71', 'name' => 'view_details_private_cargo_auto', 'group_name' => 'private cargo auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            71  => ['id' => '72', 'name' => 'bajaj_refinance_auto', 'group_name' => 'bajaj refinance auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            72  => ['id' => '73', 'name' => 'add_bajaj_refinance_auto', 'group_name' => 'bajaj refinance auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            73  => ['id' => '74', 'name' => 'edit_bajaj_refinance_auto', 'group_name' => 'bajaj refinance auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            74  => ['id' => '75', 'name' => 'sell_bajaj_refinance_auto', 'group_name' => 'bajaj refinance auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            75  => ['id' => '76', 'name' => 'delete_bajaj_refinance_auto', 'group_name' => 'bajaj refinance auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            76  => ['id' => '77', 'name' => 'view_details_bajaj_refinance_auto', 'group_name' => 'bajaj refinance auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            77  => ['id' => '78', 'name' => 'new_auto', 'group_name' => 'new auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            78  => ['id' => '79', 'name' => 'add_new_auto', 'group_name' => 'new auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            79  => ['id' => '80', 'name' => 'edit_new_auto', 'group_name' => 'new auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            80  => ['id' => '81', 'name' => 'view_details_new_auto', 'group_name' => 'new auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            81  => ['id' => '82', 'name' => 'delete_new_auto', 'group_name' => 'new auto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            82  => ['id' => '83', 'name' => 'general_setting', 'group_name' => 'settings', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            83  => ['id' => '84', 'name' => 'smtp_setting', 'group_name' => 'settings', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            84  => ['id' => '85', 'name' => 'emailtemplate_setting', 'group_name' => 'settings', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            85  => ['id' => '86', 'name' => 'payment_setting', 'group_name' => 'settings', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            86  => ['id' => '87', 'name' => 'user_post_auto_list', 'group_name' => 'approval', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            87  => ['id' => '88', 'name' => 'user_post_auto_approval', 'group_name' => 'approval', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            88  => ['id' => '89', 'name' => 're_finance_update', 'group_name' => 're_finance', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            89  => ['id' => '90', 'name' => 'rto_update', 'group_name' => 'rto', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            90  => ['id' => '91', 'name' => 'sparepart_categories', 'group_name' => 'sparepart categories', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            91  => ['id' => '92', 'name' => 'add_sparepart_categories', 'group_name' => 'sparepart categories', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            92  => ['id' => '93', 'name' => 'edit_sparepart_categories', 'group_name' => 'sparepart categories', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            93  => ['id' => '94', 'name' => 'delete_sparepart_categories', 'group_name' => 'sparepart categories', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            94  => ['id' => '95', 'name' => 'sparepart_subcategories', 'group_name' => 'sparepart Subcategories', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            95  => ['id' => '96', 'name' => 'add_sparepart_subcategories', 'group_name' => 'sparepart Subcategories', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            96  => ['id' => '97', 'name' => 'edit_sparepart_subcategories', 'group_name' => 'sparepart Subcategories', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            97  => ['id' => '98', 'name' => 'delete_sparepart_subcategories', 'group_name' => 'sparepart Subcategories', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            98  => ['id' => '99', 'name' => 'sparepart_product', 'group_name' => 'sparepart products', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            99  => ['id' => '100', 'name' => 'add_sparepart_product', 'group_name' => 'sparepart products', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            100  => ['id' => '101', 'name' => 'edit_sparepart_product', 'group_name' => 'sparepart products', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            101  => ['id' => '102', 'name' => 'delete_sparepart_product', 'group_name' => 'sparepart products', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            102  => ['id' => '103', 'name' => 'sparepart_pendingorders', 'group_name' => 'sparepart orders', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            103  => ['id' => '104', 'name' => 'sparepart_successorders', 'group_name' => 'sparepart orders', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            104  => ['id' => '105', 'name' => 'sparepart_cancelorders', 'group_name' => 'sparepart orders', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            105  => ['id' => '106', 'name' => 'quotation', 'group_name' => 'quotation', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            106  => ['id' => '107', 'name' => 'quotation_status_update', 'group_name' => 'quotation', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            107  => ['id' => '108', 'name' => 'authorized_seller', 'group_name' => 'authorized seller', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            108  => ['id' => '109', 'name' => 'add_authorized_seller', 'group_name' => 'authorized seller', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            109  => ['id' => '110', 'name' => 'edit_authorized_seller', 'group_name' => 'authorized seller', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            110  => ['id' => '111', 'name' => 'delete_authorized_seller', 'group_name' => 'authorized seller', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            111  => ['id' => '112', 'name' => 'events_announce', 'group_name' => 'events announce', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            112  => ['id' => '113', 'name' => 'add_events_announce', 'group_name' => 'events announce', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            113  => ['id' => '114', 'name' => 'edit_events_announce', 'group_name' => 'events announce', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            114  => ['id' => '115', 'name' => 'delete_events_announce', 'group_name' => 'events announce', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            115  => ['id' => '116', 'name' => 'upload_gas_station', 'group_name' => 'gas_station', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            116  => ['id' => '117', 'name' => 'sparepart_deleteorders', 'group_name' => 'sparepart orders', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            117  => ['id' => '118', 'name' => 'auto_meter', 'group_name' => 'auto meter', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],

            118 => ['id' => '119', 'name' => 'fairprice_ride_list', 'group_name' => 'fairprice', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
            119 => ['id' => '120', 'name' => 'fairprice_fare_setting', 'group_name' => 'fairprice', 'guard_name' => 'web', 'created_at' => '2023-11-09 05:32:38', 'updated_at' => '2023-11-09 05:32:38'],
        ));
    }
}
