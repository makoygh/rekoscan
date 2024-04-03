<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    

                    <div class="row  flex-grow-1 flex lg:justify-center" >
					<div class="col-12 col-md-8 grid-margin stretch-card">
            <div class="card">
              <div class="card-body" >

								<h6 class="card-title text-white">RekoScan Image Upload</h6>

								<form class="forms-sample" method="POST" action="{{ route('s3.upload')}}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
										<label for="imgName" class="form-label text-white">Image Name</label>
										<input type="text" name="imgName" 
                                        class="form-control @error('imgName') is-invalid @enderror" id="imgName" autocomplete="off" >
                                        @error('imgName')
                                            <span class='text-danger'>{{ $message }}</span>
                                        @enderror
									</div>

                                <div class="mb-3">
										<label for="fileName" class="form-label text-white">Upload File</label>

                                        <input class="form-control  @error('fileName') is-invalid @enderror" type="file" id="fileName" name="fileName" >
                                        @error('fileName')
                                            <span class='text-danger'>{{ $message }}</span>
                                        @enderror
									</div>     
									<button type="submit" class="btn btn-primary me-2">Upload</button>

								</form>

              </div>
            </div> <!-- end of form -->


                </div>
            </div>

             
        </div>
    </div>
    <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                        Developed by Team RekoScan | Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </footer>  
</x-app-layout>
