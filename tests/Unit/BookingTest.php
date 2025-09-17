<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Booking;
use Tests\TestDatabase;

/**
 * Booking Model Tests
 */
class BookingTest extends TestCase
{
    private $bookingModel;
    private $testDb;

    protected function setUp(): void
    {
        $this->testDb = TestDatabase::getInstance();
        $this->testDb->setUp();
        $this->bookingModel = new Booking();
    }

    protected function tearDown(): void
    {
        $this->testDb->tearDown();
    }

    /**
     * Test booking creation
     */
    public function testCreateBooking()
    {
        $bookingData = [
            'slot_id' => 1,
            'advertiser_id' => 3,
            'advertisement_message' => 'Test advertisement message',
            'status' => 'pending',
            'total_amount' => 100.00
        ];

        $bookingId = $this->bookingModel->createBooking($bookingData);
        
        $this->assertIsInt($bookingId);
        $this->assertGreaterThan(0, $bookingId);
    }

    /**
     * Test finding booking by ID
     */
    public function testFindById()
    {
        $booking = $this->bookingModel->findById(1);
        
        $this->assertIsArray($booking);
        $this->assertEquals('pending', $booking['status']);
        $this->assertEquals(100.00, $booking['total_amount']);
    }

    /**
     * Test finding bookings by advertiser
     */
    public function testFindByAdvertiser()
    {
        $bookings = $this->bookingModel->findByAdvertiser(3);
        
        $this->assertIsArray($bookings);
        $this->assertGreaterThan(0, count($bookings));
        
        foreach ($bookings as $booking) {
            $this->assertEquals(3, $booking['advertiser_id']);
        }
    }

    /**
     * Test finding bookings by status
     */
    public function testFindByStatus()
    {
        $pendingBookings = $this->bookingModel->findByStatus('pending');
        $approvedBookings = $this->bookingModel->findByStatus('approved');
        
        $this->assertIsArray($pendingBookings);
        $this->assertIsArray($approvedBookings);
        
        foreach ($pendingBookings as $booking) {
            $this->assertEquals('pending', $booking['status']);
        }
        
        foreach ($approvedBookings as $booking) {
            $this->assertEquals('approved', $booking['status']);
        }
    }

    /**
     * Test finding bookings by date range
     */
    public function testFindByDateRange()
    {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 week'));
        
        $bookings = $this->bookingModel->findByDateRange($startDate, $endDate);
        
        $this->assertIsArray($bookings);
        $this->assertGreaterThan(0, count($bookings));
    }

    /**
     * Test updating booking
     */
    public function testUpdateBooking()
    {
        $updateData = [
            'status' => 'approved',
            'total_amount' => 120.00
        ];

        $result = $this->bookingModel->updateBooking(1, $updateData);
        
        $this->assertTrue($result);
        
        $updatedBooking = $this->bookingModel->findById(1);
        $this->assertEquals('approved', $updatedBooking['status']);
        $this->assertEquals(120.00, $updatedBooking['total_amount']);
    }

    /**
     * Test approving booking
     */
    public function testApproveBooking()
    {
        $result = $this->bookingModel->approveBooking(1);
        
        $this->assertTrue($result);
        
        $booking = $this->bookingModel->findById(1);
        $this->assertEquals('approved', $booking['status']);
    }

    /**
     * Test rejecting booking
     */
    public function testRejectBooking()
    {
        $result = $this->bookingModel->rejectBooking(1);
        
        $this->assertTrue($result);
        
        $booking = $this->bookingModel->findById(1);
        $this->assertEquals('rejected', $booking['status']);
    }

    /**
     * Test cancelling booking
     */
    public function testCancelBooking()
    {
        $result = $this->bookingModel->cancelBooking(1);
        
        $this->assertTrue($result);
        
        $booking = $this->bookingModel->findById(1);
        $this->assertEquals('cancelled', $booking['status']);
    }

    /**
     * Test checking if slot is booked
     */
    public function testIsSlotBooked()
    {
        $this->assertTrue($this->bookingModel->isSlotBooked(3)); // This slot is booked
        $this->assertFalse($this->bookingModel->isSlotBooked(1)); // This slot is available
    }

    /**
     * Test getting booking statistics
     */
    public function testGetStats()
    {
        $stats = $this->bookingModel->getStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('pending', $stats);
        $this->assertArrayHasKey('approved', $stats);
        $this->assertArrayHasKey('rejected', $stats);
        $this->assertArrayHasKey('cancelled', $stats);
        $this->assertArrayHasKey('total_revenue', $stats);
        
        $this->assertGreaterThan(0, $stats['total']);
    }

