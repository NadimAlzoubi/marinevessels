<?php

namespace App\Http\Controllers;

use App\Models\VesselsReportFields;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class VesselsReportFieldsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $fields = VesselsReportFields::query();
            return DataTables::of($fields)
                ->addColumn('action', function ($field) {
                    return '
                        <a href="' . route('vessels_report_fields.show', $field->id) . '" class="btn btn-info btn-sm"><i class="bx bx-show"></i></a>
                        <a href="' . route('vessels_report_fields.edit', $field->id) . '" data-id="' . $field->id . '" class="btn btn-primary btn-sm edit-button"><i class="bx bx-edit"></i></a>
                        <form action="' . route('vessels_report_fields.destroy', $field->id) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-danger btn-sm"><i class="bx bx-trash"></i></button>
                        </form>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('vessels_report_fields.index');
    }

    /**
     * Show the specified resource for editing.
     */
    public function show($id)
    {
        $field = VesselsReportFields::findOrFail($id);

        if (request()->ajax()) {
            return response()->json($field);
        }

        return view('vessels_report_fields.show', compact('field'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string',
            'name' => 'required|string',
            'type' => 'required|string',
            'placeholder' => 'required|string',
            'category' => 'required|string',
        ]);

        VesselsReportFields::create($request->all());

        return response()->json([
            'message' => 'Vessel created successfully!'
        ], 200);
    }

    public function edit($id)
    {
        $field = VesselsReportFields::findOrFail($id);
        return view('vessels_report_fields.edit', compact('field'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'required|string',
            'name' => 'required|string',
            'type' => 'required|string',
            'placeholder' => 'required|string',
            'category' => 'required|string',
        ]);

        $field = VesselsReportFields::findOrFail($id);
        $field->update($request->all());

        return redirect()->route('vessels_report_fields.index')->with('success', 'Vessel updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $field = VesselsReportFields::findOrFail($id);
        $field->delete();
        return redirect()->route('vessels_report_fields.index')->with('success', 'Vessel deleted successfully.');
    }
}
