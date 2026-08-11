<div>
    <input wire:model.live.debounce.300ms="search"
    class="nav__search-input w-100" 
    type="search" 
    placeholder="أبحث هنا عن اي شئ تريده...">
     <span class="nav__search-icon">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
    <!-- قائمة عرض النتائج المنسدلة -->
    @if(mb_strlen($search) >= 2)
        <div class="position-absolute start-0 end-0 bg-white shadow rounded-bottom mt-1 z-3 overflow-hidden" style="top: 100%;">
            @if(count($results) > 0)
                <ul class="list-group list-group-flush m-0 p-0">
                    @foreach($results as $product)
                        <li class="list-group-item list-group-item-action">
                            <a href="{{ route('single.page', $product->id) }}" class="d-flex align-items-center gap-3 text-decoration-none text-dark">
                                <div>
                                    <div class="fw-bold">{{ $product->title }}</div>
                                    <small class="text-muted">{{ $product->price }} جنيه</small>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-3 text-center text-muted">
                    لا توجد نتائج تطابق "{{ $search }}"
                </div>
            @endif
        </div>
    @endif
</div>
