@extends('clients.layouts.app')

@section('content')
    <div class="container py-4" style="margin-top: 150px;">
        <h4 class="mb-4">Thông báo đơn hàng</h4>

        @forelse($orders as $order)
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body d-flex align-items-start">
                    <!-- Ảnh sản phẩm đầu tiên -->
                    @php
                        $firstProduct = $order->orderDetails->first();
                        $productImage =
                            $firstProduct && $firstProduct->product && $firstProduct->product->image
                                ? asset('storage/' . $firstProduct->product->image)
                                : asset('clients/img/no-image.png');
                    @endphp

                    <img src="{{ $productImage }}" alt="Ảnh sản phẩm" class="me-3 rounded"
                        style="width: 80px; height: 80px; object-fit: cover;">

                    <!-- Nội dung -->
                    <div class="flex-grow-1">
                        <p class="mb-1">
                            Đơn hàng <strong>#{{ $order->order_code }}</strong> của bạn {{ $order->status_text }}.
                        </p>


                        <small class="text-muted">
                            {{ $order->updated_at ? \Carbon\Carbon::parse($order->updated_at)->format('H:i d-m-Y') : '' }}
                        </small>
                    </div>

                    <!-- Link xem chi tiết -->
                    <a href="{{ route('clients.orderdetail', $order->id) }}" class="btn btn-sm btn-outline-primary">
                        Xem chi tiết
                    </a>

                </div>
            </div>
        @empty
            <p>Bạn chưa có thông báo đơn hàng nào.</p>
        @endforelse

        <!-- Phân trang -->
        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    </div>
@endsection

<style>
    .pagination {
        display: flex !important;
        flex-direction: row !important;
        justify-content: center;
        gap: 6px;
        padding-left: 0;
        margin: 0;
    }

    .page-item {
        list-style: none;
    }

    .page-link {
        border-radius: 6px;
        padding: 6px 12px;
    }
</style>
