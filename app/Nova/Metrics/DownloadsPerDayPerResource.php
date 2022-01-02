<?php

namespace App\Nova\Metrics;

use App\Builders\AppBuilder as Builder;
use App\Models\Download;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;

class DownloadsPerDayPerResource extends Trend
{
    public function name()
    {
        return 'Downloads per day';
    }

    private function getData($resource)
    {
        $type = get_class($resource->resource);

        return Download::whereHasMorph('trigger', $type, function ($query) use ($resource) {
            $query->where('id', $resource->id);
        });
    }

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->countByDays($request, $this->getData($request->findResourceOrFail()))->showLatestValue();
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            7 => '7 days',
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'downloads-per-day-per-resource';
    }
}
