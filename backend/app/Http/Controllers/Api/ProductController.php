<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Reference;

use Illuminate\Validation\Rule;

class ProductController extends Controller
{
public function index(Request $request)
{
    $query = Product::with(['category', 'unit', 'brand']);

    if ($request->filled('search')) {
        $search = trim($request->query('search'));

        $query->where(function ($q) use ($search) {
            $q->orWhere('id', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")

                ->orWhereHas('category', function ($categoryQuery) use ($search) {
                    $categoryQuery->where('name', 'like', "%{$search}%");
                })

                ->orWhereHas('unit', function ($unitQuery) use ($search) {
                    $unitQuery->where('name', 'like', "%{$search}%");
                })

                ->orWhereHas('brand', function ($brandQuery) use ($search) {
                    $brandQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    $perPage = $request->integer('per_page', 10);
    $perPage = min(max($perPage, 1), 100);

    $allowedSortFields = [
        'id',
        'name',
        'barcode',
        'sku',
        'category',
        'unit',
        'brand',
        'min_quantity',
        'description',
        'status'
    ];

    $sortField = $request->query('sort_field', 'id');

    if (!in_array($sortField, $allowedSortFields, true)) {
        $sortField = 'id';
    }

    $sortOrder = strtolower(
        $request->query('sort_order', 'asc')
    );

    if (!in_array($sortOrder, ['asc', 'desc'], true)) {
        $sortOrder = 'asc';
    }

    if ($sortField === 'category') {

        $query->orderBy(
            \App\Models\Reference::select('name')
                ->whereColumn(
                    'references.id',
                    'products.category_id'
                ),
            $sortOrder
        );

    } elseif ($sortField === 'unit') {

        $query->orderBy(
            \App\Models\Reference::select('name')
                ->whereColumn(
                    'references.id',
                    'products.unit_id'
                ),
            $sortOrder
        );

    } elseif ($sortField === 'brand') {

        $query->orderBy(
            \App\Models\Reference::select('name')
                ->whereColumn(
                    'references.id',
                    'products.brand_id'
                ),
            $sortOrder
        );

    } else {

        $query->orderBy(
            $sortField,
            $sortOrder
        );
    }

    $products = $query->paginate($perPage);

    return response()->json([
        'success' => true,
        'message' => 'Успешно получили данные!',
        'data' => $products,
    ], 200);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'barcode'      => 'nullable|string|max:255',
            'sku'          => 'nullable|string|max:255',
            'category_id'  => ['required', 'integer', Rule::exists('references', 'id')->where('type', 'category')],
            'unit_id'      => ['required', 'integer', Rule::exists('references', 'id')->where('type', 'unit')],
            'brand_id'     => ['nullable', 'integer', Rule::exists('references', 'id')->where('type', 'brand')],
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'min_quantity' => 'nullable|numeric|min:0',
            'description'  => 'nullable|string',
            'status'       => 'required|boolean',
        ]);


        if ($request->hasFile('image')) {
            $filename = now()->format('Y-m-d-H-i').'_'.uniqid().'.'.$request->file('image')->extension();
            $request->file('image')->move(public_path('/images/products/'), $filename);
            $validated['image'] = $filename;
        } else {
            $validated['image'] = 'default.png';
        }

        $product = Product::create($validated);

        $product->load([
            'category',
            'unit',
            'brand'
        ]);

        return response()->json([
            'message' => 'Товар успешно добавлен',
            'data' => $product,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'barcode'      => 'nullable|string|max:255',
            'sku'          => 'nullable|string|max:255',
            'category_id'  => ['required', 'integer', Rule::exists('references', 'id')->where('type', 'category')],
            'unit_id'      => ['required', 'integer', Rule::exists('references', 'id')->where('type', 'unit')],
            'brand_id'     => ['nullable','integer', Rule::exists('references', 'id')->where('type', 'brand')],
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'min_quantity' => 'nullable|numeric|min:0',
            'description'  => 'nullable|string',
            'status'       => 'required|boolean',
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($product->image && $product->image !== 'default.png' && file_exists(public_path('/images/products/'.$product->image))) {
                unlink(public_path('/images/products/'.$product->image));
            }

            $filename = now()->format('Y-m-d-H-i').'_'.uniqid().'.'.$request->file('image')->extension();
            $request->file('image')->move(public_path('/images/products/'), $filename);

            $validated['image'] = $filename;
        }

        $product->update($validated);

        $product->load([
            'category',
            'unit',
            'brand'
        ]);

        return response()->json([
            'message' => 'Товар успешно изменен',
            'data' => $product,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && $product->image !== 'default.png' && file_exists(public_path('/images/products/'.$product->image))){
            unlink(public_path('/images/products/'.$product->image));
        }
        $product->delete();

        return response()->json([
            'message' => 'Товар успешно удален'
        ]);
    }

    public function destroySelected(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:products,id'],
        ]);

        $products = Product::whereIn('id', $validated['ids'])->get();
        foreach ($products as $product) {
            if (
                $product->image &&
                $product->image !== 'default.png' &&
                file_exists(public_path('/images/products/'.$product->image))
            ) {
                unlink(
                    public_path('/images/products/'.$product->image)
                );
            }
        }

        $deletedCount = Product::whereIn(
            'id',
            $validated['ids']
        )->delete();

        return response()->json([
            'message' => "Удалено товаров: {$deletedCount}",
            'deleted_count' => $deletedCount
        ], 200);
    }

    public function generateBarcode(Request $request)
    {
        $request->validate([
            'key' => ['required', 'integer', 'in:1,2'],
        ]);

        $key = (int) $request->key;

        /*
        |--------------------------------------------------------------------------
        | ШТ — EAN-13
        |--------------------------------------------------------------------------
        */
        if ($key === 1) {

            $product = Product::where('barcode', 'LIKE', '20100%')
                ->orderByRaw('CAST(barcode AS UNSIGNED) DESC')
                ->first();

            // Если таких товаров ещё нет
            $newBarcode = '2010000000014';

            if ($product) {

                // Берём первые 12 цифр предыдущего EAN-13
                // и увеличиваем номер на 1
                $barcode12 = (string) (
                    (int) substr($product->barcode, 0, 12) + 1
                );

                $barcode12 = str_pad(
                    $barcode12,
                    12,
                    '0',
                    STR_PAD_LEFT
                );

                $sumOdd = 0;
                $sumEven = 0;

                // Первые 12 цифр
                for ($i = 0; $i < 12; $i++) {

                    $digit = (int) $barcode12[$i];

                    if ($i % 2 === 0) {
                        $sumOdd += $digit;
                    } else {
                        $sumEven += $digit;
                    }
                }

                // EAN-13 контрольная цифра
                $sum = ($sumOdd + (3 * $sumEven)) % 10;

                $checkDigit = (10 - $sum) % 10;

                $newBarcode = $barcode12 . $checkDigit;
            }

            return response()->json([
                'success' => true,
                'new_barcode' => $newBarcode,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | КГ
        |--------------------------------------------------------------------------
        */

        $kgUnitId = Reference::where('type', 'unit')
            ->where(function ($query) {
                $query
                    ->where('short_name', 'кг')
                    ->orWhere('name', 'Килограмм');
            })
            ->value('id');

        if (!$kgUnitId) {
            return response()->json([
                'success' => false,
                'message' => 'Единица измерения "кг" не найдена',
            ], 422);
        }

        $newBarcode = Product::selectRaw(
            'COALESCE(MAX(CAST(barcode AS UNSIGNED)), 0) + 1 AS barcode'
        )
            ->where('unit_id', $kgUnitId)
            ->whereRaw("barcode REGEXP '^[0-9]+$'")
            ->whereRaw('CAST(barcode AS UNSIGNED) < 100000')
            ->value('barcode');

        return response()->json([
            'success' => true,
            'new_barcode' => (string) $newBarcode,
        ]);
    }
}