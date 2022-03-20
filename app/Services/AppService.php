<?php

namespace App\Services;
use App\Models\App;

class AppService 
{
    public function getAll(){
        return App::withCount('products')->get();
    }

    public function getAppById($appId){
        return App::find($appId);
    }

    public function add(array $data){
        $app = new App(); 
        $app->shop_name = $data['shopName'];
        $app->access_token = $data['accessToken'];
        $app->save();
        return $app->id;
    }
    
    public function delete($appId) : bool{
        $app = App::find($appId);
        $app->delete();
        return true;
    }

}