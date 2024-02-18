<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use App\Nova\Actions\PostWithdrawrequestToEvmack;
use App\Nova\Actions\disbursingEmailToVendor;
use App\Nova\Metrics\Deposites;
use App\Nova\Metrics\NonCompleteDeposite;
use App\Nova\Metrics\NonCompletedWithdraw;
use App\Nova\Metrics\Withdraw;

class paymentrequest extends Resource
{

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\paymentRequest>
     */
    public static $model = \App\Models\paymentRequest::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),
            Text::make('Initiator Email', 'initiator_email')->sortable(),
            Text::make('Request reference', 'request_reference')->sortable(),
            Text::make('Mobile', 'client_mobile')->sortable(),
            Text::make('amount', 'amount')->sortable(),
            Text::make('trax type','trx_type'),
            Text::make('Posted reference', 'posted_reference')->sortable(),
            Text::make('Status', 'status')->sortable(),
            Text::make('user ID', 'user_id')->sortable(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }

    /**
     * 
     * update resource name
     */
    public static function label() {
        return 'Transactions';
    }
}
