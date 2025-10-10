<?php

namespace App\Traits;

use Illuminate\Support\Facades\URL;

trait PaginatesCollection
{
    protected function paginate($items, $perPage, $page)
    {
        $offset = ($page - 1) * $perPage;
        $paginatedItems = $items->slice($offset, $perPage)->values();

        return [
            'data' => $paginatedItems,
            'meta' => [
                'total' => $items->count(),
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($items->count() / $perPage),
                'from' => $offset + 1,
                'to' => $offset + $paginatedItems->count(),
            ],
            'links' => [
                'first' => url()->current() . '?page=1',
                'last' => url()->current() . '?page=' . ceil($items->count() / $perPage),
                'prev' => $page > 1 ? url()->current() . '?page=' . ($page - 1) : null,
                'next' => $page < ceil($items->count() / $perPage) ? url()->current() . '?page=' . ($page + 1) : null,
            ],
        ];
    }
}
