@extends('website.layouts.master')
@section('title', 'Favourite page')

@section('content')

    <main>
      <div class="my-5 py-5">
        <section class="section-container favourites">
          <table class="w-100">
            <thead>
              <th class="d-none d-md-table-cell"></th>
              <th class="d-none d-md-table-cell"></th>
              <th class="d-none d-md-table-cell">الاسم</th>
              <th class="d-none d-md-table-cell">السعر</th>
              <th class="d-none d-md-table-cell">تاريخ الاضافه</th>
              <th class="d-none d-md-table-cell">المخزون</th>
              <th class="d-table-cell d-md-none">product</th>
            </thead>
            <tbody class="text-center">
              @forelse ($favourites as $fav)
    @php
        $product = $fav->product;
        $discount = $product->active_discount;
    @endphp
    <tr>
        <td class="d-block d-md-table-cell">
        <span class="favourites__remove m-auto" data-product-id="{{ $product->id }}" style="cursor: pointer;">
    <i class="fa-solid fa-xmark text-danger"></i>
</span>
        </td>
        <td class="d-block d-md-table-cell favourites__img">
            <img src="{{ $product->image() }}" alt="{{ $product->title }}">
        </td>
        <td class="d-block d-md-table-cell">
            <a href="{{ route('single.page', $product->id) }}">{{ $product->title }}</a>
        </td>
        <td class="d-block d-md-table-cell">
            @if($discount)
                <span class="product__price product__price--old">{{ $product->price }} جنيه</span>
                <span class="product__price text-danger fw-bold">{{ $product->discounted_price }} جنيه</span>
            @else
                <span class="product__price">{{ $product->price }} جنيه</span>
            @endif
        </td>
        <td class="d-block d-md-table-cell">{{ $fav->created_at->translatedFormat('F d, Y') }}</td>
        <td class="d-block d-md-table-cell">
            <span class="me-2"><i class="fa-solid fa-check"></i></span>
            <span class="d-inline-block d-md-none d-lg-inline-block">متوفر بالمخزون</span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6">لا توجد منتجات في المفضلة.</td>
    </tr>
@endforelse

            </tbody>
          </table>
          @if ($favourites->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $favourites->links() }}
    </div>
@endif

          
        </section>
      </div>
      <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.favourites__remove').forEach(btn => {
            btn.addEventListener('click', function () {
                const confirmed = confirm('هل أنت متأكد أنك تريد إزالة هذا المنتج من المفضلة؟');
                if (!confirmed) return;
                
                const productId = this.dataset.productId;
                const row = this.closest('tr');

                fetch(`/favourites/toggle/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(res => res.json())
                .then(data => {
                     if (data.status === 'removed') {
        // احذف الصف من الجدول
        row.remove();

        // تحديث العداد
        const favCount = document.querySelector('.nav__link-floating-icon');
        if (favCount) favCount.innerText = data.count ?? 0;

        // لو مفيش صفوف تانية، أضف سطر "لا توجد منتجات"
        const tableBody = document.querySelector('tbody');
        if (tableBody && tableBody.children.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="6">لا توجد منتجات في المفضلة.</td>
            `;
            tableBody.appendChild(emptyRow);
        }
    }
})
                .catch(err => console.error('Error:', err));
            });
        });
    });
</script>

    </main>
@endsection

  

