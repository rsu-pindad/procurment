<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class RemoteSelectController extends \App\Http\Controllers\Controller
{
    public function fetch(Request $request)
    {
        $model = $request->query('model');
        $valueField = $request->query('value', 'id');
        $labelField = $request->query('label', 'name');
        $search = $request->query('q', '');

        if (!class_exists($model)) {
            return response()->json([], 400);
        }

        $query = App::make($model)::query();

        if ($search) {
            $query->where($labelField, 'like', '%' . $search . '%');
        }

        return response()->json(
            $query->select([$valueField, $labelField])->limit(20)->get()->map(function ($item) use ($valueField, $labelField) {
                return [
                    $valueField => $item->{$valueField},
                    $labelField => $item->{$labelField},
                ];
            })
        );
    }
}
