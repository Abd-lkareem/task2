<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait FileOperations {

    /**
     * @param Request $request
     * @return $this|false|string
     */
    public function upload($file, $name = null , $dir_name)
    {
        if($name)
        {
            $extension = $file->getClientOriginalExtension();
            $file = $file->storeAs($dir_name , $name.'.'.$extension, 'public');
        }
        else
            $file = $file->store($dir_name , 'public');



        if ($file) {
            return $file;
        }

        return null;
    }

    public function deleteFile($path)
    {
        return Storage::delete($path);
    }

    
}