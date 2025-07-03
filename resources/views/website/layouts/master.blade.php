<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title')</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/images/logo4.png') }}" >
  <link rel="stylesheet" href="{{asset('assets/css/vendors/all.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/vendors/bootstrap.rtl.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/vendors/owl.carousel.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/vendors/owl.theme.default.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/css/main.min.css')}}">
</head>

<body>
  <!-- Header Content Start -->
  <div>
    <div class="header-container fixed-top border-bottom">
      <header>
        <div class="section-container d-flex justify-content-between">
          <div class="header__email d-flex gap-2 align-items-center">
            <i class="fa-regular fa-envelope"></i>
            support@bookstore.com
          </div>
          <div class="header__info d-none d-lg-block">
            شحن مجاني للطلبات 💥 عند الشراء ب 699ج او اكثر
          </div>
          <div class="header__branches d-flex gap-2 align-items-center">
            <a class="text-white text-decoration-none" href="branches.html">
              <i class="fa-solid fa-location-dot"></i>
              فروعنا  
            </a>
          </div>
        </div>
      </header>
      <!--    -->
      @include('website.layouts.nav')
    </div>


    <!-- News Content Start -->
    <section class="sales text-center p-2 d-block d-lg-none">
      شحن مجاني للطلبات 💥 عند الشراء ب 699ج او اكثر
    </section>
    <!-- News Content End -->
  </div>
  <!-- Header Content End -->

  <!-- Page Content Start -->
  @yield('content')
  <!-- Page Content End -->

  <!-- Footer Section Start -->
  @include('website.layouts.footer')
  <!-- Footer Section End -->

  <script src="{{asset('assets/js/vendors/all.min.js')}}"></script>
  <script src="{{asset('assets/js/vendors/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('assets/js/vendors/jquery-3.7.0.js')}}"></script>
  <script src="{{asset('assets/js/vendors/owl.carousel.min.js')}}"></script>
  <script src="{{asset('assets/js/main.js')}}"></script>
  <script src="{{asset('assets/js/app.js')}}"></script>
</body>

</html>