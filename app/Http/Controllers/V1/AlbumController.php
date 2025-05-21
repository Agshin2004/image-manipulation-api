<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreAlbumRequest;
use App\Http\Requests\V1\UpdateAlbumRequest;
use App\Http\Resources\V1\AlbumResource;
use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return AlbumResource::collection(Album::where('user_id', $request->user()->id)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlbumRequest $request)
    {
        $data = $request->all();
        // attach user_id to data array
        $data['user_id'] = $request->user()->id;
        $album = Album::create($data);
        return new AlbumResource($album);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Album $album)
    {
        if ($request->user()->id !== $album->user_id)
            return response()->json(['message' => 'unauthorized'], 401);

        return $album;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlbumRequest $request, Album $album)
    {
        if ($request->user()->id !== $album->user_id)
            return response()->json(['message' => 'unauthorized'], 401);

        $album->update($request->all());
        return new AlbumResource($album);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Album $album)
    {
        if ($request->user()->id !== $album->user_id)
            return response()->json(['message' => 'unauthorized'], 401);

        $album->delete();
        return response(status: 204);
    }
}
