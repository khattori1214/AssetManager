<?php

namespace App\Http\Controllers;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetApplicationController extends Controller
{

    public function index()
    {
        $asset = new Asset();
        $assetData = $asset->assetData();
        return view('assets.index', ['assetData' => $assetData]);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $asset = new Asset();
        $assetData = $asset->search($keyword);

        return view('assets.index', ['assetData' => $assetData,'keyword' => $keyword]);
    }


}
