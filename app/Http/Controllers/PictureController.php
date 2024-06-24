<?php

namespace App\Http\Controllers;

use App\Models\Picture;
use Illuminate\Http\Request;

class PictureController extends Controller
{
    public function index()
    {
        return Picture::all();
    }

    public function store(Request $request)
    {
        return Picture::create($request->all());
    }

    public function show($id)
    {
        return Picture::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $picture = Picture::findOrFail($id);
        $picture->update($request->all());
        return $picture;
    }

    public function destroy($id)
    {
        $picture = Picture::findOrFail($id);
        $picture->delete();
        return 204;
    }
}
