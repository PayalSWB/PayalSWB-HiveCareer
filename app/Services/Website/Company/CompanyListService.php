<?php

namespace App\Services\Website\Company;

use App\Models\Company;
use App\Models\IndustryType;
use App\Models\OrganizationType;
use App\Models\TeamSize;
use Modules\Location\Entities\Country;

class CompanyListService
{
    /**
     * Get company list
     */
    public function execute($request): array
    {
        $query = Company::with('user', 'user.contactInfo', 'industry.translations')
            ->withCount([
                'jobs as activejobs' => function ($q) {
                    $q->where('status', 'active');

                    $selected_country = session()->get('selected_country');
                    if ($selected_country && $selected_country != null && $selected_country != 'all') {
                        $country = selected_country()->name;
                        $q->where('country', 'LIKE', "%$country%");
                    } else {
                        $setting = loadSetting();
                        if ($setting->app_country_type == 'single_base') {
                            if ($setting->app_country) {
                                $country = Country::where('id', $setting->app_country)->first();
                                if ($country) {
                                    $q->where('country', 'LIKE', "%$country->name%");
                                }
                            }
                        }
                    }
                },
            ])
            ->withCount([
                'bookmarkCandidateCompany as candidatemarked' => function ($q) {
                    $q->where('user_id', auth()->id());
                },
            ])
            ->withCasts(['candidatemarked' => 'boolean'])
            ->active();

        // Keyword search
        if ($request->has('keyword') && $request->keyword != null) {
            $keyword = $request->keyword;
            $query->whereHas('user', function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%$keyword%");
            });
        }

        // location search
        if ($request->has('lat') && $request->has('long') && $request->lat != null && $request->long != null) {
            $location = $request->location ? $request->location : '';
            $query->where('country', 'LIKE', "%$location%");
        }

        // Industry Type
        if ($request->has('industry_type') && $request->industry_type !== null) {
            $query->where('industry_type_id', $request->industry_type);
        }

        // Organization Type
        if ($request->has('organization_type') && $request->organization_type !== null) {
            $query->where('organization_type_id', $request->organization_type);
        }

        // Team Size
        if ($request->has('team_size') && $request->team_size !== null) {
            $query->where('team_size_id', $request->team_size);
        }

        $companies = $query->latest('activejobs')->paginate(12);

        $industry_types = IndustryType::all()->map(function ($data) {
            return ['id' => $data->id, 'name' => $data->name];
        })->sortBy('name');
        $organization_types = OrganizationType::all()->map(function ($data) {
            return ['id' => $data->id, 'name' => $data->name];
        })->sortBy('name');

        $team_sizes = TeamSize::all(['id', 'name', 'slug']);

        return [
            'companies' => $companies,
            'industry_types' => $industry_types,
            'organization_types' => $organization_types,
            'team_sizes' => $team_sizes,
        ];
    }
}
