<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\s3files;
use Illuminate\Support\Facades\Auth;
//use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use DB;



class S3Controller extends Controller
{
    
// Save the files details to DB and upload the image to S3 bucket and a local copy
public function uploadToS3(Request $request){


    //validation
    $request->validate([
        'imgName' => 'required',
        'fileName' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'

      ]);



    //Upload the file to S3 bucket
    if($request->hasFile('fileName')){
        $imgfile = $request->file('fileName');
        $imgfilename = date('YmdHi').'_'.$imgfile->getClientOriginalName();
        // upload to S3 bucket
        $imgpath = $request->file('fileName')->storeAs(
            'images',
            $imgfilename,
            's3'
        );

    }

    //upload the file locally
    if($request->hasFile('fileName')){
        $imgLocalfile = $request->file('fileName');
        $imgLocalfile->move(public_path('upload/s3'),$imgfilename); // local upload
    }    

    // insert record to DB
    s3files::insert([
        'img_name' => $request->imgName,
        'img_filename' => $imgpath,
        'img_localfile' => $imgfilename,
        'created_at' => now()
    ]);


    $notification = array(
        'message' => 'Image Uploaded Successfully',
        'alert-type' => 'success'
    );
    

    // page refresh
    return redirect()->back()->with($notification); 

}    


    // View all uploaded images
    public function allImages(){

        $uploadedImages = s3files::query()
        ->orderBy('created_at')
        ->get();        

        
        return view('all_images',compact('uploadedImages'));

    }    


    // View facial analysis page
    public function viewImage($id){

        $imageData = DB::table('s3files')
        ->where('s3files.id', '=',$id)
        ->select('s3files.*')
        ->get();

        return view('view_image',compact('imageData'));

    }


    // View news page
    public function viewNews($id){


        $imageNewsData = DB::table('s3files')
        ->where('s3files.id', '=',$id)
        ->select('s3files.*')
        ->get();

        return view('view_news',compact('imageNewsData'));

    }    

} // end of controller
