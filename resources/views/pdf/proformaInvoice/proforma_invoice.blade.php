<!DOCTYPE html>
<html lang="en" dir="ltr">
@php
    $arabic = new \ArPHP\I18N\Arabic();
@endphp


<head>
    <meta charset="UTF-8">
    <title>Proforma Disbursement Account</title>
    <style>
        /* ===== خطوط (يمكنك تعديلها أو استخدام ما يناسبك) ===== */
        @font-face {
            font-family: 'Arial';
            font-style: normal;
            font-weight: 400;
            src: url('{{ storage_path('fonts/arial-regular.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'Arial';
            font-style: normal;
            font-weight: 700;
            src: url('{{ storage_path('fonts/arial-bold.ttf') }}') format('truetype');
        }

        @font-face {
            font-family: 'Arial';
            font-style: normal;
            font-weight: 900;
            src: url('{{ storage_path('fonts/arial-black.ttf') }}') format('truetype');
        }

        body {
            margin: 10px;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            color: #000;
            line-height: 1.4;
        }

        h1,
        h2,
        h3 {
            margin: 0;
            padding: 0;
        }

        /* ===== تنسيق الهيدر (الشعار، العنوان العلوي) ===== */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .header-left {
            width: 50%;
        }

        .header-right {
            width: 50%;
            text-align: right;
            position: absolute;
            top: 0px;
            right: 0px;
        }

        .company-name {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 12px;
            color: #555;
            line-height: 1.6;
        }

        /* ===== عنوان الفاتورة الرئيس ===== */
        .invoice-title {
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 16px;
            margin: 20px 0 10px 0;
            text-decoration: underline;
        }

        /* ===== جدول المعلومات الأساسية (Vessel / Port / GRT / ...) ===== */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ccc;
        }

        .info-table td {
            padding: 5px;
            vertical-align: top;
            border: 1px solid #ccc;
        }

        .info-table .label {
            font-weight: 700;
            width: 100px;
            white-space: nowrap;
        }

        /* ===== جدول بنود الفاتورة ===== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .items-table th {
            background: #f3f3f3;
            font-weight: 700;
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        .items-table td {
            border: 1px solid #ccc;
            padding: 8px;
            vertical-align: middle;
        }

        .items-table .right {
            text-align: right;
        }

        /* ===== المجموع النهائي ===== */
        .totals {
            margin-top: 10px;
            width: 100%;
            border-collapse: collapse;
        }

        .totals td {
            padding: 5px;
        }

        .totals .label {
            font-weight: 700;
            text-align: right;
        }

        .totals .value {
            text-align: right;
            width: 100px;
        }

        /* ===== النص الكتابي (in words) ===== */
        .in-words {
            margin: 10px 0;
            font-style: italic;
            font-size: 14px;
        }

        /* ===== معلومات الحساب البنكي ===== */
        .bank-details {
            margin-top: 15px;
            font-size: 14px;
            line-height: 1.5;
        }

        .bank-details .label {
            font-weight: 700;
        }

        /* ===== تنسيق عام ===== */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: 700;
        }
    </style>
</head>

