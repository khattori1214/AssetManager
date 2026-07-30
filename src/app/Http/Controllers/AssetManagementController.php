<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetManagementController extends Controller
{
    public function index(){
        
        $assetModel = new Asset;
        $assetManagementData=$assetModel->assetData();
        return view('admin.index',['assetManagementData'=>$assetManagementData]);
    }

    public function store(Request $request){
        $registerAsset= $request->only('asset_name');
        
        $assetModel = new Asset;
        $assetModel->registerAsset($registerAsset);
        return redirect('/admin');
    }
}
