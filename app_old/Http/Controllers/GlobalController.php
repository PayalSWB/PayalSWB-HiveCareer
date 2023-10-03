<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class GlobalController extends Controller
{
    public function fetchCurrentTranslatedText()
    {
        $json = base_path('resources/lang/'.app()->getLocale().'.json');

        if (file_exists($json)) {
            $json = json_decode(file_get_contents($json), true);
        } else {
            $json = [];
        }

        return $json;
    }

    public function checkUsername(Request $request, $name)
    {
        if (! auth('user')->check()) {
            abort(404);
        }

        if ($request->type == 'company_username') {
            $username_exists = User::where('username', $name)->where('id', '!=', auth()->id())->exists();

            return $username_exists ? 'true' : 'false';
        }

        abort(404);
    }
}
