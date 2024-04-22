<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                   

                <div class="page-content">



<div class="row profile-body flex lg:justify-center">
<!-- left wrapper start -->
@foreach($imageData as $key => $item)


<nav class="col-md-8 col-xl-10  left-wrapper">
    <a href="{{ route('view.news',$item->id) }}" class="btn btn-outline-primary" target=”_blank”>View News</a>
</nav>
<div class="mt-3">
          <!-- spacer -->
 </div>
<div class="col-md-8 col-xl-6 left-wrapper">

    <div class="row">
      <div class="col-md-12 grid-margin">
        <div class="card rounded">
          <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <img class="img-xs rounded-circle" src="{{ asset('../img/rekoscan_logo_icon.png') }}" class="block h-9 w-auto fill-current " alt="">													
                <div class="ms-2 text-white">
                  <p>RekoScan News</p>

                  <p class="tx-11 text-muted">1 min ago</p>
                </div>
              </div>
              <div class="dropdown">
                <a type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal icon-lg pb-3px"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                  <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-meh icon-sm me-2"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="15" x2="16" y2="15"></line><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg> <span class="">Unfollow</span></a>
                  <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-corner-right-up icon-sm me-2"><polyline points="10 9 15 4 20 9"></polyline><path d="M4 20h7a4 4 0 0 0 4-4V4"></path></svg> <span class="">Go to post</span></a>
                  <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-share-2 icon-sm me-2"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg> <span class="">Share</span></a>
                  <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy icon-sm me-2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg> <span class="">Copy link</span></a>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body  text-white" align='center'>
          <p class="mb-3 tx-16 text-white">{{ $item->img_chatgpt_title }}</p>
            <p class="mb-3 tx-14 text-white">&nbsp;</p>
            <img class="img-fluid" src="{{ asset('upload/s3/'.$item->img_localfile) }}"   alt="">
            <p class="mb-3 tx-14 text-white">&nbsp;</p>
            <p  class="mb-3 tx-14 text-white">{{ $item->img_chatgpt_content }}</p>
          </div>
          <div class="card-footer">
          <!--<label class="tx-11 fw-bolder mb-0 text-uppercase text-white">JSON Output</label>-->
            <div class="d-flex post-actions">
            
            </div>
            <div class="mt-3">
              <!--<p class="text-muted">{{ $item->img_analysis }}</p>-->
            </div>

          </div>
        </div>
      </div>

    </div>

    <!-- JSON OUTPUT -->
   
  </div>
  @endforeach 

  <!-- left wrapper end -->    
 

  <!-- middle wrapper start -->
  <div class="d-none d-md-block col-md-8 col-xl-4 middle-wrapper">
    <div class="card rounded">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h6 class="card-title mb-0 text-white">Facial Analysis Results</h6>
          <div class="dropdown">
            <a type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal icon-lg text-muted pb-3px"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 icon-sm me-2"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg> <span class="">Edit</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-git-branch icon-sm me-2"><line x1="6" y1="3" x2="6" y2="15"></line><circle cx="18" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><path d="M18 9a9 9 0 0 1-9 9"></path></svg> <span class="">Update</span></a>
              <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye icon-sm me-2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> <span class="">View all</span></a>
            </div>
          </div>
        </div>
        <img class="img-fluid  w-200 h-20" src="{{ asset('upload/s3/'.$item->img_localfile) }}" alt="">
        <p class="text-white">Image Name: {{ $item->img_name }}</p>

        <div class="mt-3">
          <label class="tx-11 fw-bolder mb-0 text-uppercase text-white">Age Range/s:</label>
          @if ($facecount > 1)
          <p class="text-muted">Low: {{ $agelow }} </p>
          <p class="text-muted">High: {{ $agehigh }}</p>
          @else
          <p class="text-muted">Between {{ $agelow }} to {{ $agehigh }}</p>
          @endif
        </div>        
        <div class="mt-3">
          <label class="tx-11 fw-bolder mb-0 text-uppercase text-white">Gender/s:</label>
          @if ($facecount > 1)
          <p class="text-muted">There are {{ $malecount }} males in the group.</p>
          <p class="text-muted">Female: {{ $femalecount }}</p>          
          
          @else
          <p class="text-muted">{{ $gender }}</p>
          @endif
        </div>
        <div class="mt-3">
          <label class="tx-11 fw-bolder mb-0 text-uppercase text-white">Smile/s:</label>
          @if ($facecount > 1)
          <p class="text-muted">There are {{ $smilingcount }} people smiling. </p>
          <p class="text-muted">Not Smiling: {{ $notsmilingcount }}</p>     
          @else
          <p class="text-muted">{{  $smile }}</p>
          @endif
        </div>
        <div class="mt-3">
          <label class="tx-11 fw-bolder mb-0 text-uppercase text-white">With Eyeglass/es:</label>
          @if ($facecount > 1)
          <p class="text-muted">There are {{ $eyeglasscount }} people wearing eyeglasses. </p>
          <p class="text-muted">Not Wearing Eyeglass/es: {{ $noeyeglasscount }}</p>   
          @else
          <p class="text-muted">{{ $eyeglasses }}</p>
          @endif
        </div>
        <div class="mt-3">
          <label class="tx-11 fw-bolder mb-0 text-uppercase text-white">With Sunglass/es:</label>
          @if ($facecount > 1)
          <p class="text-muted">There are {{ $sunglasscount }} people wearing sunglasses. </p>
          <p class="text-muted">Not Wearing Sunglass/es: {{ $nosunglasscount }}</p>  
          @else
          <p class="text-muted">{{ $sunglasses }}</p>
          @endif
        </div>
        <div class="mt-3">
          <label class="tx-11 fw-bolder mb-0 text-uppercase text-white">Face Occluded:</label>
          @if ($facecount > 1)
          <p class="text-muted">There are {{ $occfacecount }} people that faces are occluded. </p>
          <p class="text-muted">Face Not Occluded: {{ $notoccfacecount }}</p> 
          @else
          <p class="text-muted">{{ $faceoccluded }}</p>
          @endif
        </div>
        <div class="mt-3">
          <label class="tx-11 fw-bolder mb-0 text-uppercase text-white">With Beard:</label>
          @if ($facecount > 1)
          <p class="text-muted">There are {{ $beardcount }} people with beard. </p>
          <p class="text-muted">Without Beard: {{ $nobeardcount }}</p> 
          @else
          <p class="text-muted">{{ $beard }}</p>
          @endif
        </div>     
        <div class="mt-3">
          <label class="tx-11 fw-bolder mb-0 text-uppercase text-white">With Mustache:</label>
          @if ($facecount > 1)
          <p class="text-muted">There are {{ $mustachecount }} people with mustache. </p>
          <p class="text-muted">Without Mustache: {{ $nomustachecount }}</p> 
          @else
          <p class="text-muted">{{ $mustache }}</p>
          @endif
        </div> 
        <div class="mt-3">
          <label class="tx-11 fw-bolder mb-0 text-uppercase text-white">Eyes Open:</label>
          @if ($facecount > 1)
          <p class="text-muted">There are {{ $openeyescount }} people with eyes open. </p>
          <p class="text-muted">Eyes Close: {{ $closeeyescount }}</p> 
          @else
          <p class="text-muted">{{ $eyesopen }}</p>
          @endif
        </div>      
        <div class="mt-3">
          <label class="tx-11 fw-bolder mb-0 text-uppercase text-white">Mouth Open:</label>
          @if ($facecount > 1)
          <p class="text-muted">There are {{ $openmouthcount }} peope with mouth open.</p>
          <p class="text-muted">Close Mouth: {{ $closemouthcount }}</p> 
          @else          
          <p class="text-muted">{{ $mouthopen }}</p>
          @endif
        </div>   
        <div class="mt-3">
          <label class="tx-11 fw-bolder mb-0 text-uppercase text-white">Emotions:</label>
          <p class="text-muted">Emotions varies like {{ $emotions }}</p>
        </div>
        
        <div class="mt-3 d-flex social-links">
          <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-github"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
          </a>
          <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-twitter"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
          </a>
          <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
          </a>
        </div>
        <div class="mt-3">
          <!--<label class="tx-11 fw-bolder mb-0 text-uppercase text-white">JSON Output</label>
          <p class="text-muted">{{ $item->img_analysis }}</p>-->
        </div>
      </div>
    </div>
  </div>
  <!-- middle wrapper end -->

  
  

