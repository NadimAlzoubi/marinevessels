<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Vessel;
use App\Models\InvoiceFee;
use App\Models\FixedFee;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\PricingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;


class VesselInvoicesController extends Controller
{
    use AuthorizesRequests;
    protected $pricingService;

    public function __construct(PricingService $pricingService = null)
    {
        // إذا لم يتم حقن خدمة التسعير، قم بإنشاء مثيل افتراضي
        $this->pricingService = $pricingService ?? new PricingService();
    }
    /**
     * عرض جميع الفواتير المرتبطة بالسفينة.
     */
    public function index(Request $request, Vessel $vessel)
    {
        if ($request->ajax()) {
            $invoices_by_vessel = Invoice::with('vessel')
                ->where('vessel_id', $vessel->id)
                ->get();
            $canDelete = Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'editor');

            return DataTables::of($invoices_by_vessel)
                ->addColumn('vessel_info', function ($invoices_by_vessel) {
                    // دمج الـ job_no و vessel_name معًا
                    return $invoices_by_vessel->vessel->job_no . ' | ' . ucfirst($invoices_by_vessel->vessel->vessel_name);
                })
                ->addColumn('action', function ($invoices_by_vessel) use ($canDelete) {
                    $action = '
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton' . $invoices_by_vessel->id . '" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $invoices_by_vessel->id . '">
                        <li>
                            <a class="dropdown-item" href="' . route('invoices.show', $invoices_by_vessel->id) . '">
                                <i class="bx bx-show-alt"></i> View
                            </a>
                        </li>';

                    if ($canDelete) {
                        $action .= '
                        <li>
                            <a class="dropdown-item" href="' . route('invoices.edit', $invoices_by_vessel->id) . '" data-id="' . $invoices_by_vessel->id . '">
                                <i class="bx bx-edit"></i> Edit
                            </a>
                        </li>
                        <li>
                            <form class="d-inline" action="' . route('invoices.destroy', $invoices_by_vessel->id) . '" method="POST">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bx bx-trash"></i> Delete
                                </button>
                            </form>
                        </li>';
                    }

                    $action .= '</ul></div>';

                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('vessels.invoices.index', compact('vessel'));
    }




    /**
     * عرض نموذج إنشاء فاتورة جديدة.
     */
    public function create($vesselId)
    {
        // $this->authorize('create', Vessel::class); 
        $vessel = Vessel::findOrFail($vesselId);
        $fixedFees = FixedFee::all();
        return view('vessels.invoices.create', compact('vessel', 'fixedFees'));
    }


    /**
     * حفظ الفاتورة الجديدة وربط الرسوم بها.
     */
    // public function store(Request $request)
    // {
    //     // dd($request->fees);
    //     $yearMonth = now()->format('ym');

    //     $prefixes = [
    //         'proforma' => 'PRO-',  // الفاتورة التمهيدية
    //         'preliminary' => 'PRE-',  // الفاتورة المبدئية
    //         'final' => 'INV-',  // الفاتورة الرسمية
    //         'draft' => ''  // مسودة
    //     ];

    //     $pfx = $prefixes[$request->invoice_type] ?? '';

    //     if ($request->invoice_type !== 'draft') {
    //         $prefix = $pfx . $yearMonth;

    //         $latestInvNo = Invoice::where('invoice_number', 'like', $prefix . '%')->max('invoice_number');
    //         if ($latestInvNo) {
    //             $number = substr($latestInvNo, strlen($prefix)) + 1;
    //         } else {
    //             $number = 1;
    //         }

    //         $formattedNumber = str_pad($number, 4, '0', STR_PAD_LEFT);

    //         $invNo = $prefix . $formattedNumber;
    //     } else {
    //         $invNo = NULL;
    //     }



    //     // إنشاء الفاتورة أولاً مع قيم مؤقتة
    //     $invoice = Invoice::create([
    //         'invoice_number' => $invNo,
    //         'vessel_id'      => $request->vessel_id,
    //         'invoice_type'   => $request->invoice_type,
    //         'call_type'      => $request->call_type,
    //         'invoice_date'   => $request->invoice_date,
    //         'currency'       => $request->currency,
    //         'sub_total'      => 0,
    //         'tax_total'      => 0,
    //         'grand_total'    => 0,
    //     ]);

    //     $subTotal = 0;
    //     $taxTotal = 0;

    //     foreach ($request->fees as $feeData) {
    //         if (empty($feeData['fixed_fee_id'])) {
    //             continue;
    //         }

    //         $fee = FixedFee::find($feeData['fixed_fee_id']);
    //         if (!$fee) {
    //             continue;
    //         }

    //         // تمرير القيم المطلوبة للحساب
    //         $input = [
    //             'loa'         => $feeData['loa'] ?? null,
    //             'gt'          => $feeData['gt'] ?? null,
    //             'hours'       => $feeData['hours'] ?? null,
    //             'day'         => $feeData['day'] ?? null,
    //             'quantity'    => $feeData['quantity'] ?? 1,
    //             'base_amount' => $feeData['base_amount'] ?? null,
    //         ];

    //         // حساب المبلغ بناءً على نوع قاعدة التسعير
    //         switch ($fee->pricing_rule) {
    //             case 'fixed':
    //                 $amount = $fee->amount;
    //                 $quantity = 1;
    //                 break;
    //             case 'loa':
    //                 $amount = $fee->amount;
    //                 $quantity = $input['loa'] ?? 0;
    //                 break;
    //             case 'gt':
    //                 $amount = $fee->amount;
    //                 $quantity = $input['gt'] ?? 0;
    //                 break;
    //             case 'time':
    //                 $amount = $fee->amount;
    //                 $quantity = $input['hours'] ?? 0;
    //                 break;
    //             case 'day':
    //                 $amount = $fee->amount;
    //                 $quantity = $input['day'] ?? 0;
    //                 break;
    //             case 'quantity':
    //                 $amount = $fee->amount;
    //                 $quantity = $input['quantity'] ?? 1;
    //                 break;
    //             case 'percentage':
    //                 $baseAmount = $input['base_amount'] ?? 0;
    //                 $amount = $baseAmount * ($fee->amount / 100);
    //                 $quantity = 1;
    //                 break;
    //             default:
    //                 $amount = 0;
    //                 $quantity = 1;
    //         }

    //         $description = $feeData['description'] ?? null;
    //         $discountPercent = (float) ($feeData['discount'] ?? 0);
    //         $tax_rate = isset($feeData['tax_rate']) ? (float)$feeData['tax_rate'] : 0;

    //         $lineSubtotal = $quantity * $amount;
    //         $lineDiscount = $lineSubtotal * ($discountPercent / 100);
    //         $afterDiscount = $lineSubtotal - $lineDiscount;
    //         $lineTax = $afterDiscount * ($tax_rate / 100);
    //         $lineTotal = $afterDiscount + $lineTax;

    //         $invoice->invoiceFees()->create([
    //             'fixed_fee_id' => $fee->id,
    //             'description'  => $description,
    //             'quantity'     => $quantity,
    //             'amount'       => $amount,
    //             'tax_rate'     => $tax_rate,
    //             'discount'     => $discountPercent,
    //             'total'        => $lineTotal,
    //         ]);

    //         $subTotal += $afterDiscount;
    //         $taxTotal += $lineTax;
    //     }

    //     $grandTotal = $subTotal + $taxTotal;
    //     $invoice->update([
    //         'sub_total'   => $subTotal,
    //         'tax_total'   => $taxTotal,
    //         'grand_total' => $grandTotal,
    //     ]);

    //     return redirect()->route('vessels.invoices.index', $request->vessel_id)->with('success', 'Created successfully.');
    // }

    /**
     * عرض تفاصيل فاتورة معينة.
     */
    public function show($id)
    {
        $invoice = Invoice::with('vessel', 'fixedFees')->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }


    /**
     * عرض نموذج تحرير فاتورة.
     */
    public function edit($id)
    {
        // $this->authorize('update', Invoice::class);
        // جلب الفاتورة مع الرسوم المرتبطة بها
        $invoice = Invoice::with('fees')->findOrFail($id);
        // جلب جميع السفن لاستخدامها في قائمة الاختيار (إن وجدت)
        $vessels = Vessel::all();
        // جلب الرسوم الثابتة للاختيار منها
        $fixedFees = FixedFee::all();
        // استخراج معرفات الرسوم المرتبطة بهذه الفاتورة (للاستخدام في التمييز داخل الفورم)
        $invoiceFees = $invoice->fees->pluck('id')->toArray();
        return view('invoices.edit', compact('invoice', 'vessels', 'invoiceFees'));
    }


    /*
     * تحديث بيانات الفاتورة والرسوم المرتبطة بها.
     */

    public function update(Request $request, $id)
    {
        // $this->authorize('update', Invoice::class);
        $request->validate([
            'vessel_id'    => 'required|exists:vessels,id',
            'invoice_date' => 'required|date',
            'invoice_type' => $request->invoice_type == 'draft' ? 'required|in:draft,proforma,preliminary,final' : '',
        ]);
        $invoice = Invoice::findOrFail($request->invoice);
        if ($invoice->invoice_type === 'draft' && $request->invoice_type !== 'draft') {
            $yearMonth = now()->format('ym');
            $prefixes = [
                'proforma' => 'PRO-',  // الفاتورة التمهيدية
                'preliminary' => 'PRE-',  // الفاتورة المبدئية
                'final' => 'INV-',  // الفاتورة الرسمية
                'draft' => ''  // مسودة
            ];
            $pfx = $prefixes[$request->invoice_type] ?? '';
            $prefix = $pfx . $yearMonth;
            $latestInvNo = Invoice::where('invoice_number', 'like', $prefix . '%')->max('invoice_number');
            if ($latestInvNo) {
                $number = substr($latestInvNo, strlen($prefix)) + 1;
            } else {
                $number = 1;
            }
            $formattedNumber = str_pad($number, 4, '0', STR_PAD_LEFT);
            $invNo = $prefix . $formattedNumber;
            $invoice->invoice_number = $invNo;
        }
        $invoice->update($request->only(['invoice_type', 'call_type', 'vessel_id', 'invoice_date']));
        $invoice->invoiceFees()->delete();

        $subTotal = 0;
        $totalTax = 0;

        if ($request->has('fees')) {
            foreach ($request->fees as $fee) {
                $quantity  = (float) ($fee['quantity'] ?? 1);
                $amount    = (float) ($fee['amount'] ?? 0);
                $discount  = (float) ($fee['discount'] ?? 0); // كنسبة مئوية
                $taxRate   = (float) ($fee['tax_rate'] ?? 0);

                $lineAmount = $quantity * $amount;
                $discountAmount = ($discount / 100) * $lineAmount;
                $netAmount = $lineAmount - $discountAmount;
                $taxAmount = ($taxRate / 100) * $netAmount;

                $subTotal += $netAmount;
                $totalTax += $taxAmount;

                InvoiceFee::create([
                    'invoice_id'    => $invoice->id,
                    'fixed_fee_id'  => $fee['fixed_fee_id'],
                    'description'   => $fee['description'] ?? null,
                    'quantity'      => $quantity,
                    'amount'        => $amount,
                    'discount'      => $discount,
                    'tax_rate'      => $taxRate,
                ]);
            }
        }

        $grandTotal = $subTotal + $totalTax;

        $invoice->update([
            'sub_total'   => $subTotal,
            'tax_total'   => $totalTax,
            'grand_total' => $grandTotal,
        ]);

        return redirect()->route('vessels.invoices.index', $request->vessel_id)->with('success', 'Updated successfully.');
    }


    /**
     * حذف فاتورة.
     */
    public function destroy($id)
    {
        // $this->authorize('delete', Vessel::class);
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'تم حذف الفاتورة بنجاح.');
    }







    public function getFeeDetails($id)
    {
        $fee = FixedFee::findOrFail($id);

        return response()->json([
            'name' => $fee->name,
            'description' => $fee->description,
            'amount' => $fee->amount,
            'tax' => $fee->tax,
        ]);
    }

































    /**
     * حفظ الفاتورة الجديدة وربط الرسوم بها.
     * 
     * يدعم كلاً من نظام التسعير البسيط القديم ونظام قواعد التسعير المتقدم الجديد.
     * 
     * @param Request $request طلب HTTP يحتوي على بيانات الفاتورة والرسوم
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // إنشاء رقم الفاتورة
        $yearMonth = now()->format('ym');

        $prefixes = [
            'proforma' => 'PRO-',  // الفاتورة التمهيدية
            'preliminary' => 'PRE-',  // الفاتورة المبدئية
            'final' => 'INV-',  // الفاتورة الرسمية
            'draft' => ''  // مسودة
        ];

        $pfx = $prefixes[$request->invoice_type] ?? '';

        if ($request->invoice_type !== 'draft') {
            $prefix = $pfx . $yearMonth;

            $latestInvNo = Invoice::where('invoice_number', 'like', $prefix . '%')->max('invoice_number');
            if ($latestInvNo) {
                $number = substr($latestInvNo, strlen($prefix)) + 1;
            } else {
                $number = 1;
            }

            $formattedNumber = str_pad($number, 4, '0', STR_PAD_LEFT);

            $invNo = $prefix . $formattedNumber;
        } else {
            $invNo = NULL;
        }

        // إنشاء الفاتورة أولاً مع قيم مؤقتة
        $invoice = Invoice::create([
            'invoice_number' => $invNo,
            'vessel_id'      => $request->vessel_id,
            'invoice_type'   => $request->invoice_type,
            'call_type'      => $request->call_type,
            'invoice_date'   => $request->invoice_date,
            'currency'       => $request->currency,
            'sub_total'      => 0,
            'tax_total'      => 0,
            'grand_total'    => 0,
        ]);

        $subTotal = 0;
        $taxTotal = 0;

        // الحصول على معلومات السفينة للاستخدام في سياق التسعير
        $vessel = null;
        if ($request->vessel_id) {
            $vessel = \App\Models\Vessel::find($request->vessel_id);
        }

        foreach ($request->fees as $feeData) {
            if (empty($feeData['fixed_fee_id'])) {
                continue;
            }

            $fee = FixedFee::find($feeData['fixed_fee_id']);
            if (!$fee) {
                continue;
            }

            // تمرير القيم المطلوبة للحساب
            $input = [
                'loa'         => $feeData['loa'] ?? null,
                'gt'          => $feeData['gt'] ?? null,
                'hours'       => $feeData['hours'] ?? null,
                'day'         => $feeData['day'] ?? null,
                'quantity'    => $feeData['quantity'] ?? 1,
                'base_amount' => $feeData['base_amount'] ?? null,
            ];

            // إنشاء سياق التسعير للاستخدام مع نظام قواعد التسعير المتقدم
            $pricingContext = [
                'vessel_type'   => $vessel ? $vessel->type : null,
                'gt_size'       => $input['gt'] ?? ($vessel ? $vessel->gt_size : 0),
                'loa'           => $input['loa'] ?? ($vessel ? $vessel->loa : 0),
                'stay_duration' => $input['day'] ?? 0,
                'service_hours' => $input['hours'] ?? 0,
                'quantity'      => $input['quantity'] ?? 1,
                'port'          => $request->port ?? null,
                'call_type'     => $request->call_type ?? null,
            ];

            // محاولة استخدام نظام قواعد التسعير المتقدم أولاً إذا كان متاحًا
            $amount = 0;
            $quantity = 1;
            $usedAdvancedPricing = false;

            // التحقق مما إذا كان الرسم يحتوي على معرف فئة التعريفة
            if (isset($fee->tariff_category_id) && $fee->tariff_category_id) {
                try {
                    // الحصول على فئة التعريفة
                    $tariffCategory = \App\Models\TariffCategory::find($fee->tariff_category_id);

                    if ($tariffCategory) {
                        // استخدام خدمة التسعير لحساب السعر
                        $pricingResult = $this->pricingService->calculatePrice(
                            $tariffCategory->code,
                            $pricingContext
                        );

                        // استخدام النتيجة من خدمة التسعير
                        $amount = $pricingResult['calculated_price'] ?? $pricingResult['base_rate'] ?? 0;
                        $usedAdvancedPricing = true;

                        // تسجيل استخدام نظام التسعير المتقدم
                        Log::info("Used advanced pricing for fee {$fee->id}", [
                            'tariff_category' => $tariffCategory->code,
                            'result' => $pricingResult
                        ]);
                    }
                } catch (\Exception $e) {
                    // تسجيل الخطأ واستخدام نظام التسعير البسيط كخطة بديلة
                    Log::warning("Failed to use advanced pricing for fee {$fee->id}: {$e->getMessage()}");
                    $usedAdvancedPricing = false;
                }
            }

            // استخدام نظام التسعير البسيط إذا لم يتم استخدام نظام التسعير المتقدم
            if (!$usedAdvancedPricing) {
                // حساب المبلغ بناءً على نوع قاعدة التسعير
                switch ($fee->pricing_rule) {
                    case 'fixed':
                        $amount = $fee->amount;
                        $quantity = 1;
                        break;
                    case 'loa':
                        $amount = $fee->amount;
                        $quantity = $input['loa'] ?? 0;
                        break;
                    case 'gt':
                        $amount = $fee->amount;
                        $quantity = $input['gt'] ?? 0;
                        break;
                    case 'time':
                        $amount = $fee->amount;
                        $quantity = $input['hours'] ?? 0;
                        break;
                    case 'day':
                        $amount = $fee->amount;
                        $quantity = $input['day'] ?? 0;
                        break;
                    case 'quantity':
                        $amount = $fee->amount;
                        $quantity = $input['quantity'] ?? 1;
                        break;
                    case 'percentage':
                        $baseAmount = $input['base_amount'] ?? 0;
                        $amount = $baseAmount * ($fee->amount / 100);
                        $quantity = 1;
                        break;
                    default:
                        $amount = 0;
                        $quantity = 1;
                }
            }

            $description = $feeData['description'] ?? null;
            $discountPercent = (float) ($feeData['discount'] ?? 0);
            $tax_rate = isset($feeData['tax_rate']) ? (float)$feeData['tax_rate'] : 0;

            $lineSubtotal = $quantity * $amount;
            $lineDiscount = $lineSubtotal * ($discountPercent / 100);
            $afterDiscount = $lineSubtotal - $lineDiscount;
            $lineTax = $afterDiscount * ($tax_rate / 100);
            $lineTotal = $afterDiscount + $lineTax;

            // إنشاء بند الفاتورة مع معلومات إضافية عن قاعدة التسعير المستخدمة
            $invoiceFee = $invoice->invoiceFees()->create([
                'fixed_fee_id' => $fee->id,
                'description'  => $description,
                'quantity'     => $quantity,
                'amount'       => $amount,
                'tax_rate'     => $tax_rate,
                'discount'     => $discountPercent,
                'total'        => $lineTotal,
                'pricing_method' => $usedAdvancedPricing ? 'advanced' : 'simple',
                'pricing_context' => $usedAdvancedPricing ? json_encode($pricingContext) : null,
            ]);

            $subTotal += $afterDiscount;
            $taxTotal += $lineTax;
        }

        $grandTotal = $subTotal + $taxTotal;
        $invoice->update([
            'sub_total'   => $subTotal,
            'tax_total'   => $taxTotal,
            'grand_total' => $grandTotal,
        ]);

        return redirect()->route('vessels.invoices.index', $request->vessel_id)->with('success', 'Created successfully.');
    }

    /**
     * اختبار قواعد التسعير باستخدام بيانات اختبار
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function testPricingRules()
    {
        $testCases = [
            [
                'name' => 'رسوم المرساة لسفن البضائع التي تبقى ≤ 3 أيام',
                'tariff_category_code' => '5.1',
                'context' => [
                    'vessel_type' => 'CARGO',
                    'gt_size' => 5000,
                    'stay_duration' => 2
                ]
            ],
            [
                'name' => 'رسوم المرساة لسفن البضائع التي تبقى > 4 أيام',
                'tariff_category_code' => '5.2',
                'context' => [
                    'vessel_type' => 'CARGO',
                    'gt_size' => 5000,
                    'stay_duration' => 5
                ]
            ],
            [
                'name' => 'رسوم القناة للسفن المتوسطة',
                'tariff_category_code' => '6.5',
                'context' => [
                    'gt_size' => 750
                ]
            ],
            [
                'name' => 'رسوم الإرشاد للسفن الكبيرة',
                'tariff_category_code' => '7.3',
                'context' => [
                    'loa' => 180,
                    'service_hours' => 3
                ]
            ]
        ];

        $results = [];

        foreach ($testCases as $testCase) {
            try {
                $result = $this->pricingService->calculatePrice(
                    $testCase['tariff_category_code'],
                    $testCase['context']
                );

                $results[] = [
                    'test_case' => $testCase['name'],
                    'success' => true,
                    'result' => $result
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'test_case' => $testCase['name'],
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * الحصول على قواعد التسعير المطبقة لسفينة معينة
     * 
     * @param int $vesselId معرف السفينة
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplicableRulesForVessel($vesselId)
    {
        try {
            $rules = $this->pricingService->getApplicableRulesForVessel($vesselId);

            return response()->json([
                'success' => true,
                'data' => $rules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
