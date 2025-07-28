@extends('clients.layouts.app')

@section('content')
<br><br><br><br>
<div class="container py-5">
    <div class="mb-4">
        <h2 class="fw-bold">Cuối Tuần "Cháy Phố": Khuyến Mãi Combo Gia Đình Cực Hấp Dẫn</h2>
        <p><i class="bi bi-calendar"></i> 24/03/2025</p>
    </div>

    <div class="mb-4 p-3 bg-light border rounded">
        <h5 class="fw-bold">NỘI DUNG BÀI VIẾT</h5>
        <ol class="text-danger">
            <li>Combo gia đình có gì hấp dẫn?</li>
            <li>Tại sao bạn không nên bỏ lỡ ưu đãi này?</li>
            <li>Làm sao để đặt hàng?</li>
            <li>Thời gian áp dụng</li>
            <li>Sẵn sàng “cháy phố” cùng combo gia đình?</li>
        </ol>
    </div>

    <div class="mb-4">
        <p>
            Cuối tuần là thời điểm tuyệt vời để cùng gia đình quây quần bên nhau, tận hưởng những khoảnh khắc thư giãn và thưởng thức những món ăn ngon.
            Để giúp bạn có một cuối tuần trọn vẹn hơn, chúng tôi mang đến chương trình khuyến mãi đặc biệt dành riêng cho các gia đình:
            <strong>Combo gia đình siêu tiết kiệm</strong>, giúp bạn vừa ăn ngon, vừa tiết kiệm chi phí.
        </p>
    </div>
    <img src="{{ asset('clients/img/anhtintuc5.png') }}" alt="Tin 5">
    <div class="mb-4">
        <h4 class="fw-bold">Combo gia đình có gì hấp dẫn?</h4>
        <p>
            Chúng tôi đã thiết kế một combo hoàn hảo với đầy đủ món ngon dành cho cả gia đình. Dưới đây là những gì bạn sẽ nhận được:
        </p>
        <ul>
            <li><strong>Gà giòn rụm:</strong> 8 miếng gà rán nóng hổi, giòn tan, thơm ngon.</li>
            <li><strong>Khoai tây chiên:</strong> 2 phần khoai tây chiên lớn, giòn vàng hấp dẫn.</li>
            <li><strong>Gà viên sốt đặc biệt:</strong> 12 viên gà viên phủ sốt cay hoặc phô mai béo ngậy.</li>
            <li><strong>Salad tươi ngon:</strong> 1 phần salad rau xanh với sốt đặc biệt.</li>
            <li><strong>Nước ngọt miễn phí:</strong> 4 ly nước ngọt cỡ lớn.</li>
        </ul>
        <p>
            Tất cả những món ngon này sẽ có mức giá ưu đãi <strong>giảm đến 35%</strong> so với giá thông thường.
            Quá tuyệt vời cho một bữa ăn gia đình đúng không nào?
        </p>
    </div>

    <div class="mb-4">
        <h4 class="fw-bold">Tại sao bạn không nên bỏ lỡ ưu đãi này?</h4>
        <p>
            Chúng tôi tin rằng một bữa ăn ngon không chỉ giúp bạn no bụng mà còn mang lại niềm vui và sự kết nối giữa các thành viên trong gia đình.
            Combo gia đình này là lựa chọn lý tưởng cho:
        </p>
        <ul>
            <li><strong>Bữa ăn sum vầy:</strong> Cùng nhau thưởng thức món ngon, tạo nên những kỷ niệm đáng nhớ.</li>
            <li><strong>Tiết kiệm chi phí:</strong> Với mức giá hấp dẫn, bạn không cần lo về ngân sách.</li>
            <li><strong>Thưởng thức món ăn chất lượng:</strong> Nguyên liệu tươi ngon, chế biến theo công thức đặc biệt.</li>
        </ul>
    </div>

    <div class="mb-4">
        <h4 class="fw-bold">Làm sao để đặt hàng?</h4>
        <p>Để tận hưởng ưu đãi hấp dẫn này, bạn có thể đặt hàng theo những cách sau:</p>
        <ul>
            <li>Đến trực tiếp cửa hàng và yêu cầu "Combo Gia Đình Cháy Phố".</li>
            <li>Đặt hàng qua ứng dụng hoặc website để nhận thêm ưu đãi.</li>
            <li>Gọi hotline để được hỗ trợ nhanh chóng.</li>
        </ul>
    </div>

    <div class="mb-4">
        <h4 class="fw-bold">Thời gian áp dụng</h4>
        <p>
            Chương trình khuyến mãi diễn ra <strong>vào cuối tuần, từ thứ 6 đến chủ nhật</strong> và áp dụng cho toàn bộ hệ thống cửa hàng.
            Hãy nhanh tay đặt hàng để không bỏ lỡ cơ hội này!
        </p>
    </div>

    <div class="mb-5">
        <h4 class="fw-bold">Sẵn sàng “cháy phố” cùng combo gia đình?</h4>
        <p>
            Cuối tuần này, đừng bỏ lỡ cơ hội tận hưởng bữa ăn ngon bên gia đình với mức giá siêu tiết kiệm.
            Hãy để hương vị tuyệt vời của gà rán giòn rụm và các món ăn hấp dẫn làm cho buổi họp mặt gia đình thêm trọn vẹn!
        </p>
        <p>Hẹn gặp bạn tại cửa hàng!</p>
    </div>

    <div class="d-flex gap-3">
        <span>Chia sẻ</span>
        <a href="#"><i class="bi bi-facebook fs-5"></i></a>
        <a href="#"><i class="bi bi-envelope fs-5"></i></a>
        <a href="#"><i class="bi bi-link-45deg fs-5"></i></a>
    </div>
</div>
@endsection
