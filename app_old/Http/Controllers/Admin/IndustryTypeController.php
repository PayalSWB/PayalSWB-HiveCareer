<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\IndustryTypeImport;
use App\Models\IndustryType;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Language\Entities\Language;

class IndustryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(! userCan('industry_types.view'), 403);

        $industrytypes = IndustryType::all();
        $app_language = Language::latest()->get(['code','name']);

        return view('backend.industryType.index', compact('industrytypes', 'app_language'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(! userCan('industry_types.create'), 403);

        // validation
        $app_language = Language::latest()->get(['code']);
        $validate_array = [];
        foreach ($app_language as $language) {
            $validate_array['name_'.$language->code] = 'required|string|max:255';
        }
        $this->validate($request, $validate_array);

        // saving the data
        $industry_type = new IndustryType();
        $industry_type->save();

        foreach ($request->except('_token') as $key => $value) {
            $industry_type->translateOrNew(str_replace('name_', '', $key))->name = $value;
            $industry_type->save();
        }

        flashSuccess(__('industry_type_created_successfully'));

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(IndustryType $industryType)
    {
        abort_if(! userCan('industry_types.update'), 403);

        $industrytypes = IndustryType::all();
        $app_language = Language::latest()->get(['code']);

        return view('backend.industryType.index', compact('industryType', 'industrytypes', 'app_language'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IndustryType $industryType)
    {
        abort_if(! userCan('industry_types.update'), 403);

        // validation
        $app_language = Language::latest()->get(['code']);
        $validate_array = [];
        foreach ($app_language as $language) {
            $validate_array['name_'.$language->code] = 'required|string|max:255';
        }
        $this->validate($request, $validate_array);

        // saving the data
        foreach ($request->except(['_token', '_method']) as $key => $value) {
            $industryType->translateOrNew(str_replace('name_', '', $key))->name = $value;
            $industryType->save();
        }

        flashSuccess(__('industry_type_updated_successfully'));

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(IndustryType $industryType)
    {
        abort_if(! userCan('industry_types.delete'), 403);

        if ($industryType && $industryType->companies->count()) {
            flashError(__('industry_has_jobs'));

            return back();
        }

        $industryType->delete();

        flashSuccess(__('industry_type_deleted_successfully'));

        return redirect()->route('industryType.index');
    }

    /**
     * Bulk data Import
     *
     * @return \Illuminate\Http\Response
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:csv,xlsx,xls',
        ]);

        try {
            Excel::import(new IndustryTypeImport(), $request->import_file);

            flashSuccess(__('industry_type_imported_successfully'));

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }
}
