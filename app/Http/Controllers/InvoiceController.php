<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Vessel;
use App\Models\InvoiceFee;
use App\Models\FixedFee;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * عرض جميع الفواتير.
     */
    public function index()
    {
        // جلب الفواتير مع بيانات السفينة والعلاقات إن وجدت
        $invoices = Invoice::with('vessel', 'fixedFees')->get();
        return view('invoices.index', compact('invoices'));
    }

    /**
     * عرض نموذج إنشاء فاتورة جديدة.
     */
    public function create()
    {
        // جلب بيانات السفن والرسوم الثابتة لإظهارها في النموذج
        $vessels = Vessel::all();
        $fixedFees = FixedFee::all();
        return view('invoices.create', compact('vessels', 'fixedFees'));
    }

    /**
     * حفظ الفاتورة الجديدة وربط الرسوم بها.
     */
    public function store(Request $request)
    {
        $invoice = Invoice::create([
            'vessel_id' => $request->vessel_id,
            'invoice_type' => $request->invoice_type,
            'total_amount' => 0, // سنحسب المجموع لاحقًا
        ]);

        $totalAmount = 0;

        foreach ($request->fees as $feeId => $feeData) {
            if (!isset($feeData['selected']) || empty($feeData['fee_id'])) {
                continue;
            }

            $fee = FixedFee::find($feeData['fee_id']);
            if (!$fee) {
                continue;
            }

            $quantity = $feeData['quantity'] ?? 1;
            $amount = $feeData['amount'] ?? 0;
            $tax = $feeData['tax'] ?? 0;

            $total = ($quantity * $amount) + ($quantity * $amount * ($tax / 100));

            $invoice->fees()->create([
                'fee_id' => $fee->id,
                'name' => $fee->name,
                'description' => $feeData['description'] ?? '',
                'quantity' => $quantity,
                'amount' => $amount,
                'tax' => $tax,
                'total' => $total,
            ]);

            $totalAmount += $total;
        }

        $invoice->update(['total_amount' => $totalAmount]);

        return redirect()->route('invoices.index')->with('success', 'تم إنشاء الفاتورة بنجاح');
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
        $invoice = Invoice::with('fixedFees')->findOrFail($id);
        $vessels = Vessel::all();
        $fixedFees = FixedFee::all();
        $invoiceFees = $invoice->fees()->pluck('fee_id')->toArray(); // الرسوم المضافة سابقًا


        return view('invoices.edit', compact('invoice', 'vessels', 'fixedFees', 'invoiceFees'));
    }

    /**
     * تحديث بيانات الفاتورة والرسوم المرتبطة بها.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'invoice_type' => 'required|in:proforma,final',
            'vessel_id'    => 'required|exists:vessels,id',
            'invoice_date' => 'required|date',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->only(['invoice_type', 'vessel_id', 'invoice_date']));

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
