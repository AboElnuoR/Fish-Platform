<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function edit(Branch $branch)
    {
        return $branch;
    }

    /**
     * Specify the branch form's rules.
     *
     * @return array
     */
    private function rules()
    {
        return [
            'Governorate_ID' => 'required|exists:governorate',
            'Locality_ID' => 'required|exists:locality',
            'Village_ID' => request('Village_ID') ? 'required|exists:village' : '',
            'Address' => 'sometimes|nullable|string',
            'Mob' => 'sometimes|nullable|numeric',
            'Tel' => 'required|numeric',
            'Fax' => 'sometimes|nullable|numeric',
            'Email' => 'sometimes|nullable|string|email',
            'Web' => 'sometimes|nullable|string',
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate($this->rules());
        $branch->update($data);
        $success = 'تم تعديل الفرع بنجاح';
        return back()->with(compact('success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Branch  $branch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        $success = 'تم حذف الفرع بنجاح';
        return back()->with(compact('success'));
    }
}
