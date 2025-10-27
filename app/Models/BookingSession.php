<?php
namespace App\Models;
use App\Database\Database;

class BookingSession extends BaseModel {
    protected $table = 'booking_sessions';
    protected $fillable = [
        'booking_id', 'session_date', 'start_time', 'end_time', 'status', 'created_at', 'updated_at',
    ];
    public function findByBooking($bookingId) {
        return $this->where('booking_id', $bookingId);
    }
}
