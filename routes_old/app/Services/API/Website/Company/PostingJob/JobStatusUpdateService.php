<?php

namespace App\Services\API\Website\Company\PostingJob;

use App\Models\Job;
use F9Web\ApiResponseHelpers;

class JobStatusUpdateService
{
    use ApiResponseHelpers;

    public function execute($request)
    {
        $job = Job::whereSlug($request->slug)->first();

        if (! $job) {
            return $this->respondNotFound(__('job_not_found'));
        }

        if ($job->status == 'active' || $job->status == 'expire') {
            return $this->respondForbidden(__('invalid_job_status'));
        }

        $job->update(['status' => $request->status]);

        return $this->respondOk(__('job_status_updated_successfully'));
    }

    public function changeFeatured($request)
    {
        $job = Job::whereSlug($request->slug)->first();

        if (! $job) {
            return $this->respondNotFound(__('job_not_found'));
        }

        if ($request->featured) {
            $job->update(['featured' => $request->featured]);
        }
        if ($request->highlight) {
            $job->update(['highlight' => $request->highlight]);
        }

        return $this->respondOk(__('job_status_updated_successfully'));
    }
}
