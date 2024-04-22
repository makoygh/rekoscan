<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\s3files;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\RedirectResponse;

use GeminiAPI\Laravel\Facades\Gemini;
use OpenAI\Laravel\Facades\OpenAI;
Use Exception;

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
                $imgfilename = str_replace(' ', '_', $imgfilename);

                // upload to S3 bucket
                $imgpath = $request->file('fileName')->storeAs(
                    'images',
                    $imgfilename,
                    's3'
                );

                s3files::insert([
                    'img_name' => $request->imgName,
                    'img_filename' => $imgfilename,
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

       

        //if($imageData) {
        if($imageData) { // start
            $file_name = $imageData->img_filename;
            $imgAnalysisData =  $imageData->img_analysis;
            $imgGeminiData = $imageData->img_chatgpt_title;


            $path = $this->transformPath($file_name);
            // read and store only the facial analysis once
            if (Storage::disk('s3')->exists($path)) {

                if ($imgAnalysisData == null){
                    $contents = Storage::disk('s3')->get($path);

                    // save the facial analysis data to DB
                    DB::table('s3files')
                        ->where('s3files.id', '=', $id)
                        ->update(['img_analysis' => $contents]);
                }

                // fetched updated data
                $imageData = DB::table('s3files')
                ->where('s3files.id', '=', $id)
                ->select('s3files.*')
                ->first();

                $imgAnalysisData =  $imageData->img_analysis;

            } else {

                // return message that data is not yet available
                $notification = array(
                    'message' => 'Facial analysis data is not available. Please try again later.',
                    'alert-type' => 'error'
                );

                return redirect()->back()->with($notification);

            }




            // extract face analysis data
            $faceDetail = json_decode($imgAnalysisData, true);
            // dd($faceDetail[0]);

            $dataAgeRangeLow = $faceDetail[0]['AgeRange']['Low'];
            $dataAgeRangeHigh = $faceDetail[0]['AgeRange']['High'];
            $dataGender = $faceDetail[0]['Gender']['Value'];
            $dataSmile= $this->convertToYesOrNo($faceDetail[0]['Smile']['Value']);
            $dataEyeglasses = $this->convertToYesOrNo($faceDetail[0]['Eyeglasses']['Value']);
            $dataFaceOccluded = $this->convertToYesOrNo($faceDetail[0]['FaceOccluded']['Value']);
            $dataEmotions = $faceDetail[0]['Emotions'][0]['Type'];
            $dataSunglasses = $this->convertToYesOrNo($faceDetail[0]['Sunglasses']['Value']);
            $dataBeard = $this->convertToYesOrNo($faceDetail[0]['Beard']['Value']);
            $dataMustache = $this->convertToYesOrNo($faceDetail[0]['Mustache']['Value']);
            $dataEyesOpen = $this->convertToYesOrNo($faceDetail[0]['EyesOpen']['Value']);
            $dataMouthOpen = $this->convertToYesOrNo($faceDetail[0]['MouthOpen']['Value']);

            if($imgGeminiData == null){
                $promptSmile = ' who is not smiling ';
                if ($dataSmile=='Yes') {
                    $promptSmile = ' who is smiling ';
                }

                $promptEG = ' , with no eyeglass ';
                if ($dataEyeglasses=='Yes') {
                    $promptEG = ', wearing an eyeglass ';
                }  
                
                $promptFO = ' face is not occluded ';
                if ($dataFaceOccluded=='Yes') {
                    $promptFO = ', with an occluded face ';
                }    

                $promptSG = ' , with no sunglass ';
                if ($dataSunglasses=='Yes') {
                    $promptSG = ', wearing a sunglass ';
                } 

                $promptBeard = ' , having no beard ';
                if ($dataSmile=='Yes') {
                    $promptBeard = ', with a beard ';
                } 

                $promptMustache = ' , having no mustache ';
                if ($dataMustache=='Yes') {
                    $promptMustache = ', with a mustache ';
                } 

                $promptOE = ' , eyes are closed ';
                if ($dataEyesOpen=='Yes') {
                    $promptOE = ' , eyes are open ';
                } 

                $promptMO = ' , mouth is closed ';
                if ($dataMouthOpen=='Yes') {
                    $promptMO = ' , and mouth is open ';
                } 

                $promptAge = ', with an age between ' . $dataAgeRangeLow . ' to ' .  $dataAgeRangeHigh;
                $promptEmotions = ', with a ' . $dataEmotions . ' emotions ';

                $prompt = 'Based on the following analysis: A ' . $dataGender . ' person ' . $promptSmile;
                $prompt = $prompt . $promptEG . $promptFO . $promptSG . $promptAge . $promptEmotions . $promptBeard . $promptMustache;
                $prompt = $prompt . $promptOE . $promptMO . ' ';


                $promptTitle =  $prompt . ', give me the best title for this fictional news article about that person.';;
                $promptContent = $prompt . ', create a fictional news article about that person in 500 words. Not need for a title.';

                //dd($prompt);
                // Gemini AI
                //$responseTitle = $this->generateNewsGemini($promptTitle);
                //$responseContent = $this->generateNewsGemini($promptContent);

                //OpenAI prompt
                $promptOAI = $prompt . ' , create a fictional news article about that person in 500 words. Always separate title and content with a colon';
                
                try{
                    if(is_null($imgGeminiData) && !is_null($imgAnalysisData)) {
                        //$responseJson = $this->generateNewsOpenAI($promptOAI);
                        $responseJson = $this->genNews($imgAnalysisData);

                        // check if there is an error from OpenAI
                        if(Str::contains($responseJson, 'Error:')){

                            // user Gemini AI instead
                            $responseTitle = $this->generateNewsGemini($promptTitle);
                            $responseContent = $this->generateNewsGemini($promptContent);

                            //save the News Article to DB from Gemini AI
                            DB::table('s3files')
                            ->where('s3files.id', '=', $id)
                            ->update(['img_chatgpt_title' => $responseTitle,
                                    'img_chatgpt_content' => $responseContent,
                                    ]);                            

                            // return error message about OpenAI
                            $notification = array(
                                'message' => 'Error encountered when using OpenAI. Switched to Gemini AI to generate the news article.' ,
                                'alert-type' => 'warning'
                            );

                            return redirect()->back()->with($notification);

                         }

                        
                         // process the data to store to DB
                        $news_title_content = $this->parseNews($responseJson);

                        DB::table('s3files')
                            ->where('s3files.id', '=', $id)
                            ->update(['img_chatgpt_content' => $news_title_content['body'], 'img_chatgpt_title' => $news_title_content['headline']]);


                    }

                } catch (Exception $error) {

                    // return error message about OpenAI
                    $notification = array(
                        'message' => 'Error connecting to OpenAI.',
                        'alert-type' => 'error'
                    );

                    return redirect()->back()->with($notification);
                }

                //dd($responseTitle, $responseContent);

                //save the News Article to DB
               /* 
                DB::table('s3files')
                ->where('s3files.id', '=', $id)
                ->update(['img_chatgpt_title' => $responseTitle,
                          'img_chatgpt_content' => $responseContent,
                        ]);
                */

            }   

            // fetch updated data
            $imageData = DB::table('s3files')
            ->where('s3files.id', '=', $id)
            ->select('s3files.*')
            ->get();            
             
            
           
        } else {

            // return message that data is not yet available
            $notification = array(
                'message' => 'Data do not exist.',
                'alert-type' => 'error'
            );

            return redirect()->back()->with($notification);

        } // end 

        return view('view_image', ['imageData'=>$imageData,
         'gender'=>$dataGender,
         'smile'=>$dataSmile,
         'eyeglasses'=>$dataEyeglasses,
         'faceoccluded'=>$dataFaceOccluded, 
         'emotions'=>$dataEmotions,
         'agelow'=>$dataAgeRangeLow,
         'agehigh'=>$dataAgeRangeHigh,
         'sunglasses'=>$dataSunglasses,
         'beard'=>$dataBeard,
         'mustache'=>$dataMustache,
         'eyesopen'=>$dataEyesOpen,
         'mouthopen'=>$dataMouthOpen
        ]);

    }

    private function convertToYesOrNo($value): array|string
    {
       $result = 'No';
       if($value == 1){
        $result = 'Yes';
       }

        return $result;
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
        //$newPath = str_replace('images/', 'face-analyses/', $originalPath);
        $newPath = 'face-analyses/' . $originalPath;
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

        /*if(is_null($imageNewsData->img_chatgpt_title) && is_null($imageNewsData->img_chatgpt_content) && !is_null($imageNewsData->img_analysis)) {
            $responseJson = $this->genNews($imageNewsData->img_analysis);

            $news_title_content = $this->parseNews($responseJson);

            DB::table('s3files')
                ->where('s3files.id', '=', $id)
                ->update(['img_chatgpt_content' => $news_title_content['body'], 'img_chatgpt_title' => $news_title_content['headline']]);
        }*/

        $imageNewsData = DB::table('s3files')
            ->where('s3files.id', '=', $id)
            ->select('s3files.*')
            ->get();

        return view('view_news', compact('imageNewsData'));

    }

    public function parseNews($responseJson)
    {
        // Accessing the content of the message directly
        $content = $responseJson['choices'][0]['message']['content'];

        // Splitting content to extract headline and body
        $splitContent = explode("\n\n", $content, 3);
        $headline = trim(explode(":", $splitContent[0], 2)[1], ' "');  // Remove extra quotes and whitespace
        $body = $splitContent[2];  // The body starts after the second "\n\n"

        print_r($responseJson);

        return [
            'headline' => $headline,
            'body' => $body
        ];
    }
    /**
     * Generates news using the OpenAI API.
     *
     * @param string $data The prompt for generating news.
     * @return array|false The generated news as an associative array or false on failure.
     */
    private function genNews($data) {

        $apiSecret = env('API_SECRET', 'default-secret');

        $client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => [
                'Authorization' => 'Bearer ' . $apiSecret,
                'Content-Type' => 'application/json',
            ]
        ]);
        try{
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'user', 'content' => "Based on the following analysis: $data, create fictional news about the person in the photo. Always separate title and content with a colon"]
                    ],
                    'max_tokens' => 3000
                ]
            ]);
        } catch (Exception $error) {
            return 'Error: ' . $error;
        }

        $body = $response->getBody();
        return json_decode($body, true);

    }


    // Generate news article using Google Gemini AI
    private function generateNewsGemini($data){

        $response = Gemini::generateText($data);

        return  $response;

    }

    // OpenAI / Laravel libraries
    private function generateNewsOpenAI($data){

        try{
            $response = OpenAI::completions()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $data],
                ],
            ]);
        } catch (Exception $error) {
            return 'Error: ' . $error;
        }

        
        $body = $response->getBody();
        return json_decode($body, true);

    }

} // end of controller
