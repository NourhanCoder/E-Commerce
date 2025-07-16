@extends('website.layouts.master')
@section('title', 'Payment by Kashier')
@section('content')

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    {{-- <title>الدفع عبر Kashier</title> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            direction: rtl;
        }

        .container {
            max-width: 500px;
            margin: 80px auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            text-align: center;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 30px;
        }

              .back-button {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 14px 32px;
            font-size: 16px;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.2);
        }

        .back-button:hover {
            background-color: #c0392b;
        }

      
    </style>
</head>
<body>

   <div class="container" style="margin-top: 130px;">
        <h2> نافذة الدفع عبر Kashier...</h2>

        <script
            id="kashier-iFrame"
            src="https://payments.kashier.io/kashier-checkout.js"
            data-amount="{{ $amount }}"
            data-hash="{{ $hash }}"
            data-currency="{{ $currency }}"
            data-orderId="{{ $orderId }}"
            data-merchantId="{{ $merchantId }}"
            data-merchantRedirect="{{ $redirect_url }}"
            data-mode="{{ $mode }}"
            data-type="external"
            data-display="ar"
            data-interactionSource="Ecommerce"
            data-enable3DS="true">
        </script>

        <br><br>
       <form action="{{ route('checkout.index') }}" method="get">
            <button type="submit" class="back-button">رجوع لصفحة الطلب</button>
        </form>
    </div>

</body>
</html>
@endsection


