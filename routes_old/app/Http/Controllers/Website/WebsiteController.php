<?php

namespace App\Http\Controllers\Website;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
use App\Models\Skill;
use App\Models\Company;
use App\Models\Candidate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Profession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Http\Traits\JobAble;
use Illuminate\Http\Request;
use App\Models\CandidateResume;
use Modules\Blog\Entities\Post;
use Modules\Plan\Entities\Plan;
use App\Models\CandidateLanguage;
use App\Http\Traits\CandidateAble;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Modules\Faq\Entities\FaqCategory;
use Modules\Blog\Entities\PostComment;
use Modules\Location\Entities\Country;
use Modules\Blog\Entities\PostCategory;
use Modules\Language\Entities\Language;
use App\Http\Traits\HasCountryBasedJobs;
use App\Traits\ResetCvViewsHistoryTrait;
use App\Services\Website\IndexPageService;
use App\Services\Website\PricePlanService;
use Illuminate\Notifications\Notification;
use Stevebauman\Location\Facades\Location;
use App\Services\Website\Job\JobListService;
use Illuminate\Contracts\Support\Renderable;
use App\Services\Website\RefundPolicyService;
use Modules\Testimonial\Entities\Testimonial;
use App\Services\Website\PrivacyPolicyService;
use App\Services\Website\TermsConditionService;
use App\Services\Website\Company\CompanyListService;
use App\Services\Website\Company\CompanyDetailsService;
use Modules\Currency\Entities\Currency as CurrencyModel;
use App\Notifications\Website\Candidate\ApplyJobNotification;
use App\Notifications\Website\Candidate\BookmarkJobNotification;
use App\Services\Website\Candidate\CandidateProfileDetailsService;
use Illuminate\Support\Facades\DB;

class WebsiteController extends Controller
{
    use CandidateAble, HasCountryBasedJobs, JobAble, ResetCvViewsHistoryTrait;

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function dashboard()
    {
        if (auth('user')->check() && authUser()->role == 'candidate') {
            return redirect()->route('candidate.dashboard');
        } elseif (auth('user')->check() && authUser()->role == 'company') {
            storePlanInformation();

            return redirect()->route('company.dashboard');
        }

        return redirect('login');
    }

    /**
     * Notification mark as read
     *
     * @param  Request  $request
     * @return void
     */
    public function notificationRead()
    {
        foreach (auth()->user()->unreadNotifications as $notification) {
            $notification->markAsRead();
        }

        return response()->json(true);
    }

    /**
     * Home page view
     *
     * @param  Request  $request
     * @return void
     */
    public function index()
    {
        $data = (new IndexPageService())->execute();

        return view('frontend.pages.index', $data);
    }

    /**
     * Terms and condition page view
     *
     * @param  Request  $request
     * @return void
     */
    public function termsCondition()
    {
        $data = (new TermsConditionService())->execute();

        return view('frontend.pages.terms-condition', $data);
    }

    /**
     * Privacy policy page view
     *
     * @param  Request  $request
     * @return void
     */
    public function privacyPolicy()
    {
        $data = (new PrivacyPolicyService())->execute();

        return view('frontend.pages.privacy-policy', $data);
    }

    /**
     * Refund policy page view
     *
     * @param  Request  $request
     * @return void
     */
    public function refundPolicy()
    {
        $data = (new RefundPolicyService())->execute();

        return view('frontend.pages.refund-policy', $data);
    }

    /**
     * Job page view
     *
     * @return void
     */
    public function jobs(Request $request)
    {
        $data = (new JobListService())->jobs($request);

        // For adding currency code
        $current_currency = currentCurrency();

        return view('frontend.pages.jobs', $data, compact('current_currency'));
    }

    public function loadmore(Request $request)
    {
        $data = (new JobListService())->loadMore($request);

        return view('components.website.job.load-more-jobs', compact('data'));
    }

    /**
     * Job category page view
     *
     * @param  string  $slug
     * @return void
     */
    public function jobsCategory(Request $request, $slug)
    {
        $data = (new JobListService())->categoryJobs($request, $slug);

        return view('frontend.pages.jobsCategory', $data);
    }

