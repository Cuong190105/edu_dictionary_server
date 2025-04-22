<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Xác nhận thay đổi tài khoản</h2>
    <div class="text">
        <p>Xin chào, {{ $name }},</p>
        <p>Nhấn vào nút dưới đây để xác nhận thay đổi địa chỉ Email của bạn sang địa chỉ này.</p>
    </div>
    <a href="{{ $url }}" style="background: #4a69bd; color: white; padding: 20px 20px; text-decoration: None">Xác nhận thay đổi</a>
    <div class="text">
        <p>Nếu bạn vô tình nhận được thư này mà không dùng dịch vụ, hoặc không có ý định thay đổi, vui lòng bỏ qua thư.</p>
        <p>Nếu có vấn đề nhấn vào nút trên, sử dụng liên kết dưới đây</p>
    </div>
    <p><a href="{{ $url }}">{{ $url }}</a></p>
    <div class="text">
        <p>Liên kết có hiệu lực trong vòng 60 phút kể từ khi gửi</p>
        <p>Trân trọng,</p>
        <p>EDU Dictionary</p>
    </div>
</body>
</html>