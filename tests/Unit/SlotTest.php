<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Slot;
use Tests\TestDatabase;

/**
 * Slot Model Tests
 */
class SlotTest extends TestCase
{
    private $slotModel;
    private $testDb;

    protected function setUp(): void
    {
        $this->testDb = TestDatabase::getInstance();
        $this->testDb->setUp();
        $this->slotModel = new Slot();
    }

    protected function tearDown(): void
    {
        $this->testDb->tearDown();
    }

    /**
     * Test slot creation
     */
    public function testCreateSlot()
    {
        $slotData = [
            'date' => date('Y-m-d', strtotime('+1 week')),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'status' => 'available',
            'price' => 125.50,
            'description' => 'Test Slot'
        ];

        $slotId = $this->slotModel->create($slotData);
        
        $this->assertIsInt($slotId);
        $this->assertGreaterThan(0, $slotId);
    }

    /**
     * Test finding slot by ID
     */
    public function testFindById()
    {
        $slot = $this->slotModel->findById(1);
        
        $this->assertIsArray($slot);
        $this->assertEquals('available', $slot['status']);
        $this->assertEquals(100.00, $slot['price']);
    }

    /**
     * Test finding slots by date range
     */
    public function testFindByDateRange()
    {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+1 week'));
        
        $slots = $this->slotModel->findByDateRange($startDate, $endDate);
        
        $this->assertIsArray($slots);
        $this->assertGreaterThan(0, count($slots));
        
        foreach ($slots as $slot) {
            $this->assertGreaterThanOrEqual($startDate, $slot['date']);
            $this->assertLessThanOrEqual($endDate, $slot['date']);
        }
    }

    /**
     * Test finding slots by status
     */
    public function testFindByStatus()
    {
        $availableSlots = $this->slotModel->findByStatus('available');
        $bookedSlots = $this->slotModel->findByStatus('booked');
        
        $this->assertIsArray($availableSlots);
        $this->assertIsArray($bookedSlots);
        
        foreach ($availableSlots as $slot) {
            $this->assertEquals('available', $slot['status']);
        }
        
        foreach ($bookedSlots as $slot) {
            $this->assertEquals('booked', $slot['status']);
        }
    }

    /**
     * Test finding slots by date
     */
    public function testFindByDate()
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $slots = $this->slotModel->findByDate($tomorrow);
        
        $this->assertIsArray($slots);
        
        foreach ($slots as $slot) {
            $this->assertEquals($tomorrow, $slot['date']);
        }
    }

    /**
     * Test updating slot
     */
    public function testUpdateSlot()
    {
        $updateData = [
            'price' => 150.00,
            'description' => 'Updated Description'
        ];

        $result = $this->slotModel->update(1, $updateData);
        
        $this->assertTrue($result);
        
        $updatedSlot = $this->slotModel->findById(1);
        $this->assertEquals(150.00, $updatedSlot['price']);
        $this->assertEquals('Updated Description', $updatedSlot['description']);
    }

    /**
     * Test marking slot as booked
     */
    public function testMarkAsBooked()
    {
        $result = $this->slotModel->markAsBooked(1);
        
        $this->assertTrue($result);
        
        $slot = $this->slotModel->findById(1);
        $this->assertEquals('booked', $slot['status']);
    }

    /**
     * Test marking slot as available
     */
    public function testMarkAsAvailable()
    {
        $result = $this->slotModel->markAsAvailable(2);
        
        $this->assertTrue($result);
        
        $slot = $this->slotModel->findById(2);
        $this->assertEquals('available', $slot['status']);
    }

    /**
     * Test cancelling slot
     */
    public function testCancelSlot()
    {
        $result = $this->slotModel->cancelSlot(1);
        
        $this->assertTrue($result);
        
        $slot = $this->slotModel->findById(1);
        $this->assertEquals('cancelled', $slot['status']);
    }

    /**
     * Test checking if slot is available
     */
    public function testIsAvailable()
    {
        $this->assertTrue($this->slotModel->isAvailable(1));
        $this->assertFalse($this->slotModel->isAvailable(3)); // This slot is booked
    }

    /**
     * Test getting slot statistics
     */
    public function testGetStats()
    {
        $stats = $this->slotModel->getStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('available', $stats);
        $this->assertArrayHasKey('booked', $stats);
        $this->assertArrayHasKey('cancelled', $stats);
        $this->assertArrayHasKey('maintenance', $stats);
        
        $this->assertGreaterThan(0, $stats['total']);
    }

    /**
     * Test getting slots by price range
     */
    public function testFindByPriceRange()
    {
        $slots = $this->slotModel->findByPriceRange(50, 100);
        
        $this->assertIsArray($slots);
        
        foreach ($slots as $slot) {
            $this->assertGreaterThanOrEqual(50, $slot['price']);
            $this->assertLessThanOrEqual(100, $slot['price']);
        }
    }

    /**
     * Test getting upcoming slots
     */
    public function testGetUpcomingSlots()
    {
        $slots = $this->slotModel->getUpcomingSlots(7);
        
        $this->assertIsArray($slots);
        
        foreach ($slots as $slot) {
            $this->assertGreaterThanOrEqual(date('Y-m-d'), $slot['date']);
        }
    }

    /**
     * Test getting slots by time range
     */
    public function testFindByTimeRange()
    {
        $slots = $this->slotModel->findByTimeRange('06:00:00', '12:00:00');
        
        $this->assertIsArray($slots);
        
        foreach ($slots as $slot) {
            $this->assertGreaterThanOrEqual('06:00:00', $slot['start_time']);
            $this->assertLessThanOrEqual('12:00:00', $slot['end_time']);
        }
    }

    /**
     * Test deleting slot
     */
    public function testDeleteSlot()
    {
        $result = $this->slotModel->delete(1);
        
        $this->assertTrue($result);
        
        $slot = $this->slotModel->findById(1);
        $this->assertFalse($slot);
    }

    /**
     * Test getting all slots
     */
    public function testGetAllSlots()
    {
        $slots = $this->slotModel->getAll();
        
        $this->assertIsArray($slots);
        $this->assertGreaterThan(0, count($slots));
        
        foreach ($slots as $slot) {
            $this->assertArrayHasKey('id', $slot);
            $this->assertArrayHasKey('date', $slot);
            $this->assertArrayHasKey('start_time', $slot);
            $this->assertArrayHasKey('end_time', $slot);
            $this->assertArrayHasKey('status', $slot);
            $this->assertArrayHasKey('price', $slot);
        }
    }

    /**
     * Test getting slots with bookings count
     */
    public function testGetSlotsWithBookingsCount()
    {
        $slots = $this->slotModel->getSlotsWithBookingsCount();
        
        $this->assertIsArray($slots);
        
        foreach ($slots as $slot) {
            $this->assertArrayHasKey('bookings_count', $slot);
            $this->assertIsInt($slot['bookings_count']);
        }
    }

    /**
     * Test checking for overlapping slots
     */
    public function testHasOverlappingSlots()
    {
        $date = date('Y-m-d', strtotime('+1 day'));
        $startTime = '07:00:00';
        $endTime = '10:00:00';
        
        $hasOverlap = $this->slotModel->hasOverlappingSlots($date, $startTime, $endTime, 1);
        
        $this->assertIsBool($hasOverlap);
    }

    /**
     * Test getting slot revenue
     */
    public function testGetSlotRevenue()
    {
        $revenue = $this->slotModel->getSlotRevenue();
        
        $this->assertIsFloat($revenue);
        $this->assertGreaterThan(0, $revenue);
    }
}

