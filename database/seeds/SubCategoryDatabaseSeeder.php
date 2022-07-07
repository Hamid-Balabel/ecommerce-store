<?php

use App\Models\Product;
use Illuminate\Database\Seeder;

class SubCategoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Product::class, 5)->create([
            'parent_id'=>$this->getRandomParentId()
        ]);

    }

    private function getRandomParentId()
    {
        $parent_id= \App\Models\Product::inRandomOrder()->first();
        return $parent_id;
    }
}
