<?php

namespace App\Observers;

use App\Models\Business;

class BusinessObserver
{
    /**
     * Handle the business "created" event.
     *
     * @param \App\Business $business
     * @return void
     */
    public function created(Business $business)
    {
        //

//        dd($business);

    }

    /**
     * Handle the business "updated" event.
     *
     * @param \App\Business $business
     * @return void
     */
    public function updated(Business $business)
    {
        //
    }

    /**
     * Handle the business "deleted" event.
     *
     * @param \App\Business $business
     * @return void
     */
    public function deleted(Business $business)
    {
        //
    }

    /**
     * Handle the business "restored" event.
     *
     * @param \App\Business $business
     * @return void
     */
    public function restored(Business $business)
    {
        //
    }

    /**
     * Handle the business "force deleted" event.
     *
     * @param \App\Business $business
     * @return void
     */
    public function forceDeleted(Business $business)
    {
        //
    }
}
