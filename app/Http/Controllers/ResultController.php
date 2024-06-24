<?php

namespace App\Http\Controllers;

use App\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
        return Result::all();
    }

    public function store(Request $request)
    {
        return Result::create($request->all());
    }

    public function show($id)
    {
        return Result::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $result = Result::findOrFail($id);
        $result->update($request->all());
        return $result;
    }

    public function destroy($id)
    {
        $result = Result::findOrFail($id);
        $result->delete();
        return 204;
    }
}
