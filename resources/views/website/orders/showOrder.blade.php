@extends('website.layouts.master')

@section('title', 'Order Details')

@section('content')
<main>
  <section class="page-top d-flex justify-content-center align-items-center flex-column text-center">
    <div class="page-top__overlay"></div>
    <div class="position-relative">
      <div class="page-top__title mb-3">
        <h2>تتبع طلبك</h2>
      </div>
      <div class="page-top__breadcrumb">
        <a class="text-gray" href="{{ route('home.page') }}">الرئيسية</a> /
        <span class="text-gray">تتبع طلبك</span>
      </div>
    </div>
  </section>

  <section class="section-container my-5 py-5">
    <p>
      تم تقديم الطلب #{{ $order->id }} في {{ \Carbon\Carbon::parse($order->created_at)->locale('ar')->isoFormat('D MMMM YYYY') }} وهو الآن بحالة <strong>{{ $order->status_text }}</strong>.
    </p>

    <section>
      <h2>تفاصيل الطلب</h2>
      <table class="success__table w-100 mb-5">
        <thead>
          <tr class="border-0 bg-danger text-white">
            <th>المنتج</th>
            <th class="d-none d-md-table-cell">الإجمالي</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($order->orderItems as $item)
            <tr>
              <td>
                <div>
                  <a href="{{ route('orders.show', $item->product->id) }}">
                    {{ $item->product->title }}
                  </a> × {{ $item->quantity }}
                </div>
                {{-- لو في ألوان وأحجام مثلاً تخزن في المنتج أو في pivot، اعرضهم هنا --}}
              </td>
              <td>{{ number_format($item->price_at_order * $item->quantity, 2) }} جنيه</td>
            </tr>
          @endforeach
          <tr>
            <th>المجموع:</th>
            <td class="fw-bolder">{{ number_format($order->total_price, 2) }} جنيه</td>
          </tr>
        </tbody>
      </table>
    </section>

    <section class="mb-5">
      <h2>عنوان الفاتورة</h2>
      <div class="border p-3 rounded-3">
        <p class="mb-1">{{ $order->address->user->name ?? 'غير معروف' }}</p>
        <p class="mb-1">{{ $order->address->street }}</p>
        <p class="mb-1">{{ $order->address->city }}</p>
        <p class="mb-1">{{ $order->address->phone }}</p>
        <p class="mb-1">{{ $order->address->user->email ?? 'غير معروف' }}</p>
      </div>
    </section>

    <section class="mb-5">
      <h2>طريقة الدفع</h2>
      <p>{{ $order->payment->payment_method_text }}</p>

    </section>
  </section>
</main>
@endsection