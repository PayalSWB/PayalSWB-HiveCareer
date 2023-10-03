<?php

namespace Modules\Language\Http\Controllers;

use App\Models\LanguageData;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Modules\Language\Entities\Language;

class LanguageController extends Controller
{
    public function __construct()
    {
        $this->middleware('access_limitation')->only([
            'destroy', 'update',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index()
    {
        if (! userCan('setting.view')) {
            return abort(403);
        }

        return view('language::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        if (! userCan('setting.update')) {
            return abort(403);
        }
        // if (!enableModule('language')) {
        //     return abort(404);
        // }
        $path = base_path('Modules/Language/Resources/json/languages.json');
        $translations = json_decode(file_get_contents($path), true);

        return view('language::create', compact('translations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (! userCan('setting.update')) {
            return abort(403);
        }
        $request->validate(
            [
                'name' => 'required|unique:languages,name',
                'code' => 'required|unique:languages,code',
                'icon' => 'required|unique:languages,icon',
                'direction' => 'required',
            ],
            [
                'name.required' => 'You must select a language',
                'code.required' => 'You must select a language code',
                'icon.required' => 'You must select a flag',
                'direction.required' => 'You must select a direction',
                'name.unique' => 'This language already exists',
                'code.unique' => 'This code already exists',
                'icon.unique' => 'This flag already exists',
            ],
        );

        $language = Language::create([
            'name' => $request->name,
            'code' => $request->code,
            'icon' => $request->icon,
            'direction' => $request->direction,
        ]);

        if ($language) {
            $baseFile = base_path('resources/lang/en.json');
            $fileName = base_path('resources/lang/'.Str::slug($request->code).'.json');
            copy($baseFile, $fileName);

            flashSuccess(__('language_created_successfully_please_translate_the_language_as_per_your_need'));

            return redirect()->route('languages.view', $language->code);
        } else {
            flashError();

            return back();
        }
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('language::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit(Language $language)
    {
        if (! userCan('setting.update')) {
            return abort(403);
        }

        // if (!enableModule('language') || $language->code == 'en') {
        //     return abort(404);
        // }
        $path = base_path('Modules/Language/Resources/json/languages.json');
        $translations = json_decode(file_get_contents($path), true);

        return view('language::edit', compact('translations', 'language'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function update(Request $request, Language $language)
    {
        // validation
        $request->validate(
            [
                'name' => "required|unique:languages,name,{$language->id}",
                'code' => "required|unique:languages,code,{$language->id}",
                'icon' => "required|unique:languages,icon,{$language->id}",
                'direction' => 'required',
            ],
            [
                'name.required' => 'You must select a language',
                'code.required' => 'You must select a code',
                'icon.required' => 'You must select a flag',
                'direction.required' => 'You must select a direction',
                'name.unique' => 'This language already exists',
                'code.unique' => 'This code already exists',
                'icon.unique' => 'This flag already exists',
            ],
        );

        // rename file
        $oldFile = $language->code.'.json';
        $oldName = base_path('resources/lang/'.$oldFile);
        $newFile = Str::slug($request->code).'.json';
        $newName = base_path('resources/lang/'.$newFile);

        rename($oldName, $newName);

        // update
        $updated = $language->update([
            'name' => $request->name,
            'code' => $request->code,
            'icon' => $request->icon,
            'direction' => $request->direction,
        ]);

        $updated ? flashSuccess(__('language_updated_successfully')) : flashError();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy(Language $language)
    {
        if (! userCan('setting.update')) {
            return abort(403);
        }

        if (config('zakirsoft.default_language') == $language->code) {
            flashError("You can't delete default language");

            return back();
        }

        // delete file
        if (File::exists(base_path('resources/lang/'.$language->code.'.json'))) {
            File::delete(base_path('resources/lang/'.$language->code.'.json'));
        }

        $deleted = $language->delete();

        $deleted ? flashSuccess(__('language_deleted_successfully')) : flashError();

        return back();
    }

    public function setLanguage(Request $request)
    {
        // session()->put('set_lang', $request->code);
        // app()->setLocale($request->code);

        if (config('zakirsoft.default_language') != $request->code) {
            forgetCache('default_language');
            envReplace('APP_DEFAULT_LANGUAGE', $request->code);
        }

        // menu list cache clear
        Cache::forget('menu_lists');

        flashSuccess(__('default_language_set_successfully'));

        return back();
    }

    public function syncLanguage(Language $language)
    {
        try {
            // English Language Translations
            $en_path = base_path('resources/lang/en.json');
            $en_translations = json_decode(file_get_contents($en_path), true);

            // Sync Language Translations
            $lang_path = base_path("resources/lang/{$language->code}.json");
            $lang_translations = json_decode(file_get_contents($lang_path), true);

            // Unique Values between English and Sync Language
            $unique_values = collect($en_translations)->diffKeys($lang_translations);

            // if (!count($unique_values)) {
            //     flashWarning(__('nothing_to_sync'));
            //     return back();
            // }

            // Merge Unique Values with Sync Language
            $translations = array_merge($lang_translations, json_decode($unique_values, true));

            // Update sync language json
            file_put_contents(base_path("resources/lang/{$language->code}.json"), json_encode($translations, JSON_UNESCAPED_UNICODE));

            $this->syncLanguageJson($language->code);

            flashSuccess(__('language_sync_successfully'));

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }

    private function syncLanguageJson($code)
    {
        $value = LanguageData::where('code', $code)->first();

        $currentJsonPath = base_path('resources/lang/'.$value->code.'.json');
        $currentJson = json_decode(File::get($currentJsonPath), true);
        $databaseJson = json_decode($value->data, true);

        // Merge the current JSON with the database JSON, keeping existing keys
        $mergedJson = array_merge($databaseJson, $currentJson);

        // Save the merged JSON back to the database
        $value->update(['data' => json_encode($mergedJson)]);

        // Save the merged JSON back to the JSON file
        File::put($currentJsonPath, json_encode($mergedJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
