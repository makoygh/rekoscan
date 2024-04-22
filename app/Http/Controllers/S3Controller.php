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
            $faceDataCount = count($faceDetail);


            $ctr=0;
            $dataAgeRangeLow = '';
            $dataAgeRangeHigh = '';
            $dataGender = '';
            $dataSmile = '';
            $dataEyeglasses = '';
            $dataFaceOccluded = '';
            $dataEmotions = '';
            $dataSunglasses = '';
            $dataBeard = '';
            $dataMustache = '';
            $dataEyesOpen = '';
            $dataMouthOpen = '';

            $smilingCount = 0;
            $notSmilingCount = 0;

            $withEGCount = 0;
            $withoutEGCount = 0;

            $occludedFaceCount = 0;
            $notOccludedFaceCount = 0;

            $sunglassCount = 0;
            $noSunglassCount = 0;

            $beardCount = 0;
            $noBeardCount = 0;

            $mustacheCount = 0;
            $noMustacheCount = 0;

            $maleCount = 0;
            $femaleCount = 0;

            $openEyesCount = 0;
            $closeEyesCount = 0;

            $openMouthCount = 0;
            $closeMouthCount = 0;

            while($ctr < $faceDataCount){
             
           
                $dataAgeRangeLow =  $faceDetail[$ctr]['AgeRange']['Low'] . ', ' . $dataAgeRangeLow;
                $dataAgeRangeHigh = $faceDetail[$ctr]['AgeRange']['High'] . ', ' . $dataAgeRangeHigh;
                $dataGender = $faceDetail[$ctr]['Gender']['Value'] . ', ' . $dataGender;

                if($faceDetail[$ctr]['Gender']['Value'] == 'Male')
                {
                    $maleCount = $maleCount + 1;
                } else {

                    $femaleCount = $femaleCount + 1;
                }

                $dataSmile= $this->convertToYesOrNo($faceDetail[$ctr]['Smile']['Value']) . ', ' . $dataSmile;

                if($faceDetail[$ctr]['Smile']['Value'] == 1)
                {
                    $smilingCount = $smilingCount + 1;
                } else {

                    $notSmilingCount = $notSmilingCount + 1;
                }
                
                $dataEyeglasses = $this->convertToYesOrNo($faceDetail[$ctr]['Eyeglasses']['Value']) . ', ' . $dataEyeglasses;

                if($faceDetail[$ctr]['Eyeglasses']['Value'] == 1)
                {
                    $withEGCount = $withEGCount + 1;
                } else {

                    $withoutEGCount = $withoutEGCount + 1;
                }
                

                $dataFaceOccluded = $this->convertToYesOrNo($faceDetail[$ctr]['FaceOccluded']['Value']) . ', ' . $dataFaceOccluded;
                
                if($faceDetail[$ctr]['FaceOccluded']['Value'] == 1)
                {
                    $occludedFaceCount = $occludedFaceCount + 1;
                } else {

                    $notOccludedFaceCount = $notOccludedFaceCount + 1;
                }

                $emotionsCtr =  (int) range(0, count($faceDetail[$ctr]['Emotions'])-1);

                $dataEmotions = $faceDetail[$ctr]['Emotions'][$emotionsCtr]['Type'] . ', ' . $dataEmotions;
                $dataSunglasses = $this->convertToYesOrNo($faceDetail[$ctr]['Sunglasses']['Value']) . ', ' . $dataSunglasses;

                if($faceDetail[$ctr]['Sunglasses']['Value'] == 1)
                {
                    $sunglassCount = $sunglassCount + 1;
                } else {

                    $noSunglassCount = $noSunglassCount + 1;
                }                

                $dataBeard = $this->convertToYesOrNo($faceDetail[$ctr]['Beard']['Value']) . ', ' . $dataBeard;

                if($faceDetail[$ctr]['Beard']['Value'] == 1)
                {
                    $beardCount = $beardCount + 1;
                } else {

                    $noBeardCount = $noBeardCount + 1;
                }  

                $dataMustache = $this->convertToYesOrNo($faceDetail[$ctr]['Mustache']['Value']) . ', ' . $dataMustache;

                if($faceDetail[$ctr]['Mustache']['Value'] == 1)
                {
                    $mustacheCount = $mustacheCount + 1;
                } else {

                    $noMustacheCount = $noMustacheCount + 1;
                }  

                $dataEyesOpen = $this->convertToYesOrNo($faceDetail[$ctr]['EyesOpen']['Value']) . ', ' . $dataEyesOpen;

                if($faceDetail[$ctr]['EyesOpen']['Value'] == 1)
                {
                    $openEyesCount = $openEyesCount + 1;
                } else {

                    $closeEyesCount = $closeEyesCount + 1;
                }  

                $dataMouthOpen = $this->convertToYesOrNo($faceDetail[$ctr]['MouthOpen']['Value']) . ', ' . $dataMouthOpen;

                if($faceDetail[$ctr]['MouthOpen']['Value'] == 1)
                {
                    $openMouthCount = $openMouthCount + 1;
                } else {

                    $closeMouthCount = $closeMouthCount + 1;
                }  

                $ctr++;
            }


            $dataAgeRangeLow = Str::substr($dataAgeRangeLow, 1, Str::length($dataAgeRangeLow)-3);
            $dataAgeRangeHigh = Str::substr($dataAgeRangeHigh, 0, Str::length($dataAgeRangeHigh)-2);
            $dataGender = Str::substr($dataGender, 0, Str::length($dataGender)-2);
            $dataSmile = Str::substr($dataSmile, 0, Str::length($dataSmile)-2);
            $dataEyeglasses = Str::substr($dataEyeglasses, 0, Str::length($dataEyeglasses)-2);
            $dataFaceOccluded = Str::substr($dataFaceOccluded, 0, Str::length($dataFaceOccluded)-2);
            $dataEmotions = Str::substr($dataEmotions, 0, Str::length($dataEmotions)-2);
            $dataSunglasses = Str::substr($dataSunglasses, 0, Str::length($dataSunglasses)-2);
            $dataBeard = Str::substr($dataBeard, 0, Str::length($dataBeard)-2);
            $dataMustache = Str::substr($dataMustache, 0, Str::length($dataMustache)-2);
            $dataEyesOpen = Str::substr($dataEyesOpen, 0, Str::length($dataEyesOpen)-2);
            $dataMouthOpen = Str::substr($dataMouthOpen, 0, Str::length($dataMouthOpen)-2);
            

            //dd($smilingCount);

            if($imgGeminiData == null){


                $promptTitle = 'Based on the following facial details or analysis:' . $imgAnalysisData . ', give us the best title for this fictional news article.';
                $promptContent = 'Based on the following facial details or analysis:' . $imgAnalysisData . 'create a fictional news article about it in 500 words. No need for a title.';
                
                
                $promptOAI = 'Based on the following analysis:' . $imgAnalysisData . ' , create a fictional news article about it in 500 words. Always separate title and content with a colon';

                try{
                    if(is_null($imgGeminiData) && !is_null($imgAnalysisData)) {
                        //$responseJson = $this->generateNewsOpenAI($promptOAI);
                        $responseJson = $this->genNews($imgAnalysisData);

                        // check if there is an error from OpenAI
                        if(Str::contains($responseJson, 'Error:')){

                            // use Gemini AI instead
                            $responseTitle = $this->generateNewsGemini($promptTitle);

                            if(Str::contains($responseTitle, 'Error:')){
                                // return error message about Gemini
                                $notification = array(
                                    'message' => 'Error encountered when using Gemini. Please try again later.' ,
                                    'alert-type' => 'warning'
                                );

                                return redirect()->back()->with($notification);

                            }


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

                    // use Gemini AI instead
                    $responseTitle = $this->generateNewsGemini($promptTitle);

                    if(Str::contains($responseTitle, 'Error:')){
                        // return error message about Gemini
                        $notification = array(
                            'message' => 'Error encountered when using Gemini. Please try again later.' ,
                            'alert-type' => 'warning'
                        );

                        return redirect()->back()->with($notification);

                    }
                    
                    
                    $responseContent = $this->generateNewsGemini($promptContent);

                    //save the News Article to DB from Gemini AI
                    DB::table('s3files')
                    ->where('s3files.id', '=', $id)
                    ->update(['img_chatgpt_title' => $responseTitle,
                            'img_chatgpt_content' => $responseContent,
                            ]);   
                            
                    // return error message about OpenAI
                    $notification = array(
                        'message' => 'Error connecting to OpenAI. Switching to Gemini AI.',
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
         'mouthopen'=>$dataMouthOpen,
         'facecount'=>$faceDataCount,
         'smilingcount'=>$smilingCount,
         'notsmilingcount'=>$notSmilingCount,
         'eyeglasscount'=>$withEGCount,
         'noeyeglasscount'=>$withoutEGCount,
         'occfacecount'=>$occludedFaceCount,
         'notoccfacecount'=>$notOccludedFaceCount,
         'sunglasscount'=>$sunglassCount,
         'nosunglasscount'=>$noSunglassCount,
         'beardcount'=>$beardCount,
         'nobeardcount'=>$noBeardCount,
         'mustachecount'=>$mustacheCount,
         'nomustachecount'=>$noMustacheCount,
         'malecount'=>$maleCount,
         'femalecount'=>$femaleCount,
         'openeyescount'=>$openEyesCount,
         'closeeyescount'=>$closeEyesCount,
         'openmouthcount'=>$openMouthCount,
         'closemouthcount'=>$closeMouthCount

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
        try{
            $response = Gemini::generateText($data);
        } catch (Exception $error) {
            return 'Error: ' . $error;
        }

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
