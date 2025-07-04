@extends('website.layouts.master')

@section('title', $product->title)

@section('content')
    <div class="container pt-5 mt-5 mb-5">
        <div class="row align-items-center">
            <!-- صورة المنتج -->
            <div class="col-md-4 text-center mt-4 mb-md-0 position-relative">
                @if ($product->image)
                    <img src="{{ $product->image() }}" alt="{{ $product->title }}" class="img-fluid rounded shadow"
                        style="max-height: 400px; object-fit: cover;">
                @endif
                <div class="toggle-favourite position-absolute top-0 start-0 m-2 rounded-circle bg-white d-flex justify-content-center align-items-center"
                    data-product-id="{{ $product->id }}"
                    style="width: 35px; height: 35px; cursor: pointer; left: 0 !important; right: auto !important;">
                    <i class="fa-heart
            @auth
             {{ auth()->user()->favourites->where('product_id', $product->id)->count() ? 'fa-solid text-danger' : 'fa-regular' }}
            @else
             fa-regular @endauth"></i>
                </div>
                @if ($product->active_discount)
                    <!-- شارة الخصم -->
                    <div class="badge bg-danger position-absolute top-0 start-0 m-2 px-2 py-1 fs-6">
                        خصم {{ $product->active_discount->percentage }}%
                    </div>
                @endif

            </div>



            <!-- تفاصيل المنتج -->
            <div class="col-md-8">
                <h2 class="mb-3">{{ $product->title }}</h2>

                <p class="text-muted mb-2">
                    التصنيف:
                    {{ $product->category->name ?? '-' }}
                    @if ($product->category && $product->category->parent)
                        ({{ $product->category->parent->name }})
                    @endif
                </p>

                <div class="product-description mt-4">
                    <div style="direction: ltr; text-align: left; font-size: 16px; line-height: 1.6;">
                        <p class="mb-4">{{ $product->description }}</p>
                    </div>
                </div>

                <!-- السعر -->
                @if ($product->active_discount)
                    <h4 class="fw-bold mb-3">
                        <span class="text-danger">{{ $product->discounted_price }} جنيه</span>
                        <del class="text-muted ms-2">{{ $product->price }} جنيه</del>
                    </h4>
                @else
                    <h4 class="fw-bold mb-3">{{ $product->price }} جنيه</h4>
                @endif
                <form method="POST" action="{{ route('cart.add', $product->id) }}">
                  @csrf
                 <button type="submit" class="btn btn-success">أضف إلى السلة</button>
               </form>
            </div>
        </div>
    </div>
    {{-- <pre>{{ print_r(session('cart'), true) }}</pre> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-favourite').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const productId = this.dataset.productId;
                    const icon = this.querySelector('svg, i'); // دعم لـ i أو svg

                    @if (auth()->check())
                        fetch(`/favourites/toggle/${productId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (!icon) return;

                                // لو SVG أو i، هنغير لونه يدويًا
                                if (data.status === 'added') {
                                    // القلب اتحط في المفضلة
                                    this.classList.add('bg-white');
                                    this.classList.remove('bg-white');
                                    icon.classList.remove('fa-regular');
                                    icon.classList.add('fa-solid');
                                    icon.style.color = 'red';
                                } else {
                                    // القلب اتشال من المفضلة
                                    this.classList.remove('bg-danger');
                                    this.classList.add('bg-white');
                                    icon.classList.remove('fa-solid');
                                    icon.classList.add('fa-regular');
                                    icon.style.color = 'black';
                                }



                                const favCount = document.querySelector(
                                    '.nav__link-floating-icon');
                                if (favCount) favCount.innerText = data.count ?? 0;
                            });
                    @else
                        window.location.href = '{{ route('login') }}';
                    @endif
                });
            });
        });
    </script>

@endsection
