<?php

namespace App\Http\Controllers;

use App\Models\FixedFee;
use App\Models\Invoice;
use App\Models\InvoiceFee;
use App\Models\Vessel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $invoices = Invoice::with('vessel')->get();

            $canDelete = Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'editor');

            return DataTables::of($invoices)
                ->addColumn('vessel_info', function ($invoices) {
                    // دمج الـ job_no و vessel_name معًا
                    return $invoices->vessel->job_no . ' | ' . ucfirst($invoices->vessel->vessel_name);
                })
                ->addColumn('action', function ($invoices) use ($canDelete) {
                    $action = '
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton' . $invoices->id . '" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $invoices->id . '">
                        <li>
                            <a class="dropdown-item" href="' . route('invoices.show', $invoices->id) . '">
                                <i class="bx bx-show-alt"></i> View
                            </a>
                        </li>';

                    if ($canDelete) {
                        $action .= '
                        <li>
                            <a class="dropdown-item" href="' . route('invoices.edit', $invoices->id) . '" data-id="' . $invoices->id . '">
                                <i class="bx bx-edit"></i> Edit
                            </a>
                        </li>
                        <li>
                            <form class="d-inline" action="' . route('invoices.destroy', $invoices->id) . '" method="POST">
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

        return view('invoices.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $invoice = Invoice::with('invoiceFees')->findOrFail($id);
        // $invoice = Invoice::with('vessel', 'fixedFees')->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // $this->authorize('update', Invoice::class);
        $invoice = Invoice::with('fixedFees')->findOrFail($id);
        $vessels = Vessel::all();
        $fixedFees = FixedFee::all();
        $invoiceFees = $invoice->fees()->pluck('fixed_fee_id')->toArray(); // الرسوم المضافة سابقًا
        return view('invoices.edit', compact('invoice', 'vessels', 'fixedFees', 'invoiceFees'));
    }

    /**
     * Update the specified resource in storage.
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

        return redirect()->route('vessels.invoices.index', $request->vessel_id)->with('success', 'تم تحديث الفاتورة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // $this->authorize('delete', Invoice::class);
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Deleted successfuly.');
    }
}
