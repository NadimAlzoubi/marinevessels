<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clients = Client::query();
            $canDelete = Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'editor');

            return DataTables::of($clients)
                ->addColumn('action', function ($client) use ($canDelete) {
                    $action = '
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton' . $client->id . '" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $client->id . '">
                                <li>
                                    <a class="dropdown-item" href="' . route('clients.show', $client->id) . '">
                                <i class="bx bx-show-alt"></i> View
                                    </a>
                                </li>';
                    if ($canDelete) {
                        $action .= '
                                <li>
                                    <a class="dropdown-item" href="' . route('clients.edit', $client->id) . '" data-id="' . $client->id . '">
                                        <i class="bx bx-edit"></i> Edit
                                    </a>
                                </li>
                                <li>
                                    <form class="d-inline" action="' . route('clients.destroy', $client->id) . '" method="POST">
                                        ' . csrf_field() . '
                                        ' . method_field('DELETE') . '
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bx bx-trash"></i> Delete
                                        </button>
                                    </form>
                                </li>';
                    }
                    $action .= '</ul>
                        </div>
                    ';
                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('clients.index');
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'trn' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'trn' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'type' => 'nullable|string|max:100',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Deleted successfully.');
    }
}
