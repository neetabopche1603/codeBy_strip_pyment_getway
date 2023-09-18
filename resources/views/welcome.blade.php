<!doctype html>
<html lang="en">

<head>
    <title>Show all Plans</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<style>
    body {
        padding: 2rem 0rem;
    }
</style>

<body>
    {{-- <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="text-center"> Show Plans</h3>
            </div>
            <div class="card-body">
                <form action="">
                    <div class="row">
                        <div class="col-md-4">

                        </div>

                        <div class="col-md-4">
                            
                        </div>

                        <div class="col-md-4">
                            
                        </div>
                    </div>

                </form>
            </div>
        </div>
        </div>
    </div> --}}
    <div class="container">
        @include('alertMessages')
        @auth
            <a href="{{ route('logout') }}" class="float-lg-right"
                onclick="return confirm('Are your sure logout this site')">logout</a>
        @else
            <a href="{{ route('registerView') }}" class="float-lg-right">Register</a>
        @endauth
        <h3 class="text-success text-center mt-4 mb-2">Show All Plans</h3>
        <hr>
        <form action="">
            <div class="row">
                @foreach ($plans as $plan)
                    <div class="col-4 col-sm-8 col-md-6 col-lg-4">
                        <div class="card text-center">
                            <div class="card-header text-center border-bottom-0 bg-transparent text-success pt-4">
                                <h5>Pay as You Go</h5>
                            </div>
                            <div class="card-body">
                                <h1>${{ $plan->total_payment }}</h1>
                                <h5 class="text-muted"><small>{{ $plan->plan_description }}</small></h5>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><i
                                        class="fas fa-male text-success mx-2"></i>{{ $plan->plan_name }}</li>
                            </ul>
                            <div class="card-footer border-top-0">
                                {{-- <a href="#" class="text-muted text-uppercase">Purchase  <i class="fas fa-arrow-right"></i></a> --}}

                                @if (auth()->user())
                                    @php
                                        $current_date = date('Y-m-d H:i:s');
                                    @endphp
                                    @if (auth()->user()->plan_id != '' && auth()->user()->plan_end_date > $current_date)
                                        @if (auth()->user()->plan_id == $plan->id)
                                            <a href="javascript:void(0)"
                                                class="btn btn-secondary btn-sm text-light text-uppercase"
                                                disabled>Purchased</a>
                                        @else
                                            <a href="javascript:void(0)"
                                                class="btn btn-primary btn-sm text-light text-uppercase"
                                                onclick="return alert('Your other plan already activated.')">Purchase</a>
                                        @endif
                                    @else
                                        <a href="{{ route('checkout', ['id' => $plan->id]) }}"
                                            class="btn btn-primary btn-sm text-light text-uppercase">Purchase</a>
                                    @endif
                                @else
                                    <a href="{{ route('checkout', ['id' => $plan->id]) }}"
                                        class="btn btn-primary btn-sm text-light text-uppercase">Purchase</a>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach

            </div>


        </form>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
