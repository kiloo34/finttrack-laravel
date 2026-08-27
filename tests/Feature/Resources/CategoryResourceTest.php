<?php

use App\Domain\Category\Models\Category;
use App\Http\Resources\CategoryResource;

it('formats category resource correctly', function () {
    $category = Category::factory()->create([
        'name' => 'Gaji',
        'type' => 'income',
        'is_system' => false,
    ]);

    $resource = new CategoryResource($category);
    $array = $resource->toArray(request());

    expect($array)
        ->toHaveKey('id', $category->id)
        ->toHaveKey('name', 'Gaji')
        ->toHaveKey('type', 'income')
        ->toHaveKey('is_system', false)
        ->toHaveKey('created_at')
        ->toHaveKey('updated_at')
        ->not->toHaveKey('user_id'); // ensure user_id is hidden
});
