<!doctype html>
<html lang="en">
  <head>
    <title>Login</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  </head>
  <body>
    <div class="container mt-4 w-50">
        <div class="card">
            @include('alertMessages')
           <div class="card-header"> <h3 class="text-center">Login</h3>
            <a href="{{route('registerView')}}" class="btn btn-success btn-sm float-l-right">Register</a>
        </div>
         
           <div class="card-body">
            <form action="{{route('loginPost')}}" method="post">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <input type="email" name="email" value="{{old('email')}}" class="form-control"placeholder="Enter Your Email" class="form-control" required>
                        <span class="text-danger">
                          @error('email')
                              {{$message}}
                          @enderror
                      </span>
                    </div>

                    <div class="col-12 mt-2">
                        <input type="password" name="password" value="" class="form-control" placeholder="Enter Your Password" required>
                        <span class="text-danger">
                          @error('password')
                              {{$message}}
                          @enderror
                      </span>
                    </div>
                </div>
                <input type="submit" value="Login"  class="btn btn-primary btn-sm mt-2">
            </div>
              
            </form>
           </div>
        </div>
    </div>
      
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  </body>
</html>