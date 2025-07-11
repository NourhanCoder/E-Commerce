@extends('website.layouts.master')
@section('title', 'Check-Out page')

@section('content')
    <!-- Header Content End -->

    <main>
      <section
        class="page-top d-flex justify-content-center align-items-center flex-column text-center"
      >
        <div class="page-top__overlay"></div>
        <div class="position-relative">
          <div class="page-top__title mb-3">
            <h2>إتمام الطلب</h2>
          </div>
          <div class="page-top__breadcrumb">
            <a class="text-gray" href="index.html">الرئيسية</a> /
            <span class="text-gray">إتمام الطلب</span>
          </div>
        </div>
      </section>

      @if ($errors->any())
    <div class="alert alert-danger mt-3">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif


      <section class="section-container my-5 py-5 d-lg-flex">
        <div class="checkout__form-cont w-50 px-3 mb-5">
          <h4>الفاتورة </h4>
          <form class="checkout__form" action="{{ route('checkout.store') }}" method="POST">
  @csrf

  <div class="mb-3">
    <label for="city">المدينة / المحافظة<span class="required">*</span></label>
    <select name="city" class="form__input bg-transparent" id="city">
      <option value="">اختر المدينة</option>
      <option value="القاهرة" {{ old('city') == 'القاهرة' ? 'selected' : '' }}>القاهرة</option>
      <option value="الجيزة" {{ old('city') == 'الجيزة' ? 'selected' : '' }}>الجيزة</option>
      <option value="القليوبية" {{ old('city') == 'القليوبية' ? 'selected' : '' }}>القليوبية</option>
      <option value="اسكندرية" {{ old('city') == 'اسكندرية' ? 'selected' : '' }}>اسكندرية</option>
    </select>
    @error('city')
      <div class="text-danger">{{ $message }}</div>
    @enderror
  </div>

  <div class="mb-3">
    <label for="street">العنوان بالكامل ( المنطقة - الشارع - رقم المنزل)<span class="required">*</span></label>
    <input
      name="street"
      class="form__input"
      placeholder="رقم المنزل أو الشارع / الحي"
      type="text"
      id="street"
      value="{{ old('street') }}"
    />
    @error('street')
      <div class="text-danger">{{ $message }}</div>
    @enderror
  </div>

  <div class="mb-3">
    <label for="phone">رقم الهاتف<span class="required">*</span></label>
    <input
      name="phone"
      class="form__input"
      type="text"
      id="phone"
      value="{{ old('phone') }}"
    />
    @error('phone')
      <div class="text-danger">{{ $message }}</div>
    @enderror
  </div>

  <div class="mb-3">
    <h2>معلومات إضافية</h2>
    <label for="note">ملاحظات الطلب (اختياري)</label>
    <textarea
      name="note"
      class="form__input"
      placeholder="ملاحظات حول الطلب، مثال: ملحوظة خاصة بتسليم الطلب."
      id="note"
    >{{ old('note') }}</textarea>
    @error('note')
      <div class="text-danger">{{ $message }}</div>
    @enderror
  </div>

  
<div class="mb-3">
  <label for="payment_method">طريقة الدفع<span class="required">*</span></label>
  <select name="payment_method" id="payment_method" class="form__input bg-transparent">
    <option value="">اختر طريقة الدفع</option>
    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>الدفع نقدا عند الاستلام</option>
    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>الدفع بالبطاقة</option>
  </select>
  @error('payment_method')
    <div class="text-danger">{{ $message }}</div>
  @enderror
</div>

  <button class="primary-button w-100 py-2">تأكيد الطلب</button>
</form>



        </div>
        <div class="checkout__order-details-cont w-50 px-3">
          <h4>طلبك</h4>
          <div>
  <table class="w-100 checkout__table">
    <thead>
      <tr class="border-0">
        <th>المنتج</th>
        <th>المجموع</th>
      </tr>
    </thead>
    <tbody>
      @php
          $total = 0;
          $originalTotal = 0;
      @endphp

      @forelse ($cart as $item)
        @php
            $itemTotal = $item['price'] * $item['quantity'];
            $total += $itemTotal;

            // حساب السعر الأصلي قبل الخصم (لو فيه خصم)
            $originalPrice = $item['price'] == $item['price'] ? $item['price'] : $item['original_price'] ?? $item['price'];
            $originalTotal += $originalPrice * $item['quantity'];
        @endphp

        <tr>
          <td>{{ $item['title'] }} × {{ $item['quantity'] }}</td>
          <td>
            <div class="product__price text-center d-flex gap-2 flex-wrap">
              @if (isset($item['original_price']) && $item['original_price'] > $item['price'])
                <span class="product__price product__price--old">
                  {{ number_format($item['original_price'] * $item['quantity'], 2) }} جنيه
                </span>
              @endif
              <span class="product__price">
                {{ number_format($itemTotal, 2) }} جنيه
              </span>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="2" class="text-center text-danger">لا توجد منتجات في السلة</td>
        </tr>
      @endforelse

      <tr>
        <th>المجموع</th>
        <td class="fw-bolder">{{ number_format($total, 2) }} جنيه</td>
      </tr>

      @if ($originalTotal > $total)
        <tr class="bg-green">
          <th>قمت بتوفير</th>
          <td class="fw-bolder">{{ number_format($originalTotal - $total, 2) }} جنيه</td>
        </tr>
      @endif

      <tr>
        <th>الإجمالي</th>
        <td class="fw-bolder">{{ number_format($total, 2) }} جنيه</td>
      </tr>
    </tbody>
  </table>
</div>



    </main>
@endsection
   

