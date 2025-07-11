@extends('website.layouts.master')

@section('title', $product->title)

@section('content')
  <div class="container" style="margin-top: 130px;">
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

                <p>



                <!-- حالة التوفر -->
        <div class="mb-3">
             <strong>الحالة:</strong>
             
            @if ($product->stock > 0)
                <span class="badge bg-primary">متوفر</span>
           @else
              <span class="badge bg-danger">غير متوفر</span>
           @endif

           @if ($product->stock <= 5 && $product->stock > 0)
             <p class="text-danger fw-bold mt-2">تبقّى {{ $product->stock }} فقط في المخزون!</p>
          @endif

        </div>
               @if ($product->stock > 0)
    <form method="POST" action="{{ route('cart.add', $product->id) }}">
        @csrf

        <div class="mb-3 d-flex align-items-center gap-2">
            <label class="form-label mb-0">الكمية:</label>

            <div class="input-group input-group-sm" style="width: 120px;">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="decreaseQty()">-</button>
                <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" readonly class="form-control text-center">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="increaseQty({{ $product->stock }})">+</button>
            </div>
        </div>

        <button type="submit" class="btn btn-success ">أضف إلى السلة</button>
    </form>

    <script>
        function increaseQty(max) {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function decreaseQty() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }
    </script>
@else
    <button class="btn btn-secondary btn-sm" disabled>غير متوفر حالياً</button>
@endif


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
