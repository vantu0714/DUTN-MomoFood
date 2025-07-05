@include('clients.layouts.header')
@include('clients.layouts.sidebar')

<br>
<style>
    .news-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .news-title {
        text-align: center;
        color: #f97316;
        /* màu cam */
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 30px;
    }

    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    .news-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: box-shadow 0.3s ease;
    }

    .news-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .news-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .news-content {
        padding: 16px;
    }

    .news-content h2 {
        font-size: 18px;
        font-weight: bold;
        color: #f97316;
        margin-bottom: 10px;
    }

    .news-content p {
        font-size: 14px;
        color: #4b5563;
    }

    .news-date {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 10px;
    }
</style>
<br><br><br><br><br>
<div class="news-container">
    <h1 class="news-title">📰 Tin Tức & Ưu Đãi</h1>

    <div class="news-grid">

        <div class="news-card">
            <img src="{{ asset('clients/img/best-product-4.jpg') }}" alt="Tin 1">
            <div class="news-content">
                <h2>Top 5 đồ ăn vặt bán chạy nhất</h2>
                <p>Khám phá những món ăn vặt hot hit được yêu thích nhất tháng 6!</p>
                <span class="news-date">Đăng ngày 10/06/2025</span>
            </div>
        </div>

        <div class="news-card">
            <img src="{{ asset('clients/img/giamgia.png') }}" alt="Tin 2">
            <div class="news-content">
                <h2>Ưu đãi 20% cho đơn hàng đầu tiên</h2>
                <p>Đăng ký tài khoản và nhận ưu đãi đặc biệt từ cửa hàng!</p>
                <span class="news-date">Đăng ngày 08/06/2025</span>
            </div>
        </div>

        <div class="news-card">
            <img src="{{ asset('clients/img/snack.png') }}" alt="Tin 3">
            <div class="news-content">
                <h2>Cách chọn snack ngon & lành mạnh</h2>
                <p>Mẹo chọn đồ ăn vặt tốt cho sức khỏe mà vẫn ngon miệng.</p>
                <span class="news-date">Đăng ngày 05/06/2025</span>
            </div>
        </div>

    </div>
    <br><br>
    <div class="news-grid">

        <div class="news-card">
            <img src="{{ asset('clients/img/tieuduong.png') }}" alt="Tin 1">
            <div class="news-content">
                <h2>8 món ăn vặt tốt cho người bệnh tiểu đường</h2>
                <p>Người bệnh tiểu đường có thể chọn các món ăn vặt như nửa trái táo, vài múi bưởi, sữa chua không đường, bắp, chocolate nguyên chất. </p>
                <span class="news-date">Đăng ngày 10/06/2025</span>
            </div>
        </div>

        <div class="news-card">
            <img src="{{ asset('clients/img/giomua.png') }}" alt="Tin 2">
            <div class="news-content">
                <h2>4 món ăn nóng hổi cho ngày gió mùa</h2>
                <p>Hà Nội - Hãy cùng chúng tôi điểm danh những món ăn nóng hổi nếu bạn muốn làm ấm bụng vào những ngày không khí lạnh tràn về. </p>
                <span class="news-date">Đăng ngày 08/06/2025</span>
            </div>
        </div>

        <div class="news-card">
            <img src="{{ asset('clients/img/seol.png') }}" alt="Tin 3">
            <div class="news-content">
                <h2>Thế giới đồ ăn vặt đường phố ở Seoul</h2>
                <p>Đến chợ đêm Hongdae ở Seoul, du khách sẽ lạc vào thế giới đồ ăn vặt được bày bán trên xe như bánh trứng nướng, màn thầu chiên, bánh đồng xu, xiên que với giá từ vài chục nghìn đồng.</p>
                <span class="news-date">Đăng ngày 05/06/2025</span>
            </div>
        </div>

    </div>
</div>

@include('clients.layouts.footer')
