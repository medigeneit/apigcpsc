<?php

namespace App\Http\Controllers;

use App\Models\LegallyBindingContract;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LegallyBindingContractController extends Controller
{
    public function index()
    {
        return LegallyBindingContract::get();
    }

    public function showList()
    {
        $terms_conditions = LegallyBindingContract::property('terms_conditions')->first();
        $about = LegallyBindingContract::property('about')->first();
        return [
            'terms_conditions' => $terms_conditions,
            'about' => $about,
        ];
    }

    public function store(Request $request)
    {

        $meta = LegallyBindingContract::withTrashed()->create($this->validation($request));

        return [
            'success' => true,
            'message' => "Record successfully created",
            'legally-binding-contract' => $meta
        ];
    }

    public function edit(LegallyBindingContract $legally_binding_contract)
    {
        return $legally_binding_contract;
    }
    public function update(Request $request, LegallyBindingContract $legally_binding_contract)
    {
        // $legally_binding_contract->update($this->validation($request, $legally_binding_contract->id));

        $newUser = LegallyBindingContract::updateOrCreate([
            'id'   => $legally_binding_contract->id,
        ], [
            'key'     => $request->key,
            'value' => $request->value,
        ]);
        return $newUser;
    }

    public function destroy(LegallyBindingContract $legally_binding_contract)
    {
        return $legally_binding_contract->delete();
    }
    private function validation($request, $id = '')
    {
        return $request->validate([
            'key' => [
                'required',
                'string',
                Rule::unique(LegallyBindingContract::class, 'key')
                    ->ignore($id),
            ],
            'value' => [
                'required',
                'string',
            ],
        ]);
    }
}
