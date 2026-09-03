<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reference;

class ReferenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Reference::query();

        $type = $request->query('type');

        if ($request->filled('type')) {
            $query->where('type', $type);
        }

        if ($request->filled('search')) {
            $search = trim($request->query('search'));

            $searchFields = [
                'id',
                'name',
                'code',
                'description',
            ];

            if ($type === 'unit') {
                $searchFields[] = 'short_name';
            }

            $query->where(function ($q) use ($search, $searchFields, $type) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'like', "%{$search}%");
                }

                if ($type === 'category') {
                    $q->orWhereHas('parent', function ($parentQuery) use ($search) {
                        $parentQuery->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        $perPage = $request->integer('per_page', 10);

        $allowedSortFields = [
            'id',
            'name',
            'code',
            'description',
            'status',
        ];

        if ($type === 'unit') {
            $allowedSortFields[] = 'short_name';
        }

        if ($type === 'category') {
            $allowedSortFields[] = 'parent_id';
        }

        $sortField = $request->query('sort_field', 'name');

        if (!in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'name';
        }

        $sortOrder = strtolower($request->query('sort_order', 'asc'));

        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'asc';
        }

        $references = $query
            ->orderBy($sortField, $sortOrder)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Успешно получили данные!',
            'data' => $references,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'        => 'required|string|max:255',
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50',
            'short_name'  => 'nullable|string|max:50',
            'parent_id'   => 'nullable|integer',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        if ($request->hasFile('image')) {
            $filename =
                now()->format('Y-m-d-H-i') . '_' .
                uniqid() . '.' .
                $request->file('image')->extension();

            $request->file('image')->move(
                public_path('/images/references/'),
                $filename
            );

            $validated['image'] = $filename;
        } else {
            $validated['image'] = 'default.png';
        }

        $reference = Reference::create($validated);

        return response()->json([
            'message' => 'Запись успешно добавлена',
            'data' => $reference,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'type'        => 'required|string|max:255',
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|max:50',
            'short_name'  => 'nullable|string|max:50',
            'parent_id'   => 'nullable|integer',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
            'status'      => 'required|boolean',
        ]);

        $reference = Reference::findOrFail($id);

        if ($request->hasFile('image')) {
            if (
                $reference->image &&
                $reference->image !== 'default.png' &&
                file_exists(
                    public_path('/images/references/' . $reference->image)
                )
            ) {
                unlink(
                    public_path('/images/references/' . $reference->image)
                );
            }

            $filename =
                now()->format('Y-m-d-H-i') . '_' .
                uniqid() . '.' .
                $request->file('image')->extension();

            $request->file('image')->move(
                public_path('/images/references/'),
                $filename
            );

            $validated['image'] = $filename;
        }

        $reference->update($validated);

        return response()->json([
            'message' => 'Запись успешно изменена',
            'data' => $reference,
        ]);
    }

    public function destroy($id)
    {
        $reference = Reference::findOrFail($id);

        if (
            $reference->image &&
            $reference->image !== 'default.png' &&
            file_exists(
                public_path('/images/references/' . $reference->image)
            )
        ) {
            unlink(
                public_path('/images/references/' . $reference->image)
            );
        }

        $reference->delete();

        return response()->json([
            'message' => 'Запись успешно удалена'
        ]);
    }

    public function destroySelected(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:references,id'],
        ]);

        $references = Reference::whereIn(
            'id',
            $validated['ids']
        )->get();

        foreach ($references as $reference) {
            if (
                $reference->image &&
                $reference->image !== 'default.png' &&
                file_exists(
                    public_path('/images/references/' . $reference->image)
                )
            ) {
                unlink(
                    public_path('/images/references/' . $reference->image)
                );
            }
        }

        $deletedCount = Reference::whereIn(
            'id',
            $validated['ids']
        )->delete();

        return response()->json([
            'message' => "Удалено записей: {$deletedCount}",
            'deleted_count' => $deletedCount
        ], 200);
    }
}