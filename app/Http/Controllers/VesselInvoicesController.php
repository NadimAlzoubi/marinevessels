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


class VesselInvoicesController extends Controller
{
    use AuthorizesRequests;

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
    public function store(Request $request)
    {
        $yearMonth = now()->format('ym');
        $pfx = $request->invoice_type == 'proforma' ? 'PRO-' : 'INV-';
        $prefix = $pfx . $yearMonth;

        $latestInvNo = Invoice::where('invoice_number', 'like', $prefix . '%')->max('invoice_number');
        if ($latestInvNo) {
            $number = substr($latestInvNo, strlen($prefix)) + 1;
        } else {
            $number = 1;
        }
        $formattedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
        $invNo = $prefix . $formattedNumber;

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

        foreach ($request->fees as $feeData) {
            if (empty($feeData['fixed_fee_id'])) {continue;}
            $fee = FixedFee::find($feeData['fixed_fee_id']);
            if (!$fee) {continue;}
            $quantity = (float) ($feeData['quantity'] ?? 1);
            $discountPercent = (float) ($feeData['discount'] ?? 0);
            $amount = $fee->amount;
            $tax_rate = isset($feeData['tax_rate']) ? (float)$feeData['tax_rate'] : (float)$fee->tax_rate;
            $lineSubtotal = $quantity * $amount;
            $lineDiscount = $lineSubtotal * ($discountPercent / 100);
            $afterDiscount = $lineSubtotal - $lineDiscount;
            $lineTax = $afterDiscount * ($tax_rate / 100);
            $lineTotal = $afterDiscount + $lineTax;
            $invoice->invoiceFees()->create([
                'fixed_fee_id' => $fee->id,
                'quantity'     => $quantity,
                'amount'       => $amount,
                'tax_rate'     => $tax_rate,
                'discount'     => $discountPercent,
                'total'        => $lineTotal,
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
            'invoice_type' => 'required|in:proforma,final',
            'vessel_id'    => 'required|exists:vessels,id',
            'invoice_date' => 'required|date',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->only(['invoice_type', 'call_type', 'vessel_id', 'invoice_date']));

        // إعادة ضبط الرسوم المرتبطة إذا لزم الأمر.
        // يمكنك حذف الرسوم القديمة وإعادة إضافتها أو تعديلها بناءً على احتياجاتك.
        // هنا مثال مبسط لحذف الرسوم القديمة وإعادة إضافتها:
        $invoice->fixedFees()->detach();

        $subTotal = 0;
        if ($request->has('fixed_fees')) {
            foreach ($request->fixed_fees as $feeId => $quantity) {
                if ($quantity > 0) {
                    $fixedFee = FixedFee::findOrFail($feeId);
                    $lineAmount = $fixedFee->amount * $quantity;
                    $subTotal += $lineAmount;

                    InvoiceFee::create([
                        'invoice_id'   => $invoice->id,
                        'fixed_fee_id' => $feeId,
                        'quantity'     => $quantity,
                        'discount'     => 0,
                    ]);
                }
            }
        }

        $taxTotal = $subTotal * 0.05;
        $grandTotal = $subTotal + $taxTotal;
        $invoice->update([
            'sub_total'   => $subTotal,
            'tax_total'   => $taxTotal,
            'grand_total' => $grandTotal,
        ]);

        return redirect()->route('invoices.index')->with('success', 'تم تحديث الفاتورة بنجاح.');
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
}
