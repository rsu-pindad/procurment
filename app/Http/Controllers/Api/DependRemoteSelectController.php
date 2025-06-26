<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class DependRemoteSelectController extends Controller
{

    public function fetch(Request $request)
    {
        $model = $request->query('model');
        $valueField = $request->query('value', 'id');
        $labelField = $request->query('label', 'name');
        $search = $request->query('q', '');
        $optgroupField = $request->query('optgroup'); // Contoh: 'unit.nama_unit'

        if (!class_exists($model)) {
            return response()->json([], 400);
        }

        $query = app($model)::query();

        if ($search && $labelField) {
            $query->where($labelField, 'like', '%' . $search . '%');
        }

        if ($optgroupField && str_contains($optgroupField, '.')) {
            $relation = strstr($optgroupField, '.', true);
            $query->with($relation);
        }

        $items = $query->limit(50)->get();

        $options = $items->map(function ($item) use ($valueField, $labelField, $optgroupField) {
            return [
                'id' => $item->{$valueField},
                'make'=>$item->produk_ajuan,
                'model' => $item->produk_ajuan,
            ];
        });

        $optgroups = $options->pluck('make')->unique()->values()->map(function ($name, $i) {
            return [
                '$order' => $i,
                'id' => $name,
                'name' => $name,
            ];
        });

        return response()->json([
            'options' => $options,
            'optgroups' => $optgroups,
        ]);
    }


}
