@extends('website.layouts.master')

@section('title', 'Confirmation Page')

@section('content')
  <main>
  <!-- الهيدر -->
  @if (session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger mt-3">
        {{ session('error') }}
    </div>
@endif

  <section class="page-top d-flex justify-content-center align-items-center flex-column text-center">
    <div class="page-top__overlay"></div>
    <div class="position-relative">
      <div class="page-top__title mb-3">
        <h2>حسابي</h2>
      </div>
      <div class="page-top__breadcrumb">
        <a class="text-gray" href="{{ route('home.page') }}">الرئيسية</a> /
        <span class="text-gray">تم استلام الطلب</span>
      </div>
    </div>
  </section>

  <!-- رسالة الشكر -->
  <section class="section-container profile my-5 py-5">
    <div class="text-center mb-5">
      <div class="success-gif m-auto">
        <img class="w-100" src="{{ asset('assets/images/success.gif') }}" alt="تم بنجاح" />
      </div>
      <h4 class="mb-4">جاري تجهيز طلبك الآن</h4>
      <p class="mb-1">سيقوم أحد ممثلي خدمة العملاء بالتواصل معك لتأكيد الطلب</p>
      <p>برجاء الرد على الأرقام الغير مسجلة</p>
      <a href="{{ route('home.page') }}" class="primary-button">تصفح منتجات أخرى</a>
      @if ($order->status === 'pending')
    <form action="{{ route('checkout.cancel', $order->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء الطلب؟')">
        @csrf
        @method('PATCH')
        <button class="btn btn-danger mt-3">إلغاء الطلب</button>
    </form>
@endif

    </div>

    <!-- ملخص الطلب -->
    <div>
      <p>شكرًا لك. تم استلام طلبك.</p>
      <div class="d-flex flex-wrap gap-2">
        <div class="success__details">
          <p class="success__small">رقم الطلب:</p>
          <p class="fw-bolder">{{ $order->id }}</p>
        </div>
        <div class="success__details">
          <p class="success__small">التاريخ:</p>
          <p class="fw-bolder">{{ $order->created_at->format('Y-m-d') }}</p>
        </div>
        <div class="success__details">
          <p class="success__small">البريد الإلكتروني:</p>
          <p class="fw-bolder">{{ $order->user->email }}</p>
        </div>
        <div class="success__details">
          <p class="success__small">الإجمالي:</p>
          <p class="fw-bolder">{{ number_format($order->total_price, 2) }} جنيه</p>
        </div>
      </div>
    </div>
  </section>

  <!-- تفاصيل المنتجات -->
  <section class="section-container">
    <h2>تفاصيل الطلب</h2>
    <table class="success__table w-100 mb-5">
      <thead>
        <tr class="border-0 bg-main text-white">
          <th>المنتج</th>
          <th>الإجمالي</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($order->orderItems as $item)
          <tr>
            <td>
              <div>{{ $item->product->title }} × {{ $item->quantity }}</div>
            </td>
            <td>{{ number_format($item->price_at_order * $item->quantity, 2) }} جنيه</td>
          </tr>
        @endforeach
        <tr>
          <th>الإجمالي:</th>
          <td class="fw-bold">{{ number_format($order->total_price, 2) }} جنيه</td>
          <p class="mb-1">
          <span class="fw-bold">طريقة الدفع:</span>
          {{ $order->payment->payment_method == 'cash' ? 'الدفع نقدا عند الاستلام' : 'الدفع بالبطاقة' }}
          </p>

        </tr>
      </tbody>
    </table>
  </section>

  <!-- عنوان الفاتورة -->
  <section class="section-container mb-5">
    <h2>عنوان الفاتورة</h2>
    <div class="border p-3 rounded-3">
      <p class="mb-1">{{ $order->user->name }}</p>
      <p class="mb-1">{{ $address->street }}</p>
      <p class="mb-1">{{ $address->city }}</p>
      <p class="mb-1">{{ $address->phone }}</p>
      <p class="mb-1">{{ $order->user->email }}</p>
      @if ($address->note)
        <p class="mb-1">ملاحظة: {{ $address->note }}</p>
      @endif
    </div>
  </section>
</main>
@endsection
