<!DOCTYPE html>
<html lang="en">
@php
    $arabic = new \ArPHP\I18N\Arabic();
@endphp


<head>
    <meta charset="UTF-8">
    <title>Sailing Report</title>
    <style>
        @page {
            margin: 30px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            position: relative;
        }


        /* ===== الخط الأفقي تحت الشعار ===== */
        hr {
            border: none;
            border-top: 3px solid red;
        }

        .footer hr {
            border: none;
            border-top: 3px solid red;
        }

        .footer span {
            color: red;
        }

        /* ===== العنوان الرئيسي ===== */
        .report-title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            text-decoration: underline;
            margin: 15px 0;
            text-transform: uppercase;
        }

        /* ===== حقول "المسمى + الفراغ المسطر" ===== */
        .field-label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
            /* عرض المساحة قبل الخط */
            vertical-align: top;
        }

        .field-line {
            display: inline-block;
            width: 250px;
            /* عرض الخط */
            border-bottom: 1px solid #000;
            /* منقط أو يمكنك استخدام solid */
            margin-left: 15px;
            vertical-align: top;
            min-height: 18px;
            text-align: center;
            /* ارتفاع الخط */
        }

        .field-container {
            margin: 8px 20px;
            /* تحكم في المسافات الأفقية */
        }

        /* ===== قسم الحقول العمودية ===== */
        .fields-group {
            margin: 10px 20px;
        }

        .field-row {
            margin-bottom: 10px;
        }

        .field-label-inline {
            display: inline-block;
            width: 200px;
            font-weight: bold;
            vertical-align: top;
        }

        .field-line-inline {
            display: inline-block;
            width: 200px;
            margin-left: 15px;
            margin-right: 15px;
            border-bottom: 1px solid #000;
            min-height: 18px;
            vertical-align: top;
            text-align: center;
        }



        /* ========custome-line========= */
        .custome-line .field-label-inline {
            display: inline-block;
            width: 50px;
            font-weight: bold;
            vertical-align: top;
        }

        .custome-line .field-line-inline {
            display: inline-block;
            width: 130px;
            margin-left: 10px;
            margin-right: 10px;
            border-bottom: 1px solid #000;
            min-height: 18px;
            vertical-align: top;
            text-align: center;
        }





        /* ===== جداول صغيرة أو أقسام منفصلة ===== */
        .section-title {
            margin: 20px 20px 5px;
            font-weight: bold;
            text-decoration: underline;
        }

        .section-title-cat {
            margin: 20px 20px 5px;
            font-weight: bold;
        }

        .sub-fields {
            margin: 5px 20px;
            /* إزاحة إضافية */
        }

        .sub-field {
            margin-bottom: 8px;
        }

        .sub-label {
            display: inline-block;
            width: 100px;
            font-weight: bold;
            vertical-align: top;
        }

        .sub-line {
            display: inline-block;
            width: 130px;
            border-bottom: 1px solid #000;
            min-height: 18px;
            margin-right: 10px;
            vertical-align: top;
            text-align: center;
        }

        /* ===== الخاتمة / التوقيع ===== */
        .signature {
            margin: 30px 20px 10px;
            font-weight: bold;
        }

        /* ===== ذيل الصفحة (Footer) ===== */
        .footer {
            position: absolute;
            bottom: 10px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 12px;
            text-align: left;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 50%;
            text-align: left;
            vertical-align: bottom;
        }

        .text-cell {
            width: 50%;
            text-align: right;
            vertical-align: bottom;
            font-weight: bold;
        }

        /* ======== */

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer .logo-cell {
            width: 50%;
            text-align: right;
            vertical-align: top;
        }

        .footer .text-cell {
            width: 50%;
            text-align: left;
            vertical-align: top;
            font-weight: bold;
        }

        .bold {
            font-weight: bold;
            display: inline-block;
            vertical-align: top;
        }
    </style>
</head>