    /**
     * Job details page view
     *
     * @param  Request  $request
     * @param  string  $slug
     * @return void
     */
    public function jobDetails(Job $job)
    {
        if ($job->status == 'pending') {
            if (! auth('admin')->check()) {
                abort_if(! auth('user')->check(), 404);
                abort_if(authUser()->role != 'company', 404);
                abort_if(currentCompany()->id != $job->company_id, 404);
            }
        }

        $data = $this->getJobDetails($job);
        $data['questions'] = $job->questions;

        return view('frontend.pages.job-details', $data);
    }

    /**
     * Candidate page view
     *
     * @return void
     */
    public function candidates(Request $request)
    {
        abort_if(auth('user')->check() && authUser()->role == 'candidate', 404);

        $data['professions'] = Profession::all()->sortBy('name');
        $data['candidates'] = $this->getCandidates($request);
        $data['experiences'] = Experience::all();
        $data['educations'] = Education::all();
        $data['skills'] = Skill::all()->sortBy('name');

        // reset candidate cv views history
        $this->reset();

        return view('frontend.pages.candidates', $data);
    }

    /**
     * Candidate details page view
     *
     * @param  string  $username
     * @return void
     */
    public function candidateDetails(Request $request, $username)
    {
        $candidate = User::where('username', $username)
            ->with('candidate', 'contactInfo', 'socialInfo')
            ->firstOrFail();

        abort_if(auth('user')->check() && $candidate->id != auth('user')->id(), 404);

        if ($request->ajax) {
            return response()->json($candidate);
        }

        return view('frontend.pages.candidate-details', compact('candidate'));
    }

    /**
     * Candidate profile details
     *
     * @return Response
     */
    public function candidateProfileDetails(Request $request)
    {
        if (! auth('user')->check()) {
            return response()->json([
                'message' => __('if_you_perform_this_action_you_need_to_login_your_account_first_do_you_want_to_login_now'),
                'success' => false,
            ]);
        }

        $data = (new CandidateProfileDetailsService())->execute($request);

        return response()->json($data);
    }

    /**
     * Candidate application profile details
     *
     * @return Response
     */
    public function candidateApplicationProfileDetails(Request $request)
    {
        $candidate = User::where('username', $request->username)
            ->with(['contactInfo', 'socialInfo', 'candidate' => function ($query) {
                $query->with('experiences', 'educations', 'experience', 'coverLetter', 'education', 'profession', 'languages:id,name', 'skills', 'socialInfo');
            }])->firstOrFail();

        $candidate->candidate->birth_date = Carbon::parse($candidate->candidate->birth_date)->format('d F, Y');

        $languages = $candidate->candidate
            ->languages()
            ->pluck('name')
            ->toArray();
        $candidate_languages = $languages ? implode(', ', $languages) : '';

        $skills = $candidate->candidate->skills->pluck('name');
        $candidate_skills = $skills ? implode(', ', json_decode(json_encode($skills))) : '';

        return response()->json([
            'success' => true,
            'data' => $candidate,
            'skills' => $candidate_skills,
            'languages' => $candidate_languages,
        ]);
    }

    /**
     * Candidate download cv
     *
     * @return void
     */
    public function candidateDownloadCv(CandidateResume $resume)
    {
        $filePath = $resume->file;
        $filename = time().'.pdf';
        $headers = ['Content-Type: application/pdf', 'filename' => $filename];
        $fileName = rand().'-resume'.'.pdf';

        return response()->download($filePath, $fileName, $headers);
    }

