<!doctype html>
<html lang="en">
  <head>
    <title>Register</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  </head>
  <body>
    <div class="container">
        <div class="card mt-4 w-75">
            @include('alertMessages')
           <div class="card-header"> <h3 class="text-center">Register</h3>
            <a href="{{route('login')}}" class="btn btn-success btn-sm float-l-right">Login</a>
        </div>
         
           <div class="card-body">
            <form action="{{route('registerPost')}}" method="post">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <input type="text" name="name" value="{{old('name')}}" class="form-control" placeholder="Enter Your Name" required>
                        <span class="text-danger">
                            @error('name')
                                {{$message}}
                            @enderror
                        </span>
                    </div>

                    <div class="col-6">
                        <input type="email" name="email" value="{{old('email')}}" class="form-control"placeholder="Enter Your Email" class="form-control" required>
                        <span class="text-danger">
                            @error('email')
                                {{$message}}
                            @enderror
                        </span>
                    </div>

                    <div class="col-6 mt-2">
                        <input type="password" name="password" value="" class="form-control" placeholder="Enter Your Password" required>
                        <span class="text-danger">
                            @error('password')
                                {{$message}}
                            @enderror
                        </span>
                    </div>

                    <div class="col-6 mt-2">
                        <input type="password" name="confirm_password" value="" placeholder="Enter Your confirm Password" class="form-control" required>
                        <span class="text-danger">
                            @error('confirm_password')
                                {{$message}}
                            @enderror
                        </span>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary btn-sm mt-2" value="Register">
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