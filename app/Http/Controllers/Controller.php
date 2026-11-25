<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

abstract class Controller
{
    /**
     * Ubah kata kunci pencarian menjadi pola LIKE yang aman.
     *
     * Mengembalikan null bila kata kunci kosong supaya query bisa memakai
     * pola `(? IS NULL OR kolom LIKE ?)` tanpa filter tambahan.
     */
    protected function kataKunciPencarian(?string $keyword): ?string
    {
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return null;
        }

        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $keyword);

        return '%'.$escaped.'%';
    }

    /**
     * Jalankan query mentah lalu bungkus hasilnya menjadi paginator.
     *
     * Query harus berupa SELECT tanpa LIMIT. Total baris dihitung dari
     * subquery yang sama sehingga ringkasan tetap akurat.
     *
     * @param  array<int, mixed>  $bindings
     */
    protected function paginateData(string $sql, array $bindings = [], int $perPage = 10): LengthAwarePaginator
    {
        $total = (int) DB::selectOne(
            "SELECT COUNT(*) AS total FROM ({$sql}) AS rs_count",
            $bindings
        )->total;

        $page = Paginator::resolveCurrentPage();
        $page = max(1, (int) $page);

        $rows = [];

        if ($total > 0) {
            $rows = DB::select(
                "{$sql} LIMIT ? OFFSET ?",
                [...$bindings, $perPage, ($page - 1) * $perPage]
            );
        }

        return new LengthAwarePaginator(
            $rows,
            $total,
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );
    }
}
