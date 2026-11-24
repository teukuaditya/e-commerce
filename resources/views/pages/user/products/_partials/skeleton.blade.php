{{-- Partial: skeleton / shimmer loader untuk grid produk.
     Dipakai selama request AJAX (filter kategori, search, pagination) berlangsung.
     Strukturnya meniru kartu produk agar tidak terjadi layout shift. --}}
@for ($i = 0; $i < 8; $i++)
<div class="col-6 col-sm-6 col-md-3 p-b-35 product-grid-item product-skeleton" aria-hidden="true">
    <div class="block2">
        <div class="block2-pic">
            <span class="skeleton-pic shimmer"></span>
        </div>

        <div class="block2-txt flex-w flex-t p-t-14">
            <div class="block2-txt-child1 flex-col-l w-100">
                <span class="skeleton-line shimmer" style="width: 38%; height: 13px;"></span>
                <span class="skeleton-line shimmer" style="width: 88%; height: 16px; margin-top: 9px;"></span>
                <span class="skeleton-line shimmer" style="width: 52%; height: 14px; margin-top: 11px;"></span>
            </div>
        </div>
    </div>
</div>
@endfor
