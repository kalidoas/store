<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'quantity',
        'purchase_price',
        'transport_fees',
        'other_fees',
        'selling_price',
        'notes',
        'image',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'transport_fees' => 'decimal:2',
        'other_fees' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    public static function categories(): array
    {
        return [
            'Téléphones',
            'TV',
            'Audio',
            'Informatique',
            'Électroménager',
            'Accessoires',
            'Autre',
        ];
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    protected function feesPerUnit(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                if ($this->quantity <= 0) {
                    return 0.0;
                }

                return (float) (($this->transport_fees + $this->other_fees) / $this->quantity);
            }
        );
    }

    protected function costPrice(): Attribute
    {
        return Attribute::make(
            get: fn (): float => (float) ($this->purchase_price + $this->fees_per_unit)
        );
    }

    protected function grossMargin(): Attribute
    {
        return Attribute::make(
            get: fn (): float => (float) ($this->selling_price - $this->cost_price)
        );
    }

    protected function marginPercentage(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                if ((float) $this->selling_price === 0.0) {
                    return 0.0;
                }

                return (float) (($this->gross_margin / $this->selling_price) * 100);
            }
        );
    }

    protected function stockStatus(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if ($this->quantity === 0) {
                    return 'out_of_stock';
                }

                if ($this->quantity <= 3) {
                    return 'low_stock';
                }

                return 'available';
            }
        );
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : 'https://placehold.co/48x48/f3f4f6/9ca3af?text=?';
    }
}

