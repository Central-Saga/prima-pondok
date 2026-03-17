<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan';

    public const STATUS_WAITING = 'waiting';
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_EXTEND = 'extend';

    // Checkout deadline status for admin view
    public const CHECKOUT_STATUS_NORMAL = 'normal';
    public const CHECKOUT_STATUS_WARNING = 'diperingati';
    public const CHECKOUT_STATUS_OVERDUE = 'jatuh_tempo';
    public const CHECKOUT_STATUS_EXTEND = 'extend';

    public const CANCEL_CATEGORY_PAYMENT = 'bukti_pembayaran';
    public const CANCEL_CATEGORY_GENERAL = 'general';

    private const CANCEL_PREFIX = '[bukti_pembayaran]';

    // Checkout time is 11:00 AM, checkin is 12:00 PM
    public const CHECKOUT_HOUR = 11;
    public const CHECKIN_HOUR = 12;
    public const DENDA_AMOUNT = 100000;

    protected $fillable = [
        'wisatawan_id',
        'kamar_id',
        'tanggal_checkin',
        'tanggal_checkout',
        'jumlah_hari',
        'total_bayar',
        'status',
        'catatan_cancel',
        'is_extend',
        'extend_from_id',
    ];

    protected $casts = [
        'tanggal_checkin' => 'date',
        'tanggal_checkout' => 'date',
        'total_bayar' => 'decimal:2',
    ];

    public function wisatawan()
    {
        return $this->belongsTo(Wisatawan::class);
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Encode kategori + catatan ke satu kolom catatan_cancel.
     */
    public static function encodeCancelNote(string $category, string $note): string
    {
        if ($category === self::CANCEL_CATEGORY_PAYMENT) {
            return self::CANCEL_PREFIX . $note;
        }
        return $note;
    }

    /**
     * Ambil kategori pembatalan dari catatan_cancel.
     */
    public function getCancelCategoryAttribute(): string
    {
        if ($this->catatan_cancel && str_starts_with($this->catatan_cancel, self::CANCEL_PREFIX)) {
            return self::CANCEL_CATEGORY_PAYMENT;
        }
        return self::CANCEL_CATEGORY_GENERAL;
    }

    /**
     * Ambil catatan pembatalan bersih (tanpa prefix).
     */
    public function getCancelNoteAttribute(): ?string
    {
        if (! $this->catatan_cancel) {
            return null;
        }
        $note = $this->catatan_cancel;
        if (str_starts_with($note, self::CANCEL_PREFIX)) {
            $note = substr($note, strlen(self::CANCEL_PREFIX));
        }
        return $note;
    }



    /**
     * Apakah pembatalan karena bukti pembayaran?
     */
    public function isCancelledForPayment(): bool
    {
        return $this->status === self::STATUS_CANCELLED
            && $this->cancel_category === self::CANCEL_CATEGORY_PAYMENT;
    }

    /**
     * Get checkout deadline datetime (checkout date at 11:00 AM).
     */
    public function getCheckoutDeadline(): \Carbon\Carbon
    {
        return $this->tanggal_checkout->copy()->setTime(self::CHECKOUT_HOUR, 0, 0);
    }

    /**
     * Get warning time (1 hour before checkout deadline = 10:00 AM on checkout day).
     */
    public function getCheckoutWarningTime(): \Carbon\Carbon
    {
        return $this->getCheckoutDeadline()->subHour();
    }

    /**
     * Check if this booking is in warning phase (within 1 hour before checkout deadline).
     */
    public function isCheckoutWarning(): bool
    {
        if ($this->status !== self::STATUS_CONFIRMED) return false;

        $now = now();
        $warningTime = $this->getCheckoutWarningTime();
        $deadline = $this->getCheckoutDeadline();

        return $now->gte($warningTime) && $now->lt($deadline);
    }

    /**
     * Check if this booking is overdue (past checkout deadline and not completed).
     */
    public function isCheckoutOverdue(): bool
    {
        if (!in_array($this->status, [self::STATUS_CONFIRMED, self::STATUS_EXTEND])) return false;

        return now()->gte($this->getCheckoutDeadline());
    }

    /**
     * Get checkout status for admin display.
     */
    public function getCheckoutStatusAttribute(): string
    {
        if ($this->status === self::STATUS_EXTEND) {
            return self::CHECKOUT_STATUS_EXTEND;
        }

        if ($this->status !== self::STATUS_CONFIRMED) {
            return self::CHECKOUT_STATUS_NORMAL;
        }

        if ($this->isCheckoutOverdue()) {
            return self::CHECKOUT_STATUS_OVERDUE;
        }

        if ($this->isCheckoutWarning()) {
            return self::CHECKOUT_STATUS_WARNING;
        }

        return self::CHECKOUT_STATUS_NORMAL;
    }

    /**
     * Check if user needs to see checkout alert (confirmed booking, today is checkout day or past it).
     */
    public function needsCheckoutAlert(): bool
    {
        if ($this->status !== self::STATUS_CONFIRMED) return false;

        $now = now();
        $warningTime = $this->getCheckoutWarningTime();

        return $now->gte($warningTime);
    }

    /**
     * Check if denda (fine) applies.
     */
    public function hasDenda(): bool
    {
        if (!in_array($this->status, [self::STATUS_CONFIRMED])) return false;

        return now()->gte($this->getCheckoutDeadline());
    }

    /**
     * Relation to parent booking (if this is an extension).
     */
    public function extendFrom()
    {
        return $this->belongsTo(self::class, 'extend_from_id');
    }

    /**
     * Relation to extension bookings.
     */
    public function extensions()
    {
        return $this->hasMany(self::class, 'extend_from_id');
    }

    /**
     * Check if room is available for extension from checkout date.
     */
    public function canExtend(int $days = 1): bool
    {
        $extendStart = $this->tanggal_checkout->copy();
        $extendEnd = $extendStart->copy()->addDays($days);

        // Check if any other booking overlaps with or starts on the extend end date
        $overlap = self::where('kamar_id', $this->kamar_id)
            ->where('id', '!=', $this->id)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_CONFIRMED, self::STATUS_EXTEND])
            ->where(function ($q) use ($extendStart, $extendEnd) {
                $q->where('tanggal_checkin', '<=', $extendEnd)
                  ->where('tanggal_checkout', '>', $extendStart);
            })
            ->exists();

        return !$overlap;
    }
}