    /**
     * Employer page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function employees(Request $request)
    {
        abort_if(auth('user')->check() && authUser()->role == 'company', 404);

        $data = (new CompanyListService())->execute($request);

        return view('frontend.pages.employees', $data);
    }

    /**
     * Employers details page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function employersDetails(User $user)
    {
        $data = (new CompanyDetailsService())->execute($user);

        return view('frontend.pages.employe-details', $data);
    }

    /**
     * About page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function about()
    {
        $testimonials = Testimonial::all();
        $companies = Company::count();
        $candidates = Candidate::count();

        return view('frontend.pages.about', compact('testimonials', 'companies', 'candidates'));
    }

    /**
     * Plan page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function pricing()
    {
        abort_if(auth('user')->check() && authUser()->role == 'candidate', 404);
        $plans = Plan::active()->get();
        $plan_descriptions = $plans->pluck('descriptions')->flatten();

        $current_language = currentLanguage();
        $current_currency = currentCurrency();
        $current_language_code = $current_language ? $current_language->code : config('zakirsoft.default_language');

        if ($current_language_code) {
            $plans->load([
                'descriptions' => function ($q) use ($current_language_code) {
                    $q->where('locale', $current_language_code);
                },
            ]);
        }

        return view('frontend.pages.pricing', compact('plans', 'current_language', 'plan_descriptions','current_currency'));
    }

    /**
     * Plan details page
     *
     * @param  string  $label
     * @return void
     */
    public function planDetails($label)
    {
        abort_if(! auth('user')->check(), 404);
        abort_if(auth('user')->check() && auth('user')->user()->role == 'candidate', 404);

        $data = (new PricePlanService())->details($label);

        return view('frontend.pages.plan-details', $data);
    }

    /**
     * Contact page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function contact()
    {
        return view('frontend.pages.contact');
    }

    /**
     * Faq page
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function faq()
    {
        $faq_categories = FaqCategory::with([
            'faqs' => function ($q) {
                $q->where('code', currentLangCode());
            },
        ])->get();

        return view('frontend.pages.faq', compact('faq_categories'));
    }

    public function toggleBookmarkJob(Job $job)
    {
        $check = $job->bookmarkJobs()->toggle(auth('user')->user()->candidate);

        if ($check['attached'] == [1]) {
            $user = auth('user')->user();
            // make notification to company candidate bookmark job
            Notification::send($job->company->user, new BookmarkJobNotification($user, $job));
            // make notification to candidate for notify
            if (auth()->user()->recent_activities_alert) {
                Notification::send(auth('user')->user(), new BookmarkJobNotification($user, $job));
            }
        }

        $check['attached'] == [1] ? ($message = __('job_added_to_favorite_list')) : ($message = __('job_removed_from_favorite_list'));

        flashSuccess($message);

        return back();
    }

    public function toggleApplyJob(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'resume_id' => 'required',
                'cover_letter' => 'required',
            ],
            [
                'resume_id.required' => 'Please select resume',
                'cover_letter.required' => 'Please enter cover letter',
            ],
        );

        if ($validator->fails()) {
            flashError($validator->errors()->first());

            return back();
        }

        if (auth('user')->user()->candidate->profile_complete != 0) {
            flashError(__('complete_your_profile_before_applying_to_jobs_add_your_information_resume_and_profile_picture_for_a_better_chance_of_getting_hired'));

            return redirect()->route('candidate.dashboard');
        }

        $candidate = auth('user')->user()->candidate;
        $job = Job::find($request->id);

        DB::table('applied_jobs')->insert([
            'candidate_id' => $candidate->id,
            'job_id' => $job->id,
            'cover_letter' => $request->cover_letter,
            'candidate_resume_id' => $request->resume_id,
            'application_group_id' => $job->company->applicationGroups->where('is_deleteable', false)->first()->id ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // make notification to candidate and company for notify
        $job->company->user->notify(new ApplyJobNotification(auth('user')->user(), $job->company->user, $job));

        if (auth('user')->user()->recent_activities_alert) {
            auth('user')
                ->user()
                ->notify(new ApplyJobNotification(auth('user')->user(), $job->company->user, $job));
        }

        flashSuccess(__('job_applied_successfully'));

        return back();
    }

    public function register($role)
    {
        return view('frontend.auth.register', compact('role'));
    }

    /**
     * Get all posts
     *
     * @return void
     */
    public function posts(Request $request)
    {
        $code = currentLangCode();
        $key = request()->search;
        $posts = Post::query()
            ->where('locale', $code)
            ->published()
            ->withCount('comments');

        if ($key) {
            $posts->whereLike('title', $key);
        }

        if ($request->category) {
            $category_ids = PostCategory::whereIn('slug', $request->category)
                ->get()
                ->pluck('id');
            $posts = $posts
                ->whereIn('category_id', $category_ids)
                ->latest()
                ->paginate(10)
                ->withQueryString();
        } else {
            $posts = $posts
                ->latest()
                ->paginate(10)
                ->withQueryString();
        }

        $recent_posts = Post::where('locale', $code)
            ->published()
            ->withCount('comments')
            ->latest()
            ->take(5)
            ->get();
        $categories = PostCategory::latest()->get();

        return view('frontend.pages.posts', compact('posts', 'categories', 'recent_posts'));
    }

