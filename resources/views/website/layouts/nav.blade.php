<nav class="nav">
    
    <div class="section-container w-100 d-flex align-items-center gap-4 h-100">
        <div class="nav__categories-btn align-items-center justify-content-center rounded-1 d-none d-lg-flex">
            <button class="border-0 bg-transparent" data-bs-toggle="offcanvas" data-bs-target="#nav__categories">
                <i class="fa-solid fa-align-center fa-rotate-180"></i>
            </button>
        </div>
        <div class="nav__logo">
            <a href="{{ route('home.page') }}">
                <img class="img-fluid" style="max-height: 80px;" src="{{ asset('assets/images/logo4.png') }}"
                    alt="">
            </a>
        </div>
        <div class="nav__search w-100">
            <input class="nav__search-input w-100" type="search" placeholder="أبحث هنا عن اي شئ تريده...">
            <span class="nav__search-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
        </div>
        <ul class="nav__links gap-3 list-unstyled d-none d-lg-flex m-0">
            <li class="nav__link nav__link-user">
                <a class="d-flex align-items-center gap-2">
                    حسابي
                    <i class="fa-regular fa-user"></i>
                    <i class="fa-solid fa-chevron-down fa-2xs"></i>
                </a>
                @auth {{-- المستخدم مسجل دخول --}}
                    <ul class="nav__user-list position-absolute p-0 list-unstyled bg-white">
                        <li class="nav__link nav__user-link"><a href="profile.html">لوحة التحكم</a></li>
                        <li class="nav__link nav__user-link"><a href="{{ route('orders.index') }}">الطلبات</a></li>
                        <li class="nav__link nav__user-link"><a href="account_details.html">تفاصيل الحساب</a></li>
                        <li class="nav__link nav__user-link"><a href="favourites.html">المفضلة</a></li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="nav__link nav__user-link" type="submit">تسجيل الخروج</button>
                        </form>
                    </ul>
                @endauth
            </li>
            @guest {{-- المستخدم مش مسجل دخول --}}
                <li class="nav__link">
                    <a class="d-flex align-items-center gap-2" href="{{ route('login') }}">
                        تسجيل الدخول
                        <i class="fa-regular fa-user"></i>
                    </a>
                </li>
            @endguest
            <li class="nav__link">
                <a class="d-flex align-items-center gap-2" href="{{ route('favourites.page') }}">
                    المفضلة
                    <div class="position-relative">
                        <i class="fa-regular fa-heart"></i>
                        <div class="nav__link-floating-icon">
                        @auth
                          {{ auth()->user()->favouriteProducts()->count() }}
                        @else
                         0
                        @endauth
                    </div>
                    </div>
                </a>
            </li>
            <li class="nav__link">
                <a class="d-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#nav__cart">
                    عربة التسوق
                    <div class="position-relative">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <div class="nav__link-floating-icon">
                        {{ $cartCount }}
                    </div>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <div class="nav-mobile fixed-bottom d-block d-lg-none">
        <ul class="nav-mobile__list d-flex justify-content-around gap-2 list-unstyled  m-0 border-top">
            <li class="nav-mobile__link">
                <a class="d-flex align-items-center flex-column gap-1 text-decoration-none"
                    href="{{ route('home.page') }}">
                    <i class="fa-solid fa-house"></i>
                    الرئيسية
                </a>
            </li>
            <li class="nav-mobile__link d-flex align-items-center flex-column gap-1" data-bs-toggle="offcanvas"
                data-bs-target="#nav__categories">
                <i class="fa-solid fa-align-center fa-rotate-180"></i>
                الاقسام
            </li>
            <li class="nav-mobile__link d-flex align-items-center flex-column gap-1">
                <a class="d-flex align-items-center flex-column gap-1 text-decoration-none" href="profile.html">
                    <i class="fa-regular fa-user"></i>
                    حسابي
                </a>
            </li>
            <li class="nav-mobile__link d-flex align-items-center flex-column gap-1">
                <a class="d-flex align-items-center flex-column gap-1 text-decoration-none" href="favourites.html">
                    <i class="fa-regular fa-heart"></i>
                    المفضلة
                </a>
            </li>
           
