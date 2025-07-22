@include('clients.layouts.header')
@include('clients.layouts.sidebar')

<br><br><br><br><br>
<style>
    .news-detail-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        font-family: Arial, sans-serif;
    }

    .news-detail-title {
        font-size: 28px;
        font-weight: bold;
        color: #111827;
        margin-bottom: 10px;
    }

    .news-detail-date {
        font-size: 14px;
        color: #9ca3af;
        margin-bottom: 20px;
    }

    .news-detail-toc {
        background: #f3f4f6;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 25px;
    }

    .news-detail-toc ul {
        margin: 0;
        padding-left: 20px;
    }

    .news-detail-toc li {
        margin-bottom: 5px;
        color: #1f2937;
    }

    .news-detail-section h2 {
        font-size: 20px;
        font-weight: bold;
        color: #f97316;
        margin-top: 30px;
        margin-bottom: 10px;
    }

    .news-detail-section p {
        font-size: 16px;
        color: #374151;
        margin-bottom: 10px;
    }

    .product-slider {
        display: flex;
        gap: 16px;
        overflow-x: auto;
        padding: 15px 0;
    }

    .product-card {
        min-width: 180px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    .product-card img {
        width: 100%;
        height: 120px;
        object-fit: contain;
        margin-bottom: 10px;
    }

    .product-title {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }

    .product-price {
        color: #ef4444;
        font-weight: bold;
    }

    .order-btn {
        display: inline-block;
        margin-top: 8px;
        padding: 5px 10px;
        background: #f97316;
        color: #fff;
        border-radius: 5px;
        font-size: 14px;
        text-decoration: none;
    }

    .order-btn:hover {
        background: #ea580c;
    }

    .share-section {
        margin-top: 40px;
    }

    .share-section span {
        font-weight: bold;
    }
</style>
<br><br>
<div class="news-detail-container">
    <h1 class="news-detail-title">Ngày hội tín đồ ăn vặt: Giảm giá đặc biệt cho các món chiên giòn</h1>
    <div class="news-detail-date">📅 24/03/2025</div>

    <div class="news-detail-toc">
        <strong>Nội dung bài viết</strong>
        <ul>
            <li>1. Ưu đãi cực sốc dành riêng cho bạn</li>
            <li>2. Những món chiên giòn bạn không thể bỏ qua</li>
            <li>3. Cách nhận ưu đãi nhanh chóng</li>
            <li>4. Ăn ngon, vui trọn vẹn!</li>
        </ul>
    </div>

    <div class="news-detail-section">
        <h2>Ưu đãi cực sốc dành riêng cho bạn</h2>
        <p>Chương trình khuyến mãi lần này mang đến hàng loạt ưu đãi hấp dẫn để bạn có thể thỏa sức tận hưởng các món chiên giòn yêu thích:</p>
        <ul>
            <li>Giảm 30% cho tất cả các món chiên khi đặt hàng từ 14:00 - 18:00.</li>
            <li>Mua 2 tặng 1 áp dụng cho combo khoai tây chiên, gà rán và nước ngọt.</li>
            <li>Combo siêu tiết kiệm: chỉ từ 99.000đ cho một phần combo gà rán, khoai tây và nước.</li>
        </ul>

    
       
    </div>

    <div class="news-detail-section">
        <h2>Những món chiên giòn bạn không thể bỏ qua</h2>
        <p>Nếu bạn chưa biết nên chọn món nào, đây là một vài gợi ý hoàn hảo:</p>
        <ul>
            <li>Gà rán giòn tan: Lớp vỏ giòn rụm, bên trong thịt mềm mọng nước.</li>
            <li>Khoai tây chiên vàng ươm: Dài, giòn, chấm cùng sốt béo ngậy.</li>
            <li>Phô mai que: Kéo sợi cực đã, béo ngậy và thơm lừng.</li>
            <li>Hải sản chiên xù: Mực, tôm chiên giòn, chấm sốt chua ngọt cực mê.</li>
        </ul>
    </div>

    <div class="news-detail-section">
        <h2>Cách nhận ưu đãi nhanh chóng</h2>
        <ul>
            <li>Đến trực tiếp cửa hàng vào khung giờ khuyến mãi và yêu cầu ưu đãi.</li>
            <li>Đặt hàng qua ứng dụng và nhập mã <strong>CRUNCHYDAY</strong> khi thanh toán.</li>
            <li>Mua hàng qua website, ưu đãi sẽ tự động được áp dụng vào giỏ hàng.</li>
        </ul>
    </div>

    <div class="news-detail-section">
        <h2>Ăn ngon, vui trọn vẹn!</h2>
        <p>Hãy tranh thủ cơ hội để thưởng thức ngay món chiên giòn ngon tuyệt vời cùng gia đình và bạn bè. Đừng bỏ lỡ!</p>
    </div>
</div>

@include('clients.layouts.footer')
