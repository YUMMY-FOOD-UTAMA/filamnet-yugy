<?php

namespace App\Models\Customer;

use App\Models\Region\Area;
use App\Models\Region\Region;
use App\Models\Region\SubRegion;
use App\Models\SalesMapping;
use App\Trait\AuditTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory, AuditTrait, SoftDeletes;

    protected $table = 'customers';

    protected $guarded = ['id'];

    public static function generateCustomerCode($regionID, $subRegionID, $areaID, $segmentID, $categoryID)
    {
        $region = Region::find($regionID);
        $subRegion = SubRegion::find($subRegionID);
        $area = Area::find($areaID);
        $segment = CustomerSegment::find($segmentID);
        $category = CustomerCategory::find($categoryID);

        if (!$region || !$subRegion || !$area || !$segment || !$category) {
            throw new \Exception('One or more data are invalid.');
        }

        $code = strtoupper(
            $region->code . $subRegion->code . $area->code . $segment->code . $category->code
        );

        $lastCustomer = self::latest('id')->first();

        $lastID = $lastCustomer ? $lastCustomer->id : 0;

        $newID = $lastID + 1;

        return $code . str_pad($newID, 4, '0', STR_PAD_LEFT);
    }

    public function customerCategory()
    {
        return $this->belongsTo(CustomerCategory::class)->withTrashed();
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class)->withTrashed();
    }

    public function customerSegment()
    {
        return $this->belongsTo(CustomerSegment::class)->withTrashed();
    }

    public function area()
    {
        return $this->belongsTo(Area::class)->withTrashed();
    }

    public static function availableForBooked($customerIDs, $startVisit, $endVisit): bool
    {
        return SalesMapping::whereIn('customer_id', $customerIDs)
            ->whereNotIn('status', ['Done', 'Cancel', 'Expired'])
            ->where(function ($query) use ($startVisit, $endVisit) {
                $query->where(function ($q) use ($startVisit, $endVisit) {
                    $q->where('start_visit', '<=', $endVisit)
                        ->where('end_visit', '>=', $startVisit);
                });
            })
            ->lockForUpdate()
            ->exists();
    }

    public function bookedBys()
    {
        return $this->hasMany(SalesMapping::class, 'customer_id','id')
            ->whereNotIn('status', ['Done', 'Cancel', 'Expired']);
    }

    public function statusCustomerSales(): string
    {
        return $this->is_booked_by_sales ? 'booked' : 'available';
    }
}
