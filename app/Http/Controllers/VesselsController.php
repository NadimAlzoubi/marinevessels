<?php

namespace App\Http\Controllers;

use App\Models\Vessel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\VesselsReportFields;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use ArPHP\I18N\Arabic;


class VesselsController extends Controller
{
    /**
     * Display a listing of the resource.
     */




    
    // <li>
    //     <a class="dropdown-item" href="' . route('vessels.show', $vessel->id) . '">
    //         <i class="bx bx-show"></i> View
    //     </a>
    // </li>



    public function create()
    {
        return view('vessels.create');
    }



    public function index(Request $request)
    {
        if ($request->ajax()) {
            $vessels = Vessel::query();
            return DataTables::of($vessels)
                ->addColumn('action', function ($vessel) {
                    return '
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton' . $vessel->id . '" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $vessel->id . '">
                                <li>
                                    <a class="dropdown-item" href="' . route('vessels.edit', $vessel->id) . '" data-id="' . $vessel->id . '">
                                        <i class="bx bx-edit"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="' . route('invoices.create') . '">
                                        <i class="bx bx-plus-circle"></i> Create Invoice
                                    </a>
                                </li>
                            
                                <li>
                                    <a class="dropdown-item" href="' . route('vessels.show', $vessel->id) . '">
                                        <i class="bx bxs-report"></i> Sailing report
                                    </a>
                                </li>
                                <li>
                                    <a target="_blank" class="dropdown-item" href="' . route('pdf.proformaInvoice.proforma_invoice', ["id" => $vessel->id, "clickOption" => "stream"]) . '">
                                        <i class="bx bx-printer"></i> Print proforma
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="' . route('vessels.show', $vessel->id) . '">
                                        <i class="bx bx-receipt"></i> Proforma invoice
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="' . route('vessels.show', $vessel->id) . '">
                                        <i class="bx bx-dollar"></i> Final invoice
                                    </a>
                                </li>
                                <li>
                                    <form class="d-inline" action="' . route('vessels.destroy', $vessel->id) . '" method="POST">
                                        ' . csrf_field() . '
                                        ' . method_field('DELETE') . '
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bx bx-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('vessels.index');
    }

    /**
     * Show the specified resource for editing.
     */
    public function show($id)
    {
        // جلب بيانات السفينة باستخدام ID
        $vessel = Vessel::findOrFail($id); // إذا لم يتم العثور على السفينة، سيتم توجيه المستخدم إلى صفحة 404

        // تحقق إذا كان الطلب هو AJAX
        if (request()->ajax()) {
            // إذا كان الطلب AJAX، أعد البيانات بتنسيق JSON
            return response()->json($vessel);
        }

        // $fields = VesselsReportFields::all();
        return view('vessels.show', compact('vessel'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'vessel_name' => 'required|string',
            'port_name' => 'nullable|string',
            'eta' => 'nullable|date',
            'etd' => 'nullable|date',
            'status' => 'nullable|string',
            'berth_no' => 'nullable|string',
            'voy' => 'nullable|string',
            'grt' => 'nullable|string',
            'nrt' => 'nullable|string',
            'dwt' => 'nullable|string',
            'eosp' => 'nullable|date',
            'aado' => 'nullable|date',
            'nor_tendered' => 'nullable|date',
            'nor_accepted' => 'nullable|date',
            'dropped_anchor' => 'nullable|date',
            'heaved_up_anchor' => 'nullable|date',
            'pilot_boarded' => 'nullable|date',
            'first_line' => 'nullable|date',
            'berthed_on' => 'nullable|date',
            'made_fast' => 'nullable|date',
            'sailed_on' => 'nullable|date',
            'arrival_fuel_oil' => 'nullable|string',
            'arrival_diesel_oil' => 'nullable|string',
            'arrival_fresh_water' => 'nullable|string',
            'arrival_draft_fwd' => 'nullable|string',
            'arrival_draft_aft' => 'nullable|string',
            'departure_fuel_oil' => 'nullable|string',
            'departure_diesel_oil' => 'nullable|string',
            'departure_fresh_water' => 'nullable|string',
            'departure_draft_fwd' => 'nullable|string',
            'departure_draft_aft' => 'nullable|string',
            'next_port_of_call' => 'nullable|string',
            'eta_next_port' => 'nullable|date',
            'any_requirements' => 'nullable|string',
        ]);

        // تحويل التواريخ إلى تنسيق Y-m-d H:i:s باستخدام Carbon
        $eta = $request->eta ? Carbon::parse($request->eta)->format('Y-m-d H:i:s') : null;
        $etd = $request->etd ? Carbon::parse($request->etd)->format('Y-m-d H:i:s') : null;
        $eosp = $request->eosp ? Carbon::parse($request->eosp)->format('Y-m-d H:i:s') : null;
        $aado = $request->aado ? Carbon::parse($request->aado)->format('Y-m-d H:i:s') : null;
        $nor_tendered = $request->nor_tendered ? Carbon::parse($request->nor_tendered)->format('Y-m-d H:i:s') : null;
        $nor_accepted = $request->nor_accepted ? Carbon::parse($request->nor_accepted)->format('Y-m-d H:i:s') : null;
        $dropped_anchor = $request->dropped_anchor ? Carbon::parse($request->dropped_anchor)->format('Y-m-d H:i:s') : null;
        $heaved_up_anchor = $request->heaved_up_anchor ? Carbon::parse($request->heaved_up_anchor)->format('Y-m-d H:i:s') : null;
        $pilot_boarded = $request->pilot_boarded ? Carbon::parse($request->pilot_boarded)->format('Y-m-d H:i:s') : null;
        $first_line = $request->first_line ? Carbon::parse($request->first_line)->format('Y-m-d H:i:s') : null;
        $berthed_on = $request->berthed_on ? Carbon::parse($request->berthed_on)->format('Y-m-d H:i:s') : null;
        $made_fast = $request->made_fast ? Carbon::parse($request->made_fast)->format('Y-m-d H:i:s') : null;
        $sailed_on = $request->sailed_on ? Carbon::parse($request->sailed_on)->format('Y-m-d H:i:s') : null;
        $eta_next_port = $request->eta_next_port ? Carbon::parse($request->eta_next_port)->format('Y-m-d H:i:s') : null;


        // الحصول على السنة والشهر الحاليين
        $yearMonth = now()->format('ym'); // صيغة السنة والشهر (مثال: 2504 للشهر 4 من سنة 2025)

        // إيجاد الرقم الأكبر الحالي للوظيفة في الشهر والسنة الحالية
        $latestJobNo = Vessel::where('job_no', 'like', $yearMonth . '%')
            ->max('job_no'); // الحصول على أكبر قيمة لل job_no

        // تحديد الرقم التالي
        if ($latestJobNo) {
            // استخراج الرقم بعد السنة والشهر
            $number = substr($latestJobNo, 4) + 1; // إضافة 1 للرقم السابق
        } else {
            // إذا لم يكن هناك أرقام سابقة، البداية من 1
            $number = 1;
        }

        // تنسيق الرقم ليكون بثلاث خانات (مثال: 001, 002, ...)
        $formattedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);

        // إنشاء job_no بالصيغة المطلوبة
        $jobNo = $yearMonth . $formattedNumber;

        // dd($jobNo);

        // حفظ البيانات مع التواريخ المحوّلة
        $created = Vessel::create([
            'vessel_name' => $request->vessel_name,
            'job_no' => $jobNo,
            'port_name' => $request->port_name,
            'eta' => $eta,
            'etd' => $etd,
            'status' => $request->status,
            'berth_no' => $request->berth_no,
            'voy' => $request->voy,
            'grt' => $request->grt,
            'nrt' => $request->nrt,
            'dwt' => $request->dwt,
            'eosp' => $eosp,
            'aado' => $aado,
            'nor_tendered' => $nor_tendered,
            'nor_accepted' => $nor_accepted,
            'dropped_anchor' => $dropped_anchor,
            'heaved_up_anchor' => $heaved_up_anchor,
            'pilot_boarded' => $pilot_boarded,
            'first_line' => $first_line,
            'berthed_on' => $berthed_on,
            'made_fast' => $made_fast,
            'sailed_on' => $sailed_on,
            'arrival_fuel_oil' => $request->arrival_fuel_oil,
            'arrival_diesel_oil' => $request->arrival_diesel_oil,
            'arrival_fresh_water' => $request->arrival_fresh_water,
            'arrival_draft_fwd' => $request->arrival_draft_fwd,
            'arrival_draft_aft' => $request->arrival_draft_aft,
            'departure_fuel_oil' => $request->departure_fuel_oil,
            'departure_diesel_oil' => $request->departure_diesel_oil,
            'departure_fresh_water' => $request->departure_fresh_water,
            'departure_draft_fwd' => $request->departure_draft_fwd,
            'departure_draft_aft' => $request->departure_draft_aft,
            'next_port_of_call' => $request->next_port_of_call,
            'eta_next_port' => $eta_next_port,
            'any_requirements' => $request->any_requirements,
        ]);

        // التحقق من نجاح التخزين
        if (!$created) {
            return back()->with('error', 'Failed to created vessel.');
        }
        // إعادة التوجيه عند النجاح
        return redirect()->route('vessels.index')->with('success', 'Vessel created successfully!');
    }





    public function edit($id)
    {
        $vessel = Vessel::findOrFail($id);
        return view('vessels.edit', compact('vessel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'vessel_name' => 'required|string',
            'port_name' => 'nullable|string',
            'eta' => 'nullable|date',
            'etd' => 'nullable|date',
            'status' => 'nullable|string',
            'berth_no' => 'nullable|string',
            'voy' => 'nullable|string',
            'grt' => 'nullable|string',
            'nrt' => 'nullable|string',
            'dwt' => 'nullable|string',
            'eosp' => 'nullable|date',
            'aado' => 'nullable|date',
            'nor_tendered' => 'nullable|date',
            'nor_accepted' => 'nullable|date',
            'dropped_anchor' => 'nullable|date',
            'heaved_up_anchor' => 'nullable|date',
            'pilot_boarded' => 'nullable|date',
            'first_line' => 'nullable|date',
            'berthed_on' => 'nullable|date',
            'made_fast' => 'nullable|date',
            'sailed_on' => 'nullable|date',
            'arrival_fuel_oil' => 'nullable|string',
            'arrival_diesel_oil' => 'nullable|string',
            'arrival_fresh_water' => 'nullable|string',
            'arrival_draft_fwd' => 'nullable|string',
            'arrival_draft_aft' => 'nullable|string',
            'departure_fuel_oil' => 'nullable|string',
            'departure_diesel_oil' => 'nullable|string',
            'departure_fresh_water' => 'nullable|string',
            'departure_draft_fwd' => 'nullable|string',
            'departure_draft_aft' => 'nullable|string',
            'next_port_of_call' => 'nullable|string',
            'eta_next_port' => 'nullable|date',
            'any_requirements' => 'nullable|string',
        ]);
        // التحقق من فشل الفاليديشن
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // العثور على الـ Vessel
        $vessel = Vessel::findOrFail($id);

        // تحويل التواريخ إلى تنسيق Y-m-d H:i:s باستخدام Carbon
        $eta = $request->eta ? Carbon::parse($request->eta)->format('Y-m-d H:i:s') : null;
        $etd = $request->etd ? Carbon::parse($request->etd)->format('Y-m-d H:i:s') : null;
        $eosp = $request->eosp ? Carbon::parse($request->eosp)->format('Y-m-d H:i:s') : null;
        $aado = $request->aado ? Carbon::parse($request->aado)->format('Y-m-d H:i:s') : null;
        $nor_tendered = $request->nor_tendered ? Carbon::parse($request->nor_tendered)->format('Y-m-d H:i:s') : null;
        $nor_accepted = $request->nor_accepted ? Carbon::parse($request->nor_accepted)->format('Y-m-d H:i:s') : null;
        $dropped_anchor = $request->dropped_anchor ? Carbon::parse($request->dropped_anchor)->format('Y-m-d H:i:s') : null;
        $heaved_up_anchor = $request->heaved_up_anchor ? Carbon::parse($request->heaved_up_anchor)->format('Y-m-d H:i:s') : null;
        $pilot_boarded = $request->pilot_boarded ? Carbon::parse($request->pilot_boarded)->format('Y-m-d H:i:s') : null;
        $first_line = $request->first_line ? Carbon::parse($request->first_line)->format('Y-m-d H:i:s') : null;
        $berthed_on = $request->berthed_on ? Carbon::parse($request->berthed_on)->format('Y-m-d H:i:s') : null;
        $made_fast = $request->made_fast ? Carbon::parse($request->made_fast)->format('Y-m-d H:i:s') : null;
        $sailed_on = $request->sailed_on ? Carbon::parse($request->sailed_on)->format('Y-m-d H:i:s') : null;
        $eta_next_port = $request->eta_next_port ? Carbon::parse($request->eta_next_port)->format('Y-m-d H:i:s') : null;

        // تحديث البيانات مع القيم الجديدة
        $updated = $vessel->update([
            'vessel_name' => $request->vessel_name,
            'port_name' => $request->port_name,
            'eta' => $eta,
            'etd' => $etd,
            'status' => $request->status,
            'berth_no' => $request->berth_no,
            'voy' => $request->voy,
            'grt' => $request->grt,
            'nrt' => $request->nrt,
            'dwt' => $request->dwt,
            'eosp' => $eosp,
            'aado' => $aado,
            'nor_tendered' => $nor_tendered,
            'nor_accepted' => $nor_accepted,
            'dropped_anchor' => $dropped_anchor,
            'heaved_up_anchor' => $heaved_up_anchor,
            'pilot_boarded' => $pilot_boarded,
            'first_line' => $first_line,
            'berthed_on' => $berthed_on,
            'made_fast' => $made_fast,
            'sailed_on' => $sailed_on,
            'arrival_fuel_oil' => $request->arrival_fuel_oil,
            'arrival_diesel_oil' => $request->arrival_diesel_oil,
            'arrival_fresh_water' => $request->arrival_fresh_water,
            'arrival_draft_fwd' => $request->arrival_draft_fwd,
            'arrival_draft_aft' => $request->arrival_draft_aft,
            'departure_fuel_oil' => $request->departure_fuel_oil,
            'departure_diesel_oil' => $request->departure_diesel_oil,
            'departure_fresh_water' => $request->departure_fresh_water,
            'departure_draft_fwd' => $request->departure_draft_fwd,
            'departure_draft_aft' => $request->departure_draft_aft,
            'next_port_of_call' => $request->next_port_of_call,
            'eta_next_port' => $eta_next_port,
            'any_requirements' => $request->any_requirements,
        ]);

        // التحقق من نجاح التحديث
        if (!$updated) {
            return back()->with('error', 'Failed to update vessel.');
        }

        // إعادة التوجيه عند النجاح
        return redirect()->route('vessels.index')->with('success', 'Vessel updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $vessel = Vessel::findOrFail($id);
        $vessel->delete();
        return redirect()->route('vessels.index')->with('success', 'Vessel deleted successfully.');
    }







    public function generateSailingReportPdf($id, $clickOption = 'stream')
    {
        // جلب بيانات السفينة
        $vessel = Vessel::findOrFail($id);
        $arabic = new Arabic();

        // النصوص الأصلية
        $texts = [
            'vessel_name' => $vessel->vessel_name,
            'job_no' => $vessel->job_no,
            'port_name' => $vessel->port_name,
            'eta' => $vessel->eta,
            'etd' => $vessel->etd,
            'status' => $vessel->status,
            'berth_no' => $vessel->berth_no,
            'voy' => $vessel->voy,
            'grt' => $vessel->grt,
            'nrt' => $vessel->nrt,
            'dwt' => $vessel->dwt,
            'eosp' => $vessel->eosp,
            'aado' => $vessel->aado,
            'nor_tendered' => $vessel->nor_tendered,
            'nor_accepted' => $vessel->nor_accepted,
            'dropped_anchor' => $vessel->dropped_anchor,
            'heaved_up_anchor' => $vessel->heaved_up_anchor,
            'pilot_boarded' => $vessel->pilot_boarded,
            'first_line' => $vessel->first_line,
            'berthed_on' => $vessel->berthed_on,
            'made_fast' => $vessel->made_fast,
            'sailed_on' => $vessel->sailed_on,
            'arrival_fuel_oil' => $vessel->arrival_fuel_oil,
            'arrival_diesel_oil' => $vessel->arrival_diesel_oil,
            'arrival_fresh_water' => $vessel->arrival_fresh_water,
            'arrival_draft_fwd' => $vessel->arrival_draft_fwd,
            'arrival_draft_aft' => $vessel->arrival_draft_aft,
            'departure_fuel_oil' => $vessel->departure_fuel_oil,
            'departure_diesel_oil' => $vessel->departure_diesel_oil,
            'departure_fresh_water' => $vessel->departure_fresh_water,
            'departure_draft_fwd' => $vessel->departure_draft_fwd,
            'departure_draft_aft' => $vessel->departure_draft_aft,
            'next_port_of_call' => $vessel->next_port_of_call,
            'eta_next_port' => $vessel->eta_next_port,
            'any_requirements' => $vessel->any_requirements,

        ];






        if (!function_exists('mb_strrev')) {
            function mb_strrev($string, $encoding = 'UTF-8')
            {
                // تقسم النص إلى حروف باستخدام mb_str_split (إذا كان PHP >= 7.4)
                if (function_exists('mb_str_split')) {
                    $chars = mb_str_split($string, 1, $encoding);
                } else {
                    // حل بديل إذا لم تكن mb_str_split متوفرة
                    preg_match_all('/./us', $string, $matches);
                    $chars = $matches[0];
                }
                return implode('', array_reverse($chars));
            }
        }
        function fixArabicText($text, $arabic)
        {
            return preg_replace_callback('/[\p{Arabic}]+/u', function ($matches) use ($arabic) {
                // تطبيق utf8Glyphs على النص العربي
                $glyphText = $arabic->utf8Glyphs($matches[0]);

                // عكس ترتيب الكلمات بعد معالجتها
                $words = explode(' ', $glyphText);
                $reversedText = implode(' ', array_reverse($words));

                return $reversedText;
            }, $text);
        }

        // تصحيح جميع النصوص
        $data = array_map(function ($text) use ($arabic) {
            return fixArabicText($text, $arabic);
        }, $texts);

        // تصحيح جميع النصوص
        // $fixedTexts = array_map([$arabic, 'utf8Glyphs'], $texts);
        // // تمرير البيانات المصححة إلى الـ View
        // $data = [
        //     'vessel_name' => $fixedTexts['vessel_name'],
        //     'port_name' => $fixedTexts['port_name'],
        //     'any_requirements' => $fixedTexts['any_requirements']
        // ];


        // $data = array_map([$arabic, 'utf8Glyphs'], $texts);

        // إنشاء ملف PDF
        $pdf = Pdf::loadView('pdf.vesselReport.vessel_report', $data);
        // إضافة العلامة المائية
        $pdf->setPaper('A4', 'portrait')->output();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();

        // تحميل الصورة كعلامة مائية
        $imagePath = public_path('images/mv.png');
        // التحقق من وجود الصورة قبل المتابعة
        if (file_exists($imagePath)) {
            // الحصول على أبعاد الصورة الأصلية
            list($originalWidth, $originalHeight) = getimagesize($imagePath);

            // الحصول على أبعاد الصفحة
            $pageWidth = $canvas->get_width();
            $pageHeight = $canvas->get_height();

            // التحكم في حجم العلامة المائية (تكبير/تصغير الصورة الأصلية إذا لزم الأمر)
            $scaleFactor = 1; // نسبة تصغير الصورة (يمكن تعديلها)
            $watermarkWidth = $originalWidth * $scaleFactor;
            $watermarkHeight = $originalHeight * $scaleFactor;

            // التأكد من أن العلامة المائية لا تتجاوز أبعاد الصفحة
            if ($watermarkWidth > $pageWidth * 0.8) {
                $watermarkWidth = $pageWidth * 0.8;
                $watermarkHeight = ($originalHeight / $originalWidth) * $watermarkWidth;
            }
            if ($watermarkHeight > $pageHeight * 0.8) {
                $watermarkHeight = $pageHeight * 0.8;
                $watermarkWidth = ($originalWidth / $originalHeight) * $watermarkHeight;
            }

            // حساب موقع العلامة المائية في المنتصف
            $x = ($pageWidth - $watermarkWidth) / 2;
            $y = ($pageHeight - $watermarkHeight) / 2;
            $canvas->set_opacity(0.1); // شفافية العلامة المائية
            // رسم العلامة المائية
            $canvas->image($imagePath, $x, $y, $watermarkWidth, $watermarkHeight);
        }

        // اختيار نوع الإخراج
        return $clickOption === 'download'
            ? $pdf->download('vessel_report.pdf')
            : $pdf->stream('vessel_report.pdf');
    }








    public function generateProformaInvoicePdf($id, $clickOption = 'stream')
    {
        // جلب بيانات السفينة
        $vessel = Vessel::findOrFail($id);
        $arabic = new Arabic();

        // النصوص الأصلية
        $texts = [
            'vessel_name' => $vessel->vessel_name,
            'job_no' => $vessel->job_no,
            'port_name' => $vessel->port_name,
            'eta' => $vessel->eta,
            'etd' => $vessel->etd,
            'status' => $vessel->status,
            'berth_no' => $vessel->berth_no,
            'voy' => $vessel->voy,
            'grt' => $vessel->grt,
            'nrt' => $vessel->nrt,
            'dwt' => $vessel->dwt,
            'eosp' => $vessel->eosp,
            'aado' => $vessel->aado,
            'nor_tendered' => $vessel->nor_tendered,
            'nor_accepted' => $vessel->nor_accepted,
            'dropped_anchor' => $vessel->dropped_anchor,
            'heaved_up_anchor' => $vessel->heaved_up_anchor,
            'pilot_boarded' => $vessel->pilot_boarded,
            'first_line' => $vessel->first_line,
            'berthed_on' => $vessel->berthed_on,
            'made_fast' => $vessel->made_fast,
            'sailed_on' => $vessel->sailed_on,
            'arrival_fuel_oil' => $vessel->arrival_fuel_oil,
            'arrival_diesel_oil' => $vessel->arrival_diesel_oil,
            'arrival_fresh_water' => $vessel->arrival_fresh_water,
            'arrival_draft_fwd' => $vessel->arrival_draft_fwd,
            'arrival_draft_aft' => $vessel->arrival_draft_aft,
            'departure_fuel_oil' => $vessel->departure_fuel_oil,
            'departure_diesel_oil' => $vessel->departure_diesel_oil,
            'departure_fresh_water' => $vessel->departure_fresh_water,
            'departure_draft_fwd' => $vessel->departure_draft_fwd,
            'departure_draft_aft' => $vessel->departure_draft_aft,
            'next_port_of_call' => $vessel->next_port_of_call,
            'eta_next_port' => $vessel->eta_next_port,
            'any_requirements' => $vessel->any_requirements,

        ];






        if (!function_exists('mb_strrev')) {
            function mb_strrev($string, $encoding = 'UTF-8')
            {
                // تقسم النص إلى حروف باستخدام mb_str_split (إذا كان PHP >= 7.4)
                if (function_exists('mb_str_split')) {
                    $chars = mb_str_split($string, 1, $encoding);
                } else {
                    // حل بديل إذا لم تكن mb_str_split متوفرة
                    preg_match_all('/./us', $string, $matches);
                    $chars = $matches[0];
                }
                return implode('', array_reverse($chars));
            }
        }
        function fixArabicText1($text, $arabic)
        {
            return preg_replace_callback('/[\p{Arabic}]+/u', function ($matches) use ($arabic) {
                // تطبيق utf8Glyphs على النص العربي
                $glyphText = $arabic->utf8Glyphs($matches[0]);

                // عكس ترتيب الكلمات بعد معالجتها
                $words = explode(' ', $glyphText);
                $reversedText = implode(' ', array_reverse($words));

                return $reversedText;
            }, $text);
        }

        // تصحيح جميع النصوص
        $data = array_map(function ($text) use ($arabic) {
            return fixArabicText1($text, $arabic);
        }, $texts);

        // إنشاء ملف PDF
        $pdf = Pdf::loadView('pdf.proformaInvoice.proforma_invoice', $data);
        // إضافة العلامة المائية
        $pdf->setPaper('A4', 'portrait')->output();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();

        // تحميل الصورة كعلامة مائية
        $imagePath = public_path('images/mv.png');
        // التحقق من وجود الصورة قبل المتابعة
        if (file_exists($imagePath)) {
            // الحصول على أبعاد الصورة الأصلية
            list($originalWidth, $originalHeight) = getimagesize($imagePath);

            // الحصول على أبعاد الصفحة
            $pageWidth = $canvas->get_width();
            $pageHeight = $canvas->get_height();

            // التحكم في حجم العلامة المائية (تكبير/تصغير الصورة الأصلية إذا لزم الأمر)
            $scaleFactor = 1; // نسبة تصغير الصورة (يمكن تعديلها)
            $watermarkWidth = $originalWidth * $scaleFactor;
            $watermarkHeight = $originalHeight * $scaleFactor;

            // التأكد من أن العلامة المائية لا تتجاوز أبعاد الصفحة
            if ($watermarkWidth > $pageWidth * 0.8) {
                $watermarkWidth = $pageWidth * 0.8;
                $watermarkHeight = ($originalHeight / $originalWidth) * $watermarkWidth;
            }
            if ($watermarkHeight > $pageHeight * 0.8) {
                $watermarkHeight = $pageHeight * 0.8;
                $watermarkWidth = ($originalWidth / $originalHeight) * $watermarkHeight;
            }

            // حساب موقع العلامة المائية في المنتصف
            $x = ($pageWidth - $watermarkWidth) / 2;
            $y = ($pageHeight - $watermarkHeight) / 2;
            $canvas->set_opacity(0.1); // شفافية العلامة المائية
            // رسم العلامة المائية
            $canvas->image($imagePath, $x, $y, $watermarkWidth, $watermarkHeight);
        }

        // اختيار نوع الإخراج
        return $clickOption === 'download'
            ? $pdf->download('proforma_invoice.pdf')
            : $pdf->stream('proforma_invoice.pdf');
    }
}
