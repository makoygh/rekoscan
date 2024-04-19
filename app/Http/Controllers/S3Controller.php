<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\s3files;
use Illuminate\Support\Facades\Auth;

//use Aws\S3\S3Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Uploads an image file to the S3 bucket and saves the file details to the database.
 *
 * @param Request $request The request object.
 * @return \Illuminate\Http\RedirectResponse The redirect response.
 */
class S3Controller extends Controller
{

    /**
     * Uploads an image file to the S3 bucket and saves the file details to the database
     *
     * @param Request $request The HTTP request containing the image file and associated data
     * @return RedirectResponse
     */
    public function uploadToS3(Request $request)
    {


        //validation
        $request->validate([
            'imgName' => 'required',
            'fileName' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'

        ]);


        try {
            //Code that may throw an Exception

            $uuid = Str::random(5);

            //Upload the file to S3 bucket
            if ($request->hasFile('fileName')) {
                $imgfile = $request->file('fileName');
                $imgfilename = $uuid . '_' . date('YmdHi') . '_' . $imgfile->getClientOriginalName();

                // upload to S3 bucket
                $imgpath = $request->file('fileName')->storeAs(
                    'images',
                    $imgfilename,
                    's3'
                );

                s3files::insert([
                    'img_name' => $request->imgName,
                    'img_filename' => $imgpath,
                    'img_localfile' => $imgfilename,
                    'created_at' => now()
                ]);
            }

            //upload the file locally
            if ($request->hasFile('fileName')) {
                $imgLocalfile = $request->file('fileName');
                //$imgLocalfilename = date('YmdHi').'_'.$imgLocalfile->getClientOriginalName();
                $imgLocalfile->move(public_path('upload/s3'), $imgfilename); // local upload
            }


            $notification = array(
                'message' => 'Image Uploaded Successfully',
                'alert-type' => 'success'
            );

        } catch (Exception $error) {

            $notification = array(
                'message' => 'Uploading of Image Failed.',
                'alert-type' => 'error'
            );

            // Either form a friendlier message to display to the user OR redirect them to a failure page
        }


        // page refresh
        return redirect()->back()->with($notification);

    }


    /**
     * Fetches and displays all uploaded images
     *
     * @return Factory|View
     */
    public function allImages()
    {

        $uploadedImages = s3files::query()
            ->orderBy('created_at')
            ->get();


        return view('all_images', compact('uploadedImages'));

    }


    /**
     * Fetches and displays image data based on the given ID
     *
     * @param int $id The ID of the image entry
     * @return Factory|View
     */
    public function viewImage($id)
    {
        $imageData = DB::table('s3files')
            ->where('s3files.id', '=', $id)
            ->select('s3files.*')
            ->first();

        if($imageData) {
            $file_name = $imageData->img_filename;

            $path = $this->transformPath($file_name);
            if (Storage::disk('s3')->exists($path)) {
                $contents = Storage::disk('s3')->get($path);
                DB::table('s3files')
                    ->where('s3files.id', '=', $id)
                    ->update(['img_analysis' => $contents]);
            }

            $imageData = DB::table('s3files')
                ->where('s3files.id', '=', $id)
                ->select('s3files.*')
                ->get();
        }

        return view('view_image', compact('imageData'));

    }


    /**
     * Transforms the given path by removing the 'images/' prefix and changing the file extension from .jpg to .txt
     *
     * @param string $originalPath The original path to be transformed
     * @return array|string The transformed path
     */
    private function transformPath($originalPath): array|string
    {
        // Remove the 'images/' prefix and change the file extension from .jpg to .txt
        $newPath = str_replace('images/', 'face-analyses/', $originalPath);
        $newPath = substr_replace($newPath, '.txt', strrpos($newPath, '.'));

        return $newPath;
    }

    /**
     * Fetches and displays news data based on the given ID
     *
     * @param int $id The ID of the news entry
     * @return Factory|View
     */
    public function viewNews($id)
    {

        $imageNewsData = DB::table('s3files')
            ->where('s3files.id', '=', $id)
            ->select('s3files.*')
            ->first();

        if(!$imageNewsData->img_chatgpt_title && !$imageNewsData->img_chatgpt_content && !$imageNewsData->img_analysis) {
            $news = $this->genNews($imageNewsData->img_analysis);

            DB::table('s3files')
                ->where('s3files.id', '=', $id)
                ->update(['img_chatgpt_content' => $news, 'img_chatgpt_title' => 'test title: change me']);
        }

        $imageNewsData = DB::table('s3files')
            ->where('s3files.id', '=', $id)
            ->select('s3files.*')
            ->get();

        return view('view_news', compact('imageNewsData'));

    }


    /**
     * Generates news using the OpenAI API.
     *
     * @param string $data The prompt for generating news.
     * @return array|false The generated news as an associative array or false on failure.
     */
    private function genNews($data) {
        $client = new Client();
        $response = $client->post('https://chat.openai.com/g/g-7i6yyOBxg-is215proj', [
            'headers' => [
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'prompt' => $data,
                'max_tokens' => 3000,
            ],
        ]);

        $body = $response->getBody();
        return json_decode($body, true);

    }

} // end of controller
