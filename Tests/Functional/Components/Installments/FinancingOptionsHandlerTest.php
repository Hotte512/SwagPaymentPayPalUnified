<?php
/**
 * (c) shopware AG <info@shopware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SwagPaymentPayPalUnified\Tests\Functional\Components\Installments;

use SwagPaymentPayPalUnified\Components\Installments\FinancingOptionsHandler;
use SwagPaymentPayPalUnified\PayPalBundle\Structs\Installments\FinancingResponse;

class FinancingOptionsHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function test_can_be_constructed()
    {
        $service = new FinancingOptionsHandler(new FinancingResponse());

        $this->assertNotNull($service);
    }

    public function test_sortOptionsBy_by_term()
    {
        $service = new FinancingOptionsHandler(FinancingResponse::fromArray($this->getFinancingFixture()['financing_options'][0]));

        $sorted = $service->sortOptionsBy(FinancingOptionsHandler::SORT_BY_TERM)->toArray()['qualifyingFinancingOptions'];

        $this->assertEquals(6, $sorted[0]['creditFinancing']['term']);
        $this->assertEquals(12, $sorted[1]['creditFinancing']['term']);
        $this->assertEquals(18, $sorted[2]['creditFinancing']['term']);
        $this->assertEquals(24, $sorted[3]['creditFinancing']['term']);
        $this->assertEquals(24, $sorted[4]['creditFinancing']['term']);
    }

    public function test_sortOptionsBy_by_monthly_payment()
    {
        $service = new FinancingOptionsHandler(FinancingResponse::fromArray($this->getFinancingFixture()['financing_options'][0]));

        $sorted = $service->sortOptionsBy(FinancingOptionsHandler::SORT_BY_MONTHLY_PAYMENT)->toArray()['qualifyingFinancingOptions'];

        $this->assertEquals(29.49, $sorted[0]['monthlyPayment']['value']);
        $this->assertEquals(29.49, $sorted[1]['monthlyPayment']['value']);
        $this->assertEquals(38.42, $sorted[2]['monthlyPayment']['value']);
        $this->assertEquals(56.3, $sorted[3]['monthlyPayment']['value']);
        $this->assertEquals(106.98, $sorted[4]['monthlyPayment']['value']);
    }

    public function test_build_by_term()
    {
        $service = new FinancingOptionsHandler(FinancingResponse::fromArray($this->getFinancingFixture()['financing_options'][0]));
        $data = $service->finalizeList(FinancingOptionsHandler::SORT_BY_TERM);

        $this->assertTrue($data[0]['hasStar']);
        $this->assertNull($data[1]['hasStar']);
        $this->assertNull($data[2]['hasStar']);
        $this->assertTrue($data[3]['hasStar']);
    }

    public function test_build_by_monthly_payment()
    {
        $service = new FinancingOptionsHandler(FinancingResponse::fromArray($this->getFinancingFixture()['financing_options'][0]));
        $data = $service->finalizeList(FinancingOptionsHandler::SORT_BY_MONTHLY_PAYMENT);

        $this->assertTrue($data[0]['hasStar']);
        $this->assertNull($data[1]['hasStar']);
        $this->assertNull($data[2]['hasStar']);
        $this->assertNull($data[3]['hasStar']);
        $this->assertTrue($data[4]['hasStar']);
    }

    public function test_finalizeList_without_data()
    {
        $service = new FinancingOptionsHandler(FinancingResponse::fromArray());
        $data = $service->finalizeList();

        $this->assertCount(0, $data);
    }

    /**
     * @return array
     */
    private function getFinancingFixture()
    {
        return require __DIR__ . '/_fixtures/FinancingResponseFixture.php';
    }
}
