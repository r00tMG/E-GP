<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $permissions = [
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'voir_recipes',
            'ajouter_recipes',
            'modifier_recipe',
            'supprimer_recipe',
            'voir_ingredients',
            'ajouter_ingredient',
            'modifier_ingredient',
            'supprimer_ingredient',
            'voir_categories',
            'ajouter_categorie',
            'modifier_categorie',
            'supprimer_categorie'

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
