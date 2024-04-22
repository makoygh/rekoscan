<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!--{{ __("You're logged in!") }}-->
                    

                    <div class="page-content">

<nav class="page-breadcrumb">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">Upload New Image</a>
</nav>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
<div class="card">
<div class="card-body">
<h6 class="card-title text-white">All Uploaded Images</h6>

<div class="table-responsive">
  <table id="dataTableExample" class="table">
    <thead>
      <tr>
        <th>Image Name</th>
        <th>File Name</th>
        <th>Date Uploaded</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    @foreach($uploadedImages as $key => $item)
      <tr>
        <td>{{ $item->img_name }}</td>
        <td>{{ $item->img_filename }}</td>
        <td>{{ $item->created_at }}</td>
        <td>
        <a href="{{ route('view.image',$item->id) }}" class="btn btn-inverse-warning">View Facial Analysis and News</a>
        <!--<a href="{{ route('view.news',$item->id) }}"  class="btn btn-inverse-danger" target=”_blank”>View News</a>-->

        </td>
      </tr>
    @endforeach 
    </tbody>
  </table>
</div>
</div>
</div>
    </div>
</div>

</div> <!-- end of page -->


                </div>
             
            </div>
            <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                        Developed by Team RekoScan | Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </footer>   

        </div>
    </div>


</x-app-layout>
