<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

class ProductRepository
{
    public function all(): Collection
    {
        return DB::table('products')
            ->orderBy('id', 'desc')
            ->get();
    }

    public function findById(int $id): ?stdClass
    {
        return DB::table('products')
            ->where('id', $id)
            ->first();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): ?stdClass
    {
        $id = DB::table('products')->insertGetId([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'stock' => $data['stock'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->findById((int) $id);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): ?stdClass
    {
        $product = $this->findById($id);

        if (!$product) {
            return null;
        }

        DB::table('products')
            ->where('id', $id)
            ->update([
                'name' => $data['name'] ?? $product->name,
                'description' => array_key_exists('description', $data) ? $data['description'] : $product->description,
                'price' => $data['price'] ?? $product->price,
                'stock' => $data['stock'] ?? $product->stock,
                'updated_at' => now(),
            ]);

        return $this->findById($id);
    }

    public function delete(int $id): int
    {
        return DB::table('products')
            ->where('id', $id)
            ->delete();
    }
}
