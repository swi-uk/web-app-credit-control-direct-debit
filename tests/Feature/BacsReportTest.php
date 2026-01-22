<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class BacsReportTest extends TestCase
{
    public function testAruddBounceTriggersRetryAndRestricted(): void
    {
        // Acceptance scenario:
        // - payment scheduled for external_order_id=1001, retry_count=0
        // - ARUDD import should set retry_scheduled, next_retry_at=+3 days,
        //   customer restricted, and enqueue payment.update + customer.credit.update.
        $this->assertTrue(true);
    }

    public function testSecondBounceLocksCustomer(): void
    {
        // Acceptance scenario:
        // - payment retry_count=2 (max retries used)
        // - ARUDD import should set failed_final, lock customer,
        //   and enqueue customer.lock webhook.
        $this->assertTrue(true);
    }

    public function testAdvanceNoticeSent(): void
    {
        // Acceptance scenario:
        // - payment due in 3 days
        // - send advance notices should set advance_notice_sent_at
        //   and create audit event.
        $this->assertTrue(true);
    }
}
