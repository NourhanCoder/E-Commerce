@extends('website.layouts.master')

@section('title', 'All Orders')

@section('content')
<main>
  <section class="page-top d-flex justify-content-center align-items-center flex-column text-center ">
    <div class="page-top__overlay"></div>
    <div class="position-relative">
      <div class="page-top__title mb-3">
        <h2>حسابي</h2>
      </div>
      <div class="page-top__breadcrumb">
        <a class="text-gray" href="{{ route('home.page') }}">الرئيسية</a> /
        <span class="text-gray">حسابي</span>
      </div>
    </div>
  </section>

  <section class="section-container profile my-3 my-md-5 py-5 d-md-flex gap-5">
    <div class="profile__right">
      <!-- قائمة التنقل الجانبية -->
      
    </div>

    <div class="profile__left mt-4 mt-md-0 w-100">
      <div class="profile__tab-content orders active">
        @if ($orders->isEmpty())
          <div class="orders__none d-flex justify-content-between align-items-center py-3 px-4">
            <p class="m-0">لم يتم تنفيذ أي طلب بعد.</p>
            <a href="{{ route('home.page') }}" class="primary-button">تصفح المنتجات</a>
          </div>
        @else
          <table class="orders__table w-100">
            <thead>
              <tr>
                <th class="d-none d-md-table-cell">الطلب</th>
                <th class="d-none d-md-table-cell">التاريخ</th>
                <th class="d-none d-md-table-cell">الحالة</th>
                <th class="d-none d-md-table-cell">الإجمالي</th>
                <th class="d-none d-md-table-cell">إجراءات</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($orders as $order)
                <tr class="order__item">
                  <td class="d-flex justify-content-between d-md-table-cell">
                    <div class="fw-bolder d-md-none">الطلب:</div>
                    <div><a href="#">#{{ $order->id }}</a></div>
                  </td>
                  <td class="d-flex justify-content-between d-md-table-cell">
                    <div class="fw-bolder d-md-none">التاريخ:</div>
                    <div>{{ \Carbon\Carbon::parse($order->created_at)->locale('ar')->isoFormat('D MMMM YYYY') }}
                    </div>
                  </td>
                  <td class="d-flex justify-content-between d-md-table-cell">
                    <div class="fw-bolder d-md-none">الحالة:</div>
                    <div>{{ $order->status_text }}</div>
                  </td>
                  <td class="d-flex justify-content-between d-md-table-cell">
                    <div class="fw-bolder d-md-none">الإجمالي:</div>
                    <div>{{ number_format($order->total_price, 2) }} جنيه</div>
                  </td>
                  <td class="d-flex justify-content-between d-md-table-cell">
                    <div class="fw-bolder d-md-none">إجراءات:</div>
                    <div>
                      <a class="primary-button" href="{{ route('orders.show', $order->id) }}">عرض</a>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  </section>
</main>
@endsection