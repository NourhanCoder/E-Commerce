@extends('website.layouts.master')

@section('title', 'Products in ' . $category->name)

@section('content')
<div class="container" style="margin-top: 130px;">

<h2 class="mb-4 text-center fw-bold" >
    {{ $category->name }}
    @if($category->parent)
        <small class="text-muted">({{ $category->parent->name }})</small>
    @endif
</h2>


    @if ($products->count())
        <div class="row">
            @foreach ($products as $product)
                <div class="col-md-2 col-sm-4 col-6 mb-4"> 
                    <div class="card h-100 position-relative shadow-sm" style="font-size: 14px;">
                        
                        {{-- علامة الخصم --}}
                        @php
                            $discount = $product->discount()
                                ->where('start_date', '<=', now())
                                ->where('end_date', '>=', now())
                                ->first();
                        @endphp
                        @if ($discount)
                            <span class="badge bg-danger position-absolute top-0 start-0 m-1" style="z-index: 2;">
                                -{{ $discount->percentage }}%
                            </span>
                        @endif

                        {{-- صورة الكتاب --}}
                        @if ($product->image)
                            <img src="{{ $product->image() }}"
                                 alt="{{ $product->title }}"
                                 class="card-img-top"
                                 style="height: 180px; object-fit: contain; padding: 10px;">
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

                        {{-- التفاصيل --}}
                        <div class="card-body text-center p-2">
                            <h6 class="card-title">{{ Str::limit($product->title, 25) }}</h6>
                            <p class="card-text text-muted small">{{ Str::limit($product->description, 40) }}</p>

                            {{-- السعر مع أو بدون خصم --}}
                            @if ($product->discounted_price < $product->price)
                                <p class="fw-bold mb-2">
                                    <span class="text-danger">{{ $product->discounted_price }} جنيه</span>
                                    <span class="text-decoration-line-through text-muted small">{{ $product->price }} جنيه</span>
                                </p>
                            @else
                                <p class="fw-bold mb-2">{{ $product->price }} جنيه</p>
                            @endif

                            {{-- زر التفاصيل --}}
                            <div class="mt-2">
                           <a href="{{ route('single.page', $product->id) }}" class="btn btn-primary btn-sm w-100 mb-2 ">
                                عرض التفاصيل
                            </a>

                            <form method="POST" action="{{ route('cart.add', $product->id) }}">
                              @csrf
                               <input type="hidden" name="quantity" value="1">
                             <button type="submit" class="btn btn-success btn-sm w-100">أضف إلى السلة</button>
                            </form>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @else
        <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
            <div class="text-center">
                <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد منتجات في هذا التصنيف حتى الآن.</h5>
            </div>
        </div>
    @endif
</div>

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
