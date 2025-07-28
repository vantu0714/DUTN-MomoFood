@include('clients.layouts.header')
@include('clients.layouts.sidebar')
<link rel="stylesheet" href="{{ asset('clients/css/shop.css') }}">


<br><br><br><br><br>

<style>
    .news-section {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .section-title {
        text-align: center;
        font-size: 32px;
        font-weight: bold;
        color: #ef4444;
        margin-bottom: 40px;
    }

    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
    }

    .news-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease;
    }

    .news-card:hover {
        transform: translateY(-5px);
    }

    .news-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .news-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .news-card-title {
        font-size: 18px;
        font-weight: bold;
        color: #f97316;
        margin-bottom: 10px;
    }

    .news-card-desc {
        color: #4b5563;
        font-size: 14px;
        flex: 1;
    }

    .news-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        margin-top: 15px;
        color: #9ca3af;
    }

    .btn-read-more {
        padding: 6px 12px;
        background-color: transparent;
        border: 1px solid #f97316;
        color: #f97316;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-read-more:hover {
        background-color: #f97316;
        color: white;
    }
</style>
<br><br>
<div class="news-section">
    <h1 class="section-title">📰 Tin Tức</h1>

    <div class="news-grid">
        <!-- Card 1 -->
        <div class="news-card">
            <img src="{{ asset('clients/img/anhtintuc1.png') }}" alt="Tin 1">
            <div class="news-card-body">
                <h2 class="news-card-title">Ngày hội tín đồ ăn vặt: Giảm giá đặc biệt cho các món chiên giòn</h2>
                <p class="news-card-desc">Bạn là fan của đồ chiên giòn rụm, thơm ngon? Vậy thì tin vui dành cho bạn đây! Chúng tôi đang tổ chức ngày hội ...</p>
                <div class="news-meta">
                    <span><i class="bi bi-calendar3"></i> 10/06/2025</span>
                    <a href="{{ route('news.detail', ['id' => 1]) }}" class="btn-read-more">Xem chi tiết</a>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="news-card">
            <img src="{{ asset('clients/img/anhtintuc2.png') }}" alt="Tin 2">
            <div class="news-card-body">
                <h2 class="news-card-title">Thứ 2 đầy năng lượng: Ưu đãi đặc biệt cho bữa sáng nhanh gọn</h2>
                <p class="news-card-desc">Thứ 2 luôn là ngày thử thách nhất trong tuần. Sau một cuối tuần thư giãn, việc quay lại guồng quay công việc khiến ...</p>
                <div class="news-meta">
                    <span><i class="bi bi-calendar3"></i> 08/06/2025</span>
                    <a href="{{ route('news.detail', ['id' => 2]) }}" class="btn-read-more">Xem chi tiết</a>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="news-card">
            <img src="{{ asset('clients/img/anhtintuc3.png') }}" alt="Tin 3">
            <div class="news-card-body">
                <h2 class="news-card-title">Tiệc tùng linh đình: Khuyến mãi combo pizza và nước ngọt siêu tiết kiệm</h2>
                <p class="news-card-desc">Bạn đang tìm kiếm một bữa ăn ngon lành để cùng bạn bè quây quần? Đừng bỏ lỡ khuyến mãi hot nhất tuần này! Combo ...</p>
                <div class="news-meta">
                    <span><i class="bi bi-calendar3"></i> 05/06/2025</span>
                    <a href="{{ route('news.detail', ['id' => 3]) }}" class="btn-read-more">Xem chi tiết</a>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="news-card">
            <img src="{{ asset('clients/img/anhtintuc4.png') }}" alt="Tin 4">
            <div class="news-card-body">
                <h2 class="news-card-title">Deal Sốc "Nửa Giá": Thưởng Thức Mì Ý Ngon Mê Ly</h2>
                <p class="news-card-desc">Bạn là fan của mì Ý và luôn tìm kiếm những ưu đãi hấp dẫn? Vậy thì đây chính là tin vui dành cho bạn! ...</p>
                <div class="news-meta">
                    <span><i class="bi bi-calendar3"></i> 10/06/2025</span>
                    <a href="{{ route('news.detail', ['id' => 4]) }}" class="btn-read-more">Xem chi tiết</a>
                </div>
            </div>
        </div>

        <!-- Card 5 -->
        <div class="news-card">
            <img src="{{ asset('clients/img/anhtintuc5.png') }}" alt="Tin 5">
            <div class="news-card-body">
                <h2 class="news-card-title">Cuối Tuần "Cháy Phố": Khuyến Mãi Combo Gia Đình Cực Hấp Dẫn</h2>
                <p class="news-card-desc">Cuối tuần là thời điểm tuyệt vời để cùng gia đình quây quần bên nhau, tận hưởng những khoảnh khắc thư giãn và thưởng...</p>
                <div class="news-meta">
                    <span><i class="bi bi-calendar3"></i> 08/06/2025</span>
                    <a href="{{ route('news.detail', parameters: ['id' => 5]) }}" class="btn-read-more">Xem chi tiết</a>
                </div>
            </div>
        </div>

        <!-- Card 6 -->
        <div class="news-card">
            <img src="{{ asset('clients/img/anhtintuc6.png') }}" alt="Tin 6">
            <div class="news-card-body">
                <h2 class="news-card-title">Thứ 4 'vàng': Ưu đãi đặc biệt cho tín đồ gà rán</h2>
                <p class="news-card-desc">Hội những người mê gà rán đâu rồi? Thứ 4 này đừng bỏ lỡ cơ hội tận hưởng ưu đãi siêu hấp dẫn dành riêng...</p>
                <div class="news-meta">
                    <span><i class="bi bi-calendar3"></i> 05/06/2025</span>
                    <a href="{{ route('news.detail', parameters: ['id' => 6]) }}" class="btn-read-more">Xem chi tiết</a>
                </div>
            </div>
        </div>

    </div>
</div>

@include('clients.layouts.footer')
