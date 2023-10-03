<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidateLanguage;
use Illuminate\Http\Request;

class CandidateLanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        abort_if(! userCan('candidate-language.view'), 403);

        $candidate_languages = CandidateLanguage::latest('id')
            ->when($request->has('keyword') && $request->keyword != null, function ($q) use ($request) {
                $q->where('name', 'LIKE', "%$request->keyword%");
            })
            ->paginate(20)
            ->withQueryString();

        return view('backend.candidate.language.index', compact('candidate_languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort_if(! userCan('candidate-language.create'), 403);

        $request->validate([
            'name' => 'required',
        ]);

        CandidateLanguage::create(['name' => $request->name]);

        flashSuccess(__('language_created_successfully'));

        return redirect()->route('admin.candidate.language.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(! userCan('candidate-language.update'), 403);

        $item = CandidateLanguage::FindOrFail($id);
        $candidate_languages = CandidateLanguage::latest('id')->paginate(20);

        return view('backend.candidate.language.index', compact('candidate_languages', 'item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        abort_if(! userCan('candidate-language.update'), 403);

        $request->validate(['name' => 'required']);

        $item = CandidateLanguage::FindOrFail($id);
        $item->update(['name' => $request->name]);

        flashSuccess(__('language_updated_successfully'));

        return redirect()->route('admin.candidate.language.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(! userCan('candidate-language.delete'), 403);

        $item = CandidateLanguage::FindOrFail($id);
        $item->delete();

        flashSuccess(__('language_deleted_successfully'));

        return redirect()->route('admin.candidate.language.index');
    }
}
