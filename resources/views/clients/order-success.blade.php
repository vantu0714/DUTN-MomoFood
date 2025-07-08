@extends('clients.layouts.app')

@section('content')
<br><br><br><br>
<div class="success-wrapper">
    <div class="success-box">
        <div class="loader" id="loader"></div>
        <div class="checkmark" id="checkmark">✔</div>
        <div class="message" id="message">Đặt hàng thành công!</div>
        <a href="{{ route('home') }}" class="btn-back" id="backBtn" style="display: none;">Về trang chủ</a>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #f0fff4, #d2f7e9);
        font-family: 'Segoe UI', sans-serif;
    }

    .success-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
    }

    .success-box {
        background: white;
        padding: 40px 30px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
        width: 100%;
        max-width: 400px;
    }

    .loader {
        border: 6px solid #f3f3f3;
        border-top: 6px solid #28a745;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .checkmark {
        display: none;
        font-size: 60px;
        color: #28a745;
        animation: popIn 0.5s ease forwards;
        margin-bottom: 10px;
    }

    @keyframes popIn {
        0% { transform: scale(0); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    .message {
        display: none;
        font-size: 22px;
        color: #333;
        margin-top: 10px;
        font-weight: 600;
    }

    .btn-back {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #28a745;
        color: white;
        border-radius: 8px;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .btn-back:hover {
        background-color: #218838;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => {
            document.getElementById("loader").style.display = "none";
            document.getElementById("checkmark").style.display = "block";
            document.getElementById("message").style.display = "block";
            document.getElementById("backBtn").style.display = "inline-block";
        }, 2000);
    });
</script>
@endsection