    /**
     * Post details
     *
     * @param  string  $slug
     * @return void
     */
    public function post($slug)
    {
        $code = currentLangCode();
        $data['post'] = Post::published()
            ->whereSlug($slug)
            ->where('locale', $code)
            ->with(['author:id,name,name', 'comments.replies.user:id,name,image'])
            ->first();

        if (! $data['post']) {
            $current_language = getLanguageByCode($code);
            $post_language = getLanguageByCode(Post::whereSlug($slug)->value('locale'));
            $data['error_message'] = "This post is not available in {$current_language}, change the language to {$post_language} to see this post";

            flashError($data['error_message']);
            abort(404);
        }

        return view('frontend.pages.post', $data);
    }

    /**
     * Post comment
     *
     * @return void
     */
    public function comment(Post $post, Request $request)
    {
        if (! auth()->check()) {
            flashError(__('if_you_perform_this_action_you_need_to_login_your_account_first_do_you_want_to_login_now'));

            return redirect()->route('login');
        }

        $request->validate([
            'body' => 'required|max:2500|min:2',
        ]);

        $comment = new PostComment();
        $comment->author_id = auth()->user()->id;
        $comment->post_id = $post->id;
        if ($request->has('parent_id')) {
            $comment->parent_id = $request->parent_id;
            $redirect = '#replies-'.$request->parent_id;
        } else {
            $redirect = '#comments';
        }
        $comment->body = $request->body;
        $comment->save();

        return redirect(url()->previous().$redirect);
    }

    /**
     * Mark all notification as read
     *
     * @return void
     */
    public function markReadSingleNotification(Request $request)
    {
        $has_unread_notification = auth()
            ->user()
            ->unreadNotifications->count();

        if ($has_unread_notification && $request->id) {
            auth()
                ->user()
                ->unreadNotifications->where('id', $request->id)
                ->markAsRead();
        }

        return true;
    }

    /**
     * Set session
     *
     * @return void
     */
    public function setSession(Request $request)
    {
        info($request->all());
        $request->session()->put('location', $request->input());

        return response()->json(true);
    }

