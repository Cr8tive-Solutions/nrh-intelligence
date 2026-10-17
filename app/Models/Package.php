<?php

namespace App\Models;

use Database\Factories\PackageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Package extends Model
{
    /** @use HasFactory<PackageFactory> */
    use HasFactory;

    use LogsActivity;

    protected $fillable = ['customer_id', 'country_id', 'name', 'price'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'country_id', 'price'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('package')
            ->setDescriptionForEvent(fn (string $event) => "Package {$event}");
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Pivot rows are written only by the admin portal — this app reads them.
     * Ordered to match `RequestCandidate::scopeTypes()` so a package's scopes
     * appear in the same sequence everywhere in the portal.
     */
    public function scopeTypes(): BelongsToMany
    {
        return $this->belongsToMany(ScopeType::class, 'package_scope_type')
            ->orderBy('scope_types.sort_order')
            ->orderBy('scope_types.id');
    }

    /**
     * Sum of this package's scopes at this customer's effective prices, or null
     * when any scope is price-on-request with no agreed override — a request
     * containing such a scope is rejected by CreateRequestController, so the
     * package has no quotable total.
     *
     * Kept identical to the admin portal's `Package::effectiveTotal()` so the
     * figure a customer sees matches the one NRH sees.
     *
     * Pass `$overrides` (customer_scope_prices keyed by scope_type_id, fetched
     * once by the caller) to avoid one query per package in a listing.
     *
     * @param  Collection<int, mixed>|null  $overrides
     */
    public function effectiveTotal(?Collection $overrides = null): ?float
    {
        $overrides ??= CustomerScopePrice::where('customer_id', $this->customer_id)
            ->pluck('price', 'scope_type_id');

        $total = 0.0;

        foreach ($this->scopeTypes as $scope) {
            if ($overrides->has($scope->id)) {
                $total += (float) $overrides->get($scope->id);

                continue;
            }

            if ($scope->price_on_request) {
                return null;
            }

            $total += (float) $scope->price;
        }

        return $total;
    }
}