    /**
     * Test getting bookings by advertiser with details
     */
    public function testGetBookingsByAdvertiserWithDetails()
    {
        $bookings = $this->bookingModel->getBookingsByAdvertiserWithDetails(3);
        
        $this->assertIsArray($bookings);
        
        foreach ($bookings as $booking) {
            $this->assertEquals(3, $booking['advertiser_id']);
            $this->assertArrayHasKey('slot_date', $booking);
            $this->assertArrayHasKey('slot_start_time', $booking);
            $this->assertArrayHasKey('slot_end_time', $booking);
        }
    }

    /**
     * Test getting recent bookings
     */
    public function testGetRecentBookings()
    {
        $bookings = $this->bookingModel->getRecentBookings(5);
        
        $this->assertIsArray($bookings);
        $this->assertLessThanOrEqual(5, count($bookings));
    }

    /**
     * Test getting booking revenue
     */
    public function testGetBookingRevenue()
    {
        $revenue = $this->bookingModel->getBookingRevenue();
        
        $this->assertIsFloat($revenue);
        $this->assertGreaterThan(0, $revenue);
    }

    /**
     * Test getting bookings by month
     */
    public function testGetBookingsByMonth()
    {
        $currentMonth = date('Y-m');
        $bookings = $this->bookingModel->getBookingsByMonth($currentMonth);
        
        $this->assertIsArray($bookings);
        
        foreach ($bookings as $booking) {
            $this->assertStringStartsWith($currentMonth, $booking['created_at']);
        }
    }

    /**
     * Test getting top advertisers
     */
    public function testGetTopAdvertisers()
    {
        $advertisers = $this->bookingModel->getTopAdvertisers(5);
        
        $this->assertIsArray($advertisers);
        $this->assertLessThanOrEqual(5, count($advertisers));
        
        foreach ($advertisers as $advertiser) {
            $this->assertArrayHasKey('advertiser_id', $advertiser);
            $this->assertArrayHasKey('booking_count', $advertiser);
            $this->assertArrayHasKey('total_spent', $advertiser);
        }
    }

    /**
     * Test getting booking status distribution
     */
    public function testGetStatusDistribution()
    {
        $distribution = $this->bookingModel->getStatusDistribution();
        
        $this->assertIsArray($distribution);
        $this->assertArrayHasKey('pending', $distribution);
        $this->assertArrayHasKey('approved', $distribution);
        $this->assertArrayHasKey('rejected', $distribution);
        $this->assertArrayHasKey('cancelled', $distribution);
    }

    /**
     * Test getting all bookings
     */
    public function testGetAllBookings()
    {
        $bookings = $this->bookingModel->getAll();
        
        $this->assertIsArray($bookings);
        $this->assertGreaterThan(0, count($bookings));
        
        foreach ($bookings as $booking) {
            $this->assertArrayHasKey('id', $booking);
            $this->assertArrayHasKey('slot_id', $booking);
            $this->assertArrayHasKey('advertiser_id', $booking);
            $this->assertArrayHasKey('status', $booking);
            $this->assertArrayHasKey('total_amount', $booking);
        }
    }

    /**
     * Test deleting booking
     */
    public function testDeleteBooking()
    {
        $result = $this->bookingModel->delete(1);
        
        $this->assertTrue($result);
        
        $booking = $this->bookingModel->findById(1);
        $this->assertFalse($booking);
    }

    /**
     * Test getting booking with slot details
     */
    public function testGetBookingWithSlotDetails()
    {
        $booking = $this->bookingModel->getBookingWithSlotDetails(1);
        
        $this->assertIsArray($booking);
        $this->assertArrayHasKey('slot_date', $booking);
        $this->assertArrayHasKey('slot_start_time', $booking);
        $this->assertArrayHasKey('slot_end_time', $booking);
        $this->assertArrayHasKey('slot_price', $booking);
    }

    /**
     * Test getting booking with advertiser details
     */
    public function testGetBookingWithAdvertiserDetails()
    {
        $booking = $this->bookingModel->getBookingWithAdvertiserDetails(1);
        
        $this->assertIsArray($booking);
        $this->assertArrayHasKey('advertiser_name', $booking);
        $this->assertArrayHasKey('advertiser_email', $booking);
        $this->assertArrayHasKey('advertiser_phone', $booking);
        $this->assertArrayHasKey('advertiser_company', $booking);
    }
}

