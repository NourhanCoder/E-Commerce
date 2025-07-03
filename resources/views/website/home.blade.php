@extends('website.layouts.master')
@section('title', 'Home page')

@section('content')
    <!-- Page Content Start -->
    <main class="pt-4">
        <!-- Hero Section Start -->
        @if ($sliderBooks->isEmpty())
            <p class="text-center py-5">لا توجد كتب في السلايدر حاليًا.</p>
        @else
            <section class="section-container hero">
                <div class="custom-slider">
                    @foreach ($sliderBooks->chunk(2) as $chunk)
                        @php $chunk = $chunk->values(); @endphp

                        <div class="slide justify-content-center align-items-center">
                            {{-- الكتاب الأول --}}
                            @if ($chunk->get(0))
                                <div class="book book-left">
                                    <img src="{{ $chunk[0]->image() }}" alt="{{ $chunk[0]->title }}">
                                </div>
                            @endif

                            <div class="new-arrival">وصل حديثًا</div>

                            {{-- الكتاب الثاني (لو موجود) --}}
                            @if ($chunk->get(1))
                                <div class="book book-right">
                                    <img src="{{ $chunk[1]->image() }}" alt="{{ $chunk[1]->title }}">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
        <!-- Hero Section End -->

        <!-- ===== CSS ===== -->
        <style>
            .custom-slider {
                display: flex;
                overflow: hidden;
                width: 100%;
                max-width: 1200px;
                margin: auto;
                position: relative;
            }

            .slide {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
                min-width: 100%;
                min-height: 300px;
                transition: transform 0.5s ease-in-out;
                flex-shrink: 0;
                border: 2px solid #ddd;
                border-radius: 15px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                background-color: #f9f9f9;
                padding: 20px;
                gap: 30px;
            }

            .book {
                width: 30%;
                max-width: 200px;
            }

            .book img {
                width: 100%;
                height: auto;
                border-radius: 10px;
                border: 2px solid #ccc;
            }

            .new-arrival {
                font-size: 30px;
                font-weight: bold;
                text-align: center;
                white-space: nowrap;
                color: #333;
            }
        </style>

        <!-- ===== Script ===== -->
        <script>
            let index = 0;
            const slides = document.querySelectorAll('.slide');
            const totalSlides = slides.length;

            function showSlides() {
                slides.forEach((slide) => {
                    slide.style.display = 'none';
                });

                slides[index].style.display = 'flex';
                index = (index + 1) % totalSlides;
            }

            showSlides();
            setInterval(showSlides, 3000);
        </script>

        <!-- Offer Section Start -->
        @if ($discounts->isNotEmpty())
            <section class="section-container mb-5 mt-3">
                <div class="offer d-flex flex-column flex-md-row align-items-md-center justify-content-between rounded-3 p-3 text-white"
                    style="background-color: #1f3b57">

                    {{-- قائمة الكتب --}}
                    <div class="mb-3 mb-md-0">
                        <label class="fw-bold mb-2">العروض المتاحة: يمكنك اختيار كتاب لمعرفة الوقت المتبقي للخصم</label>
                        <select id="discountSelector" class="form-select">
                            @foreach ($discounts as $discount)
                                <option
                                    value="{{ \Carbon\Carbon::parse($discount->end_date)->setTimezone('Africa/Cairo')->toIso8601String() }}"
                                    data-title="{{ $discount->product->title }}"
                                    data-link="{{ route('single.page', $discount->product->id) }}"
                                    data-percent="{{ $discount->percentage }}">
                                    {{ $discount->product->title }} (خصم {{ $discount->percentage }}%)
                                </option>
                            @endforeach
                        </select>

                        {{-- زر يفتح صفحة الكتاب --}}
                        <div class="mt-2">
                            <a id="bookLink" href="{{ route('single.page', $discounts[0]->product->id) }}"
                                class="btn btn-warning btn-sm">
                                عرض تفاصيل الكتاب
                            </a>
                        </div>
                    </div>

                    {{-- التايمر --}}
                    <div class="offer__time d-flex gap-2 fs-6 countdown text-center" id="main-countdown"
                        data-end="{{ \Carbon\Carbon::parse($discounts[0]->end_date)->setTimezone('Africa/Cairo')->toIso8601String() }}">

                        <div class="d-flex flex-column align-items-center">
                            <span class="fw-bolder days">--</span>
                            <div>أيام</div>
                        </div>:
                        <div class="d-flex flex-column align-items-center">
                            <span class="fw-bolder hours">--</span>
                            <div>ساعات</div>
                        </div>:
                        <div class="d-flex flex-column align-items-center">
                            <span class="fw-bolder minutes">--</span>
                            <div>دقائق</div>
                        </div>:
                        <div class="d-flex flex-column align-items-center">
                            <span class="fw-bolder seconds">--</span>
                            <div>ثواني</div>
                        </div>
                    </div>
                </div>
            </section>
        @else
            <section class="section-container mb-5 mt-3 text-center">
                <div class="alert alert-secondary">لا توجد عروض حالية</div>
            </section>
        @endif

        <!-- Countdown Script -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let countdownElement = document.getElementById('main-countdown');
                let selector = document.getElementById('discountSelector');
                let linkButton = document.getElementById('bookLink');
                let interval;

                function startCountdown(endTimeStr) {
                    const endTime = new Date(endTimeStr);

                    function updateCountdown() {
                        const now = new Date();
                        const distance = endTime - now;

                        if (distance <= 0) {
                            countdownElement.innerHTML = "<div class='text-danger'>انتهى العرض</div>";
                            clearInterval(interval);
                            return;
                        }

                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        countdownElement.querySelector('.days').textContent = String(days).padStart(2, '0');
                        countdownElement.querySelector('.hours').textContent = String(hours).padStart(2, '0');
                        countdownElement.querySelector('.minutes').textContent = String(minutes).padStart(2, '0');
                        countdownElement.querySelector('.seconds').textContent = String(seconds).padStart(2, '0');
                    }

                    clearInterval(interval);
                    updateCountdown();
                    interval = setInterval(updateCountdown, 1000);
                }

                // تغيير العرض عند تغيير الاختيار
                selector.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    const newEnd = selected.value;
                    const newLink = selected.dataset.link;

                    // تغيير التايمر
                    startCountdown(newEnd);

                    // تغيير لينك الكتاب
                    linkButton.href = newLink;
                });

                // تشغيل أول تايمر تلقائيًا
                startCountdown(selector.value);
            });
        </script>
        <!-- Offer Section End -->

        <!-- Products Section Start -->
        <section class="section-container mb-4">
            <div class="owl-carousel products__slider owl-theme">
                @foreach ($products as $product)
                    @php
                        $discount = $product->active_discount;
                    @endphp

                    <div class="products__item">
                        <div class="product__header mb-3">
                            <a href="{{ route('single.page', $product->id) }}">
                                <div class="product__img-cont">
                                    <img class="product__img w-100 h-100 object-fit-cover" src="{{ $product->image() }}"
                                        alt="{{ $product->title }}">
                                </div>
                            </a>

                            @if ($discount)
                                <div
                                    class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white bg-danger small">
                                    وفر {{ $discount->percentage }}%
                                </div>
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

                        </div>

                        <div class="product__title text-center">
                            <a class="text-black text-decoration-none" href="{{ route('single.page', $product->id) }}">
                                {{ $product->title }}
                            </a>
                        </div>

                        <div class="product__author text-center">
                            {{ $product->author ?? '-' }}
                        </div>

                        <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                            @if ($discount)
                                <span class="product__price product__price--old">
                                    {{ $product->price }} جنيه
                                </span>
                                <span class="product__price text-danger fw-bold">
                                    {{ $product->discounted_price }} جنيه
                                </span>
                            @else
                                <span class="product__price">
                                    {{ $product->price }} جنيه
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
        </section>
        <!-- Categories Section End -->

        <!-- Best Sales Section Start -->
        <section class="section-container mb-5">
            <div class="products__header mb-4 d-flex align-items-center justify-content-between">
                <h4 class="m-0">الاكثر مبيعا</h4>
                <button class="products__btn py-2 px-3 rounded-1">تسوق الأن</button>
            </div>
            <div class="owl-carousel products__slider owl-theme">
                <div class="products__item">
                    <div class="product__header mb-3">
                        <a href="single-product.html">
                            <div class="product__img-cont">
                                <img class="product__img w-100 h-100 object-fit-cover" src="assets/images/product-1.webp"
                                    data-id="white">
                            </div>
                        </a>
                        <div class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white">
                            وفر 10%
                        </div>
                        <div
                            class="product__favourite position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center bg-white">
                            <i class="fa-regular fa-heart"></i>
                        </div>
                    </div>
                    <div class="product__title text-center">
                        <a class="text-black text-decoration-none" href="single-product.html">
                            Flutter Apprentice
                        </a>
                    </div>
                    <div class="product__author text-center">
                        Mike Katz
                    </div>
                    <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                        <span class="product__price product__price--old">
                            550.00 جنيه
                        </span>
                        <span class="product__price">
                            350.00 جنيه
                        </span>
                    </div>
                </div>
                <div class="products__item">
                    <div class="product__header mb-3">
                        <a href="single-product.html">
                            <div class="product__img-cont">
                                <img class="product__img w-100 h-100 object-fit-cover" src="assets/images/product-2.webp"
                                    data-id="white">
                            </div>
                        </a>
                        <div class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white">
                            وفر 10%
                        </div>
                        <div
                            class="product__favourite position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center bg-white">
                            <i class="fa-regular fa-heart"></i>
                        </div>
                    </div>
                    <div class="product__title text-center">
                        <a class="text-black text-decoration-none" href="single-product.html">
                            Modern Full-Stack Development
                        </a>
                    </div>
                    <div class="product__author text-center">
                        Frank Zammetti
                    </div>
                    <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                        <span class="product__price product__price--old">
                            450.00 جنيه
                        </span>
                        <span class="product__price">
                            250.00 جنيه
                        </span>
                    </div>
                </div>
                <div class="products__item">
                    <div class="product__header mb-3">
                        <a href="single-product.html">
                            <div class="product__img-cont">
                                <img class="product__img w-100 h-100 object-fit-cover" src="assets/images/product-3.webp"
                                    data-id="white">
                            </div>
                        </a>
                        <div class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white">
                            وفر 10%
                        </div>
                        <div
                            class="product__favourite position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center bg-white">
                            <i class="fa-regular fa-heart"></i>
                        </div>
                    </div>
                    <div class="product__title text-center">
                        <a class="text-black text-decoration-none" href="single-product.html">
                            C# 10 in a Nutshell
                        </a>
                    </div>
                    <div class="product__author text-center">
                        Joseph Albahari
                    </div>
                    <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                        <span class="product__price product__price--old">
                            650.00 جنيه
                        </span>
                        <span class="product__price">
                            450.00 جنيه
                        </span>
                    </div>
                </div>
                <div class="products__item">
                    <div class="product__header mb-3">
                        <a href="single-product.html">
                            <div class="product__img-cont">
                                <img class="product__img w-100 h-100 object-fit-cover" src="assets/images/product-4.webp"
                                    data-id="white">
                            </div>
                        </a>
                        <div class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white">
                            وفر 10%
                        </div>
                        <div
                            class="product__favourite position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center bg-white">
                            <i class="fa-regular fa-heart"></i>
                        </div>
                    </div>
                    <div class="product__title text-center">
                        <a class="text-black text-decoration-none" href="single-product.html">
                            Algorithms عربي
                        </a>
                    </div>
                    <div class="product__author text-center">
                        Aditya Y. Bhargava
                    </div>
                    <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                        <span class="product__price product__price--old">
                            359.00 جنيه
                        </span>
                        <span class="product__price">
                            249.00 جنيه
                        </span>
                    </div>
                </div>
                <div class="products__item">
                    <div class="product__header mb-3">
                        <a href="single-product.html">
                            <div class="product__img-cont">
                                <img class="product__img w-100 h-100 object-fit-cover" src="assets/images/product-5.webp"
                                    data-id="white">
                            </div>
                        </a>
                        <div class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white">
                            وفر 10%
                        </div>
                        <div
                            class="product__favourite position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center bg-white">
                            <i class="fa-regular fa-heart"></i>
                        </div>
                    </div>
                    <div class="product__title text-center">
                        <a class="text-black text-decoration-none" href="single-product.html">
                            Head-First Design Patterns
                        </a>
                    </div>
                    <div class="product__author text-center">
                        Eric Freeman & Elisabeth Robson
                    </div>
                    <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                        <span class="product__price product__price--old">
                            550.00 جنيه
                        </span>
                        <span class="product__price">
                            350.00 جنيه
                        </span>
                    </div>
                </div>
                <div class="products__item">
                    <div class="product__header mb-3">
                        <a href="single-product.html">
                            <div class="product__img-cont">
                                <img class="product__img w-100 h-100 object-fit-cover" src="assets/images/product-1.webp"
                                    data-id="white">
                            </div>
                        </a>
                        <div class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white">
                            وفر 10%
                        </div>
                        <div
                            class="product__favourite position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center bg-white">
                            <i class="fa-regular fa-heart"></i>
                        </div>
                    </div>
                    <div class="product__title text-center">
                        <a class="text-black text-decoration-none" href="single-product.html">
                            Flutter Apprentice
                        </a>
                    </div>
                    <div class="product__author text-center">
                        Mike Katz
                    </div>
                    <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                        <span class="product__price product__price--old">
                            550.00 جنيه
                        </span>
                        <span class="product__price">
                            350.00 جنيه
                        </span>
                    </div>
                </div>
                <div class="products__item">
                    <div class="product__header mb-3">
                        <a href="single-product.html">
                            <div class="product__img-cont">
                                <img class="product__img w-100 h-100 object-fit-cover" src="assets/images/product-2.webp"
                                    data-id="white">
                            </div>
                        </a>
                        <div class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white">
                            وفر 10%
                        </div>
                        <div
                            class="product__favourite position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center bg-white">
                            <i class="fa-regular fa-heart"></i>
                        </div>
                    </div>
                    <div class="product__title text-center">
                        <a class="text-black text-decoration-none" href="single-product.html">
                            Modern Full-Stack Development
                        </a>
                    </div>
                    <div class="product__author text-center">
                        Frank Zammetti
                    </div>
                    <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                        <span class="product__price product__price--old">
                            450.00 جنيه
                        </span>
                        <span class="product__price">
                            250.00 جنيه
                        </span>
                    </div>
                </div>
                <div class="products__item">
                    <div class="product__header mb-3">
                        <a href="single-product.html">
                            <div class="product__img-cont">
                                <img class="product__img w-100 h-100 object-fit-cover" src="assets/images/product-3.webp"
                                    data-id="white">
                            </div>
                        </a>
                        <div class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white">
                            وفر 10%
                        </div>
                        <div
                            class="product__favourite position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center bg-white">
                            <i class="fa-regular fa-heart"></i>
                        </div>
                    </div>
                    <div class="product__title text-center">
                        <a class="text-black text-decoration-none" href="single-product.html">
                            C# 10 in a Nutshell
                        </a>
                    </div>
                    <div class="product__author text-center">
                        Joseph Albahari
                    </div>
                    <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                        <span class="product__price product__price--old">
                            650.00 جنيه
                        </span>
                        <span class="product__price">
                            450.00 جنيه
                        </span>
                    </div>
                </div>
                <div class="products__item">
                    <div class="product__header mb-3">
                        <a href="single-product.html">
                            <div class="product__img-cont">
                                <img class="product__img w-100 h-100 object-fit-cover" src="assets/images/product-4.webp"
                                    data-id="white">
                            </div>
                        </a>
                        <div class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white">
                            وفر 10%
                        </div>
                        <div
                            class="product__favourite position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center bg-white">
                            <i class="fa-regular fa-heart"></i>
                        </div>
                    </div>
                    <div class="product__title text-center">
                        <a class="text-black text-decoration-none" href="single-product.html">
                            Algorithms عربي
                        </a>
                    </div>
                    <div class="product__author text-center">
                        Aditya Y. Bhargava
                    </div>
                    <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                        <span class="product__price product__price--old">
                            359.00 جنيه
                        </span>
                        <span class="product__price">
                            249.00 جنيه
                        </span>
                    </div>
                </div>
                <div class="products__item">
                    <div class="product__header mb-3">
                        <a href="single-product.html">
                            <div class="product__img-cont">
                                <img class="product__img w-100 h-100 object-fit-cover" src="assets/images/product-5.webp"
                                    data-id="white">
                            </div>
                        </a>
                        <div class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white">
                            وفر 10%
                        </div>
                        <div
                            class="product__favourite position-absolute top-0 end-0 m-1 rounded-circle d-flex justify-content-center align-items-center bg-white">
                            <i class="fa-regular fa-heart"></i>
                        </div>
                    </div>
                    <div class="product__title text-center">
                        <a class="text-black text-decoration-none" href="single-product.html">
                            Head-First Design Patterns
                        </a>
                    </div>
                    <div class="product__author text-center">
                        Eric Freeman & Elisabeth Robson
                    </div>
                    <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                        <span class="product__price product__price--old">
                            550.00 جنيه
                        </span>
                        <span class="product__price">
                            350.00 جنيه
                        </span>
                    </div>
                </div>
            </div>
        </section>
        <!-- Best Sales Section End -->

        <!-- Newest Section Start -->
        <section class="section-container mb-5">
            <div class="products__header mb-4 d-flex align-items-center justify-content-between">
                <h4 class="m-0">وصل حديثا</h4>
                <button class="products__btn py-2 px-3 rounded-1">تسوق الآن</button>
            </div>

            <div class="owl-carousel products__slider owl-theme">
                @foreach ($latestProducts as $product)
                    <div class="products__item">
                        <div class="product__header mb-3 position-relative">
                            <a href="{{ route('single.page', $product->id) }}">
                                <div class="product__img-cont">
                                    <img class="product__img w-100 h-100 object-fit-cover" src="{{ $product->image() }}"
                                        alt="{{ $product->title }}">
                                </div>
                            </a>

                            @if ($product->active_discount)
                                <div
                                    class="product__sale position-absolute top-0 start-0 m-1 px-2 py-1 rounded-1 text-white bg-danger small">
                                    وفر {{ $product->active_discount->percentage }}%
                                </div>
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
                        </div>

                        <div class="product__title text-center">
                            <a class="text-black text-decoration-none" href="{{ route('single.page', $product->id) }}">
                                {{ $product->title }}
                            </a>
                        </div>

                        <div class="product__author text-center">
                            {{ $product->author ?? '-' }}
                        </div>

                        <div class="product__price text-center d-flex gap-2 justify-content-center flex-wrap">
                            @if ($product->active_discount)
                                <span class="product__price product__price--old text-muted">
                                    {{ $product->price }} جنيه
                                </span>
                                <span class="product__price text-danger fw-bold">
                                    {{ $product->discounted_price }} جنيه
                                </span>
                            @else
                                <span class="product__price fw-bold">
                                    {{ $product->price }} جنيه
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        <!-- Newest Section End -->
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


    </main>
    <!-- Page Content End -->
@endsection
