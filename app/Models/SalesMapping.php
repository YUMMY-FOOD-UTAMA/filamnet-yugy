<?php

namespace App\Models;

use App\Models\Customer\Customer;
use App\Trait\AuditTrait;
use App\Trait\SalesMappingTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesMapping extends Model
{
    use HasFactory, AuditTrait, SoftDeletes;

    protected $table = 'sales_mappings';

    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withTrashed();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withTrashed();
    }

    public function expiredAtTheDay()
    {
        $start = Carbon::parse($this->start_visit)->setTimezone('Asia/Jakarta')->startOfDay();
        $end = Carbon::parse($this->end_visit)->setTimezone('Asia/Jakarta')->startOfDay();

        return $start->diffInDays($end) . ' Days';
    }

    public function rangeDate()
    {
        return Carbon::parse($this->start_visit)
                ->setTimezone('Asia/Jakarta')
                ->format('Y-m-d')
            . ' - ' .
            Carbon::parse($this->end_visit)
                ->setTimezone('Asia/Jakarta')
                ->format('Y-m-d');
    }
}