<body>

    <!-- الشعار في الزاوية العلوية اليسرى -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <img width="160px" src="{{ public_path('images/mv.png') }}" alt="MarineValley Logo">
            </td>
            <td class="text-cell">www.mvslservices.com</td>
        </tr>
    </table>
    <hr>


    <!-- عنوان الورقة -->
    <div class="report-title">Sailing Report</div>

    <!-- الحقول الأساسية في سطر واحد (مثال) -->
    <div class="field-container">
        <span class="field-label">Port:</span>
        <span class="field-line">{{ $port_name }}</span>
        <span class="field-label" style="margin-left: 15px;"> Berth No:</span>
        <span class="field-line">{{ $berth_no }}</span>
    </div>

    <div class="field-container">
        <span class="field-label">Vessel:</span>
        <span class="field-line">{{ $vessel_name }}</span>
        <span class="field-label" style="margin-left: 15px;"> Voy:</span>
        <span class="field-line">{{ $voy }}</span>
    </div>

    <!-- حقول عمودية (GRT / NRT / DWT) -->
    <div class="fields-group">
        <div class="field-row custome-line">
            <span class="field-label-inline">GRT</span>
            <span class="field-line-inline">{{ $grt }}</span>
            <span class="field-label-inline" style="margin-left:20px;">NRT</span>
            <span class="field-line-inline">{{ $nrt }}</span>
            <span class="field-label-inline" style="margin-left:20px;">DWT</span>
            <span class="field-line-inline">{{ $dwt }}</span>
        </div>
    </div>

    <br>




    {{-- 'eta' => $vessel->eta, --}}
    {{-- 'etd' => $vessel->etd, --}}
    {{-- 'eosp' => $vessel->eosp, --}}




    {{-- TODO at --}}
    <!-- أوقات وصول / مغادرة -->
    <div class="fields-group">
        <div class="field-row">
            <span class="field-label-inline">Arrived Abu Dhabi On</span>
            @php
                // تحقق إذا كانت القيمة موجودة ولم تكن فارغة
                if (!empty($aado)) {
                    [$date, $time] = explode(' ', $aado, 2);
                } else {
                    $date = 'N/A';
                    $time = 'N/A';
                }
            @endphp
            <span class="field-line-inline">{{ $date }}</span>
            <span> at </span>
            <span class="field-line-inline">{{ $time }}</span>
            <span> (Local Time) </span>
        </div>
        <div class="field-row">
            <span class="field-label-inline">NOR Tendered On</span>
            @php
                // تحقق إذا كانت القيمة موجودة ولم تكن فارغة
                if (!empty($nor_tendered)) {
                    [$date, $time] = explode(' ', $nor_tendered, 2);
                } else {
                    $date = 'N/A';
                    $time = 'N/A';
                }
            @endphp
            <span class="field-line-inline">{{ $date }}</span>
            <span> at </span>
            <span class="field-line-inline">{{ $time }}</span>
            <span> (Local Time) </span>
        </div>
        <div class="field-row">
            <span class="field-label-inline">NOR Accepted On</span>
            @php
                // تحقق إذا كانت القيمة موجودة ولم تكن فارغة
                if (!empty($nor_accepted)) {
                    [$date, $time] = explode(' ', $nor_accepted, 2);
                } else {
                    $date = 'N/A';
                    $time = 'N/A';
                }
            @endphp
            <span class="field-line-inline">{{ $date }}</span>
            <span> at </span>
            <span class="field-line-inline">{{ $time }}</span>
            <span> (Local Time) </span>
        </div>
        <div class="field-row">
            <span class="field-label-inline">Dropped Anchor</span>
            @php
                // تحقق إذا كانت القيمة موجودة ولم تكن فارغة
                if (!empty($dropped_anchor)) {
                    [$date, $time] = explode(' ', $dropped_anchor, 2);
                } else {
                    $date = 'N/A';
                    $time = 'N/A';
                }
            @endphp
            <span class="field-line-inline">{{ $date }}</span>
            <span> at </span>
            <span class="field-line-inline">{{ $time }}</span>
            <span> (Local Time) </span>
        </div>
        <div class="field-row">
            <span class="field-label-inline">Heaved Up Anchor</span>
            @php
                // تحقق إذا كانت القيمة موجودة ولم تكن فارغة
                if (!empty($heaved_up_anchor)) {
                    [$date, $time] = explode(' ', $heaved_up_anchor, 2);
                } else {
                    $date = 'N/A';
                    $time = 'N/A';
                }
            @endphp
            <span class="field-line-inline">{{ $date }}</span>
            <span> at </span>
            <span class="field-line-inline">{{ $time }}</span>
            <span> (Local Time) </span>
        </div>
        <div class="field-row">
            <span class="field-label-inline">Pilot Boarded On</span>
            @php
                // تحقق إذا كانت القيمة موجودة ولم تكن فارغة
                if (!empty($pilot_boarded)) {
                    [$date, $time] = explode(' ', $pilot_boarded, 2);
                } else {
                    $date = 'N/A';
                    $time = 'N/A';
                }
            @endphp
            <span class="field-line-inline">{{ $date }}</span>
            <span> at </span>
            <span class="field-line-inline">{{ $time }}</span>
            <span> (Local Time) </span>
        </div>
        <div class="field-row">
            <span class="field-label-inline">First Line</span>
            @php
                // تحقق إذا كانت القيمة موجودة ولم تكن فارغة
                if (!empty($first_line)) {
                    [$date, $time] = explode(' ', $first_line, 2);
                } else {
                    $date = 'N/A';
                    $time = 'N/A';
                }
            @endphp
            <span class="field-line-inline">{{ $date }}</span>
            <span> at </span>
            <span class="field-line-inline">{{ $time }}</span>
            <span> (Local Time) </span>
        </div>
        <div class="field-row">
            <span class="field-label-inline">Berthed On</span>
            @php
                // تحقق إذا كانت القيمة موجودة ولم تكن فارغة
                if (!empty($berthed_on)) {
                    [$date, $time] = explode(' ', $berthed_on, 2);
                } else {
                    $date = 'N/A';
                    $time = 'N/A';
                }
            @endphp
            <span class="field-line-inline">{{ $date }}</span>
            <span> at </span>
            <span class="field-line-inline">{{ $time }}</span>
            <span> (Local Time) </span>
        </div>
        <div class="field-row">
            <span class="field-label-inline">Made Fast On</span>
            @php
                // تحقق إذا كانت القيمة موجودة ولم تكن فارغة
                if (!empty($made_fast)) {
                    [$date, $time] = explode(' ', $made_fast, 2);
                } else {
                    $date = 'N/A';
                    $time = 'N/A';
                }
            @endphp
            <span class="field-line-inline">{{ $date }}</span>
            <span> at </span>
            <span class="field-line-inline">{{ $time }}</span>
            <span> (Local Time) </span>
        </div>
        <div class="field-row">
            <span class="field-label-inline">Sailed On</span>
            @php
                // تحقق إذا كانت القيمة موجودة ولم تكن فارغة
                if (!empty($sailed_on)) {
                    [$date, $time] = explode(' ', $sailed_on, 2);
                } else {
                    $date = 'N/A';
                    $time = 'N/A';
                }
            @endphp
            <span class="field-line-inline">{{ $date }}</span>
            <span> at </span>
            <span class="field-line-inline">{{ $time }}</span>
            <span> (Local Time) </span>
        </div>
    </div>

    <br>

    <!-- ARRIVAL ROB -->
    <div class="section-title">Bunkers ROB:</div>




    <span class="section-title-cat">On Arrival:</span>
    <div class="sub-fields">
        <div class="sub-field">
            <span class="sub-label">Fuel Oil</span>
            <span class="sub-line">{{ $arrival_fuel_oil }}</span>
            <span class="bold">Tons, </span>
            <span class="sub-label">Diesel Oil</span>
            <span class="sub-line">{{ $arrival_diesel_oil }}</span>
            <span class="bold">Tons, </span>
            <span class="sub-label">Fresh Water</span>
            <span class="sub-line">{{ $arrival_fresh_water }}</span>
            <span class="bold">Tons</span>
        </div>
        <div class="sub-field">
            <span class="sub-label">Draft FWD</span>
            <span class="sub-line">{{ $arrival_draft_fwd }}</span>
            <span class="bold">MTRs, </span>
            <span class="sub-label">Draft AFT</span>
            <span class="sub-line">{{ $arrival_draft_aft }}</span>
            <span class="bold">MTRs</span>
        </div>
    </div>

    <br>

    <span class="section-title-cat">On Departure:</span>
    <div class="sub-fields">
        <div class="sub-field">
            <span class="sub-label">Fuel Oil</span>
            <span class="sub-line">{{ $departure_fuel_oil }}</span>
            <span class="bold">Tons, </span>
            <span class="sub-label">Diesel Oil</span>
            <span class="sub-line">{{ $departure_diesel_oil }}</span>
            <span class="bold">Tons, </span>
            <span class="sub-label">Fresh Water</span>
            <span class="sub-line">{{ $departure_fresh_water }}</span>
            <span class="bold">Tons</span>
        </div>
        <div class="sub-field">
            <span class="sub-label">Draft FWD</span>
            <span class="sub-line">{{ $departure_draft_fwd }}</span>
            <span class="bold">MTRs, </span>
            <span class="sub-label">Draft AFT</span>
            <span class="sub-line">{{ $departure_draft_aft }}</span>
            <span class="bold">MTRs</span>
        </div>
    </div>

    <br>

    <!-- Next Port of Call / ETA -->
    <div class="field-container">
        <span class="field-label" style="width: 120px;">Next Port of Call</span>
        <span class="field-line">{{ $next_port_of_call }}</span>
        <span class="field-label" style="margin-left: 15px; width: 50px;">ETA</span>
        <span class="field-line">{{ $eta_next_port }}</span>
    </div>

    <br>

    <!-- Any Requirements -->
    <div class="fields-group">
        <div class="field-row">
            <span class="field-label-inline">Any requirements:</span>
        </div>
        <!-- يمكن تكرار السطور لزيادة المساحة -->
        <div class="field-row">
            <span class="field-line-inline" style="width: 800px;">{{ $any_requirements }}</span>
        </div>
        <div class="field-row">
            <span class="field-line-inline" style="width: 800px;"></span>
        </div>
        <div class="field-row">
            <span class="field-line-inline" style="width: 800px;"></span>
        </div>
    </div>


    <!-- ذيل الصفحة -->
    <div class="footer">
        <hr>
        <table class="footer-table">
            <tr>
                <td class="text-cell">
                    <div><span>Address:</span> P.O. Box: 12345, Abu Dhabi - U.A.E.</div>
                    <div><span>Telephone:</span> +971 2 64 44 826, Fax: +971 2 64 44 827</div>
                    <div><span>Support Team Email:</span> <u style="color: blue">info@mvslservices.com</u></div>
                </td>
                <td class="logo-cell">
                    <img width="90px" src="{{ public_path('images/mv.png') }}" alt="MarineValley Logo">
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