{{-- <li class="nav-mobile__link d-flex align-items-center flex-column gap-1" data-bs-toggle="offcanvas"
    data-bs-target="#nav__cart">
    <i class="fa-solid fa-cart-shopping position-relative">
        @if($cartCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  style="font-size: 10px;">
                {{ $cartCount }}
            </span>
        @endif
    </i>
    السلة
</li> --}}


        </ul>
        <!--  -->
    </div>
</nav>

<div class="nav__categories offcanvas offcanvas-start px-4 py-2" tabindex="-1" id="nav__categories"
    aria-labelledby="nav__categories">
    <div class="nav__categories-header offcanvas-header justify-content-end">
        <button type="button" class="border-0 bg-transparent text-danger nav__close" data-bs-dismiss="offcanvas"
            aria-label="Close">
            <i class="fa-solid fa-x fa-1x fw-light"></i>
        </button>
    </div>
    <div class="nav__categories-body offcanvas-body pt-0">
        <div class="nav__side-logo mb-2">
            <img class="w-100" src="{{ asset('assets/images/logo4.png') }}" alt="">
        </div>
        <ul class="nav__list list-unstyled">
    @foreach ($categories as $mainCategory)
        <li class="nav__link nav__side-link">
            <a class="d-flex justify-content-between align-items-center py-3" data-bs-toggle="collapse" href="#cat-{{ $mainCategory->id }}" role="button" aria-expanded="false" aria-controls="cat-{{ $mainCategory->id }}">
                {{ $mainCategory->name }}
                @if ($mainCategory->children->count() > 0)
                    <i class="fa fa-chevron-down"></i>
                @endif
            </a>

            @if ($mainCategory->children->count() > 0)
                <ul class="collapse list-unstyled ps-3" id="cat-{{ $mainCategory->id }}">
                    @foreach ($mainCategory->children as $subCategory)
                        <li>
                            <a href="{{ route('products.byCategory', $subCategory->id) }}" class="py-2 d-block">
                                {{ $subCategory->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>
    </div>
</div>

<div class="nav__cart offcanvas offcanvas-end px-3 py-2" tabindex="-1" id="nav__cart" aria-labelledby="nav__cart">
    <div class="nav__categories-header offcanvas-header align-items-center">
        <h5>سلة التسوق</h5>
        <button type="button" class="border-0 bg-transparent text-danger nav__close" data-bs-dismiss="offcanvas"
            aria-label="Close">
            <i class="fa-solid fa-x fa-1x fw-light"></i>
        </button>
    </div>

    <div class="nav__categories-body offcanvas-body pt-4">
        @if (count($cart) > 0)
            <div class="cart-products">
                <ul class="nav__list list-unstyled">
                    @php $total = 0; @endphp
                    @foreach ($cart as  $productId => $item)
                        @php
                            $total += $item['price'] * $item['quantity'];
                        @endphp
                        <li class="cart-products__item d-flex justify-content-between gap-2 align-items-center">
                            <div class="d-flex gap-2 align-items-center">
                                <div>
                                    <form method="POST" action="{{ route('cart.remove', $productId) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="cart-products__remove btn btn-sm btn-danger">x</button>
                                    </form>
                                </div>
                                <div>
                                    <p class="cart-products__name m-0 fw-bolder">{{ $item['title'] }}</p>
                                    <p class="cart-products__price m-0">
                                        <button class="btn btn-sm btn-outline-secondary quantity-decrease"
                                            data-product-id="{{  $productId }}">-</button>

                                        <span class="product-quantity mx-1"
                                            data-product-id="{{  $productId }}">{{ $item['quantity'] }}</span>

                                        <button class="btn btn-sm btn-outline-secondary quantity-increase"
                                            data-product-id="{{  $productId }}">+</button>

                                        x <span class="product-price">{{ $item['price'] }}</span> جنيه
                                    </p>
                                </div>
                            </div>
                            <div class="cart-products__img" style="width: 70px">
                                <img class="w-100" src="{{ $item['image'] }}" alt="{{ $item['title'] }}">
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="d-flex justify-content-between mt-3">
                    <p class="fw-bolder">المجموع:</p>
                    <p class="cart-total-amount">{{ $total }} جنيه</p>
                </div>
            </div>

            @if(auth()->check())
    {{-- لو المستخدم مسجل دخول، يروح على صفحة إتمام الطلب --}}
    <a href="{{ route('checkout.index') }}" class="nav__cart-btn text-center text-white w-100 border-0 mb-3 py-2 px-3 bg-success">
        اتمام الطلب
    </a>
@else
    {{-- لو مش مسجل دخول، يروح على صفحة تسجيل الدخول --}}
    <a href="{{ route('login') }}" class="nav__cart-btn text-center text-white w-100 border-0 mb-3 py-2 px-3 bg-success">
        تسجيل الدخول لإتمام الطلب
    </a>
@endif

            {{-- <button class="nav__cart-btn text-center w-100 py-2 px-3 bg-transparent">تابع التسوق</button> --}}
        @else
            <p>لا توجد منتجات في سلة المشتريات.</p>
        @endif
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // زر + و -
        document.querySelectorAll('.quantity-increase, .quantity-decrease').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.dataset.productId;
                const action = this.classList.contains('quantity-increase') ? 'increase' : 'decrease';

                fetch(`/cart/update/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ action })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`.product-quantity[data-product-id="${productId}"]`).innerText = data.newQuantity;
                        document.querySelector('.cart-total-amount').innerText = data.total + ' جنيه';
                    }
                });
            });
        });
    });
</script>