<!--</div>-->

 <!-- JSON OUTPUT -->
 <div class="mt-3">
          <!-- spacer -->
 </div>
 <div class="col-md-8 col-xl-10  left-wrapper">
 <div class="row">
      <div class="col-md-12 grid-margin">
        <div class="card rounded">
          <div class="card-header">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <img class="img-xs rounded-circle" src="{{ asset('../img/rekoscan_logo_icon.png') }}" class="block h-9 w-auto fill-current " alt="">													
                <div class="ms-2 text-white">
                  <p>RekoScan - JSON Output</p>

                  <p class="tx-11 text-muted">1 min ago</p>
                </div>
              </div>
              <div class="dropdown">
                <a type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal icon-lg pb-3px"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                  <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-meh icon-sm me-2"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="15" x2="16" y2="15"></line><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg> <span class="">Unfollow</span></a>
                  <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-corner-right-up icon-sm me-2"><polyline points="10 9 15 4 20 9"></polyline><path d="M4 20h7a4 4 0 0 0 4-4V4"></path></svg> <span class="">Go to post</span></a>
                  <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-share-2 icon-sm me-2"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg> <span class="">Share</span></a>
                  <a class="dropdown-item d-flex align-items-center" href="javascript:;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-copy icon-sm me-2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg> <span class="">Copy link</span></a>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body text-muted" >
          <p>{{ $item->img_analysis }}</p>

          </div>
          <div class="card-footer">
            <div class="d-flex post-actions">
            
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- end of JSON Output-->

    </div> <!-- end of profile body -->

    </div> <!-- end of page -->


                </div>
            </div>
            <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                        Developed by Team RekoScan | Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </footer>               
        </div>
    </div>
</x-app-layout>