<body>

    <!-- ===== الهيدر (الشعار + بيانات الشركة) ===== -->
    <div class="header">
        <div class="header-left">
            <!-- يمكن وضع اسم الشركة أو الشعار هنا -->
            
            
            
            <div class="company-name">Marine Valley Shipping & Logistics Services LLC</div>
            <div class="company-details">
                Address: P.O. Box: 12345, Abu Dhabi - U.A.E. <br>
                Telephone: +971 2 64 44 826, Fax: +971 2 64 44 827 <br>
                Support Team Email: info@mvslservices.com
            </div>
        </div>
        <div class="header-right">
            <img width="180px" src="{{ public_path('images/mv.png') }}" alt="MarineValley Logo">
        </div>
    </div>

    <!-- ===== عنوان الفاتورة ===== -->
    <div class="invoice-title">PROFORMA DISBURSEMENT ACCOUNT</div>

    <!-- ===== جدول المعلومات الأساسية ===== -->
    <table class="info-table">
        <tr>
            <td class="label">Vessel</td>
            <td>TEAM SPIRIT</td>
            <td class="label">GRT</td>
            <td>4,078</td>
            <td class="label">NRT</td>
            <td>2,009</td>
        </tr>
        <tr>
            <td class="label">Voyage</td>
            <td>12345</td>
            <td class="label">Port / Country</td>
            <td>Khalifa Port / UAE</td>
            <td class="label">ETA</td>
            <td>22/03/2025</td>
        </tr>
        <tr>
            <td class="label">ETD</td>
            <td>23/03/2025</td>
            <td class="label">Status</td>
            <td>At Berth</td>
            <td class="label">Currency</td>
            <td>USD</td>
        </tr>
    </table>

    <!-- ===== جدول بنود الفاتورة ===== -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">MS/Description</th>
                <th style="width: 15%;">Unit Price</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 10%;">Tax</th>
                <th style="width: 15%;">Total (USD)</th>
            </tr>
        </thead>
        <tbody>
            <!-- مثال على البنود -->
            <tr>
                <td>AGENCY FEES (Import &amp; Export Permit)</td>
                <td class="right">5,000.00</td>
                <td class="right">1</td>
                <td class="right">0.00</td>
                <td class="right">5,000.00</td>
            </tr>
            <tr>
                <td>Port Dues</td>
                <td class="right">1,500.00</td>
                <td class="right">2</td>
                <td class="right">0.00</td>
                <td class="right">3,000.00</td>
            </tr>
            <tr>
                <td>Berth Dues</td>
                <td class="right">700.00</td>
                <td class="right">3</td>
                <td class="right">0.00</td>
                <td class="right">2,100.00</td>
            </tr>
            <tr>
                <td>Stevedoring Charges</td>
                <td class="right">8,250.00</td>
                <td class="right">1</td>
                <td class="right">0.00</td>
                <td class="right">8,250.00</td>
            </tr>
            <tr>
                <td>Miscellaneous</td>
                <td class="right">1,200.00</td>
                <td class="right">1</td>
                <td class="right">0.00</td>
                <td class="right">1,200.00</td>
            </tr>
            <!-- أضف المزيد من البنود حسب الحاجة -->
        </tbody>
    </table>

    <!-- ===== المجموع النهائي ===== -->
    <table class="totals">
        <tr>
            <td class="label">Sub Total</td>
            <td class="value">19,550.00</td>
        </tr>
        <tr>
            <td class="label">VAT 5%</td>
            <td class="value">977.50</td>
        </tr>
        <tr>
            <td class="label">Grand Total</td>
            <td class="value">20,527.50</td>
        </tr>
    </table>

    <!-- ===== النص الكتابي (المبلغ بالحروف) ===== -->
    <div class="in-words">
        <strong>In Words:</strong> US Dollars Twenty Thousand Five Hundred Twenty Seven and Fifty Cents
    </div>

    <!-- ===== تفاصيل الحساب البنكي ===== -->
    <div class="bank-details">
        <p><span class="label">A/C Name:</span> Marine Valley Shipping and Logistics Services LLC</p>
        <p><span class="label">A/C No:</span> 141312345678901</p>
        <p><span class="label">IBAN:</span> AE310141312345678901</p>
        <p><span class="label">SWIFT:</span> ABCDAEADXXX</p>
        <p><span class="label">Bank:</span> Abu Dhabi Bank</p>
        <p><span class="label">TRN:</span> 1001234567009</p>
    </div>

    <!-- ===== ملاحظات ختامية إن وجدت ===== -->
    <p style="margin-top: 30px;">
        <em>Note: Please settle the payment within 7 working days.</em>
    </p>

</body>

</html>
