<?php

namespace App\Scopes;

use App\Enums\TransactionsInternalStatusesEnum;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Http\Request;

class TransactionsRegistryAdminScope extends BaseTransactionsRegistryScope implements Scope
{
    /**
     * Manager.
     *
     * @var string
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->manager = $request->get('manager');
    }

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder
            ->when($this->dateFrom, function (Builder $query) {
                $query->where('date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function (Builder $query) {
                $query->where('date', '<=', $this->dateTo->endOfDay());
            })
            ->when($this->amountFrom, function (Builder $query) {
                $query->where('amount', '>=', $this->amountFrom);
            })
            ->when($this->amountTo, function (Builder $query) {
                $query->where('amount', '<=', $this->amountTo);
            })
            ->when($this->search, function (Builder $query) {
                $query
                    ->where('card_number', $this->search)
                    ->orWhere('external_id', $this->search);
            })
            ->when($this->column, function (Builder $query) {
                $query->orderBy($this->column, $this->direction);
            })
            ->when($this->manager, function (Builder $query) {
                $query->where('manager_name', $this->manager);
            });

        if ($this->status == TransactionsInternalStatusesEnum::STATUS_SEND_SUPPLIER) {
            $builder->whereHas('processing', function (Builder $processingQuery) {
                $processingQuery->where('is_processing', true);
            });
        } else {
            $builder->when($this->status, function (Builder $query) {
                $query->where('status', $this->status);
            });
        }

        if (request()->has('manager') && request()->get('manager') == '') {
            $builder->where(['manager_name' => '']);
        }
    }

}
