<?php
namespace App\Helpers;
use Illuminate\Http\Request;
use Storage;
class AmazonFileHelper{
  public static function UploadFile(Request $request,$fileKey,$filePath,$bucket){
    // return response()->json([
    //   'status'=>false,
    //   'message'=>$fileKey.' is missing',
    // ]);
    if($request->hasfile($fileKey)){
      $file = $request->file($fileKey);
      $name=time().$file->getClientOriginalName();
      $ar = Storage::disk($bucket)->put($filePath.$name, file_get_contents($file));
      $imgUrl=Storage::disk($bucket)->url($name);
      return response()->json([
        'status'=>$ar,
        'message'=>'Image Uploaded successfully',
        'imgUrl'=>$imgUrl
      ]);
    }
    // $response[]=$request->all();
      // if($files=$request->file('image')){
        //   if (env('APP_ENV') == 'local'){
        //     $response[]='Local Enviroment';
        //   }else{
        //     $response[]='Production Enviroment';
        //   }

        //   // $file_name = $_FILES['image']['name'];
        //   $file_name = $files->getClientOriginalName();
        //   $temp_file_location = $_FILES['image']['tmp_name'];
        //   require 'vendor/autoload.php';
        //   $s3 = new Aws\S3\S3Client([
        //     'region'  => '-- your region --',
        //     'version' => 'latest',
        //     'credentials' => [
        //       'key'    => "-- access key id --",
        //       'secret' => "-- secret access key --",
        //     ]
        //   ]);
        //   return $s3;
        //   $result = $s3->putObject([
        //     'Bucket' => '-- bucket name --',
        //     'Key'    => $file_name,
        //     'SourceFile' => $temp_file_location     
        //   ]);
        //   var_dump($result);
      // }
      // if ($request->has('name')) {
      // }
      // return $response;
  }
}