    /**
     * Set current location
     *
     * @param  Request  $request
     * @return void
     */
    public function setCurrentLocation($request)
    {
        // Current Visitor Location Track && Set Country IF App Is Multi Country Base
        $app_country = setting('app_country_type');

        if ($app_country == 'multiple_base') {
            $ip = request()->ip();
            // $ip = '103.102.27.0'; // Bangladesh
            // $ip = '105.179.161.212'; // Mauritius
            // $ip = '110.33.122.75'; // AUD
            // $ip = '5.132.255.255'; // SA
            // $ip = '107.29.65.61'; // United States"
            // $ip = '46.39.160.0'; // Czech Republic
            // $ip = "94.112.58.11"; // Czechia

            if ($ip) {
                $current_user_data = Location::get($ip);
                if ($current_user_data) {
                    $user_country = $current_user_data->countryName;
                    if ($user_country) {
                        $this->setLangAndCurrency($user_country);
                        $database_country = Country::where('name', $user_country)
                            ->where('status', 1)
                            ->first();
                        if ($database_country) {
                            $selected_country = session()->get('selected_country');
                            if (! $selected_country) {
                                session()->put('selected_country', $database_country->id);

                                return true;
                            }
                        }
                    }
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Process for set currency & language
     *
     * @param  string  $name
     * @return bool
     */
    public function setLangAndCurrency($name)
    {
        // this process for get language code/sort name  and currency sortname
        $get_lang_wise_sort_name = json_decode(file_get_contents(base_path('resources/backend/dummy-data/country_currency_language.json')), true);

        $country_name = Str::slug($name);
        if ($get_lang_wise_sort_name) {
            // loop json file data

            for ($i = 0; $i < count($get_lang_wise_sort_name); $i++) {
                $json_country_name = Str::slug($get_lang_wise_sort_name[$i]['name']);

                if ($country_name == $json_country_name) {
                    // check country are same

                    $cn_code = $get_lang_wise_sort_name[$i]['currency']['code'];
                    $ln_code = $get_lang_wise_sort_name[$i]['language']['code'];

                    // Currency setup
                    $set_currency = CurrencyModel::where('code', Str::upper($cn_code))->first();
                    if ($set_currency) {
                        session(['current_currency' => $set_currency]);
                        currencyRateStore();
                    }
                    // // Currency setup
                    $set_language = Language::where('code', Str::lower($ln_code))->first();
                    if ($set_language) {
                        session(['current_lang' => $set_language]);
                        // session()->put('set_lang', $lang);
                        app()->setLocale($ln_code);
                    }

                    // menu list cache clear
                    Cache::forget('menu_lists');

                    return true;
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Set selected country
     *
     * @return void
     */
    public function setSelectedCountry(Request $request)
    {
        session()->put('selected_country', $request->country);

        return back();
    }

    /**
     * Remove selected country
     *
     * @return void
     */
    public function removeSelectedCountry()
    {
        session()->forget('selected_country');

        return redirect()->back();
    }

    /**
     * Job autocomplete
     *
     * @return array
     */
    public function jobAutocomplete(Request $request)
    {
        $jobs = Job::select('title as value', 'id')
            ->where('title', 'LIKE', '%'.$request->get('search').'%')
            ->active()
            ->withoutEdited()
            ->latest()
            ->get()
            ->take(15);

        if ($jobs && count($jobs)) {
            $data = '<ul class="dropdown-menu show">';
            foreach ($jobs as $job) {
                $data .= '<li class="dropdown-item"><a href="'.route('website.job', ['keyword' => $job->value]).'">'.$job->value.'</a></li>';
            }
            $data .= '</ul>';
        } else {
            $data = '<ul class="dropdown-menu show"><li class="dropdown-item">No data found</li></ul>';
        }

        return response()->json($data);
    }

    /**
     * Careerjet jobs list
     *
     * @return Renderable
     */
    public function careerjetJobs(Request $request)
    {
        if (! config('zakirsoft.careerjet_id')) {
            abort(404);
        }

        $careerjet_jobs = $this->getCareerjetJobs($request, 25);

        return view('frontend.pages.jobs.careerjet-jobs', compact('careerjet_jobs'));
    }

    /**
     * Indeed jobs list
     *
     * @return Renderable
     */
    public function indeedJobs(Request $request)
    {
        if (! config('zakirsoft.indeed_id')) {
            abort(404);
        }

        $indeed_jobs = $this->getIndeedJobs($request, 25);

        return view('frontend.pages.jobs.indeed-jobs', compact('indeed_jobs'));
    }

    /**
     * Ckeditor image upload
     *
     * @return array
     */
    public function ckeditorImageUpload(Request $request)
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName.'_'.time().'.'.$extension;
            $request->file('upload')->move(public_path('uploads/ckeditor'), $fileName);

            $url = asset('uploads/ckeditor/'.$fileName);

            return response()->json([
                'uploaded' => true,
                'url' => $url,
            ]);
        }
    }
}
