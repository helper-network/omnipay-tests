<?php

namespace Omnipay\Tests;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;

/**
 * Base Gateway Test class
 *
 * Ensures all gateways conform to consistent standards
 */
abstract class GatewayTestCase extends TestCase
{
	/** @var AbstractGateway */
	protected $gateway;

	public function testGetNameNotEmpty()
	{
		$name = $this->gateway->getName();
		$this->assertNotEmpty($name);
		$this->assertIsString($name);
	}

	public function testGetShortNameNotEmpty()
	{
		$shortName = $this->gateway->getShortName();
		$this->assertNotEmpty($shortName);
		$this->assertIsString($shortName);
	}

	public function testGetDefaultParametersReturnsArray()
	{
		$settings = $this->gateway->getDefaultParameters();
		$this->assertIsArray($settings);
	}

	public function testDefaultParametersHaveMatchingMethods()
	{
		$settings = $this->gateway->getDefaultParameters();
		foreach ($settings as $key => $default) {
			$getter = 'get'.ucfirst($this->camelCase($key));
			$setter = 'set'.ucfirst($this->camelCase($key));
			$value = uniqid('', true);

			$this->assertTrue(method_exists($this->gateway, $getter), "Gateway must implement $getter()");
			$this->assertTrue(method_exists($this->gateway, $setter), "Gateway must implement $setter()");

			// setter must return instance
			$this->assertSame($this->gateway, $this->gateway->$setter($value));
			$this->assertSame($value, $this->gateway->$getter());
		}
	}

	public function testTestMode()
	{
		$this->assertSame($this->gateway, $this->gateway->setTestMode(false));
		$this->assertFalse($this->gateway->getTestMode());

		$this->assertSame($this->gateway, $this->gateway->setTestMode(true));
		$this->assertTrue($this->gateway->getTestMode());
	}

	public function testCurrency()
	{
		// currency is normalized to uppercase
		$this->assertSame($this->gateway, $this->gateway->setCurrency('eur'));
		$this->assertSame('EUR', $this->gateway->getCurrency());
	}

	public function testSupportsAuthorize()
	{
		$supportsAuthorize = $this->gateway->supportsAuthorize();
		$this->assertIsBool($supportsAuthorize);

		if ($supportsAuthorize) {
			$this->assertInstanceOf(RequestInterface::class, $this->gateway->authorize());
		} else {
			$this->assertFalse(method_exists($this->gateway, 'authorize'));
		}
	}

	public function testSupportsCompleteAuthorize()
	{
		$supportsCompleteAuthorize = $this->gateway->supportsCompleteAuthorize();
		$this->assertIsBool($supportsCompleteAuthorize);

		if ($supportsCompleteAuthorize) {
			$this->assertInstanceOf(RequestInterface::class, $this->gateway->completeAuthorize());
		} else {
			$this->assertFalse(method_exists($this->gateway, 'completeAuthorize'));
		}
	}

	public function testSupportsCapture()
	{
		$supportsCapture = $this->gateway->supportsCapture();
		$this->assertIsBool($supportsCapture);

		if ($supportsCapture) {
			$this->assertInstanceOf(RequestInterface::class, $this->gateway->capture());
		} else {
			$this->assertFalse(method_exists($this->gateway, 'capture'));
		}
	}

	public function testSupportsPurchase()
	{
		$supportsPurchase = $this->gateway->supportsPurchase();
		$this->assertIsBool($supportsPurchase);

		if ($supportsPurchase) {
			$this->assertInstanceOf(RequestInterface::class, $this->gateway->purchase());
		} else {
			$this->assertFalse(method_exists($this->gateway, 'purchase'));
		}
	}

	public function testSupportsCompletePurchase()
	{
		$supportsCompletePurchase = $this->gateway->supportsCompletePurchase();
		$this->assertIsBool($supportsCompletePurchase);

		if ($supportsCompletePurchase) {
			$this->assertInstanceOf(RequestInterface::class, $this->gateway->completePurchase());
		} else {
			$this->assertFalse(method_exists($this->gateway, 'completePurchase'));
		}
	}

	public function testSupportsRefund()
	{
		$supportsRefund = $this->gateway->supportsRefund();
		$this->assertIsBool($supportsRefund);

		if ($supportsRefund) {
			$this->assertInstanceOf(RequestInterface::class, $this->gateway->refund());
		} else {
			$this->assertFalse(method_exists($this->gateway, 'refund'));
		}
	}

	public function testSupportsVoid()
	{
		$supportsVoid = $this->gateway->supportsVoid();
		$this->assertIsBool($supportsVoid);

		if ($supportsVoid) {
			$this->assertInstanceOf(RequestInterface::class, $this->gateway->void());
		} else {
			$this->assertFalse(method_exists($this->gateway, 'void'));
		}
	}

	public function testSupportsCreateCard()
	{
		$supportsCreate = $this->gateway->supportsCreateCard();
		$this->assertIsBool($supportsCreate);

		if ($supportsCreate) {
			$this->assertInstanceOf(RequestInterface::class, $this->gateway->createCard());
		} else {
			$this->assertFalse(method_exists($this->gateway, 'createCard'));
		}
	}

	public function testSupportsDeleteCard()
	{
		$supportsDelete = $this->gateway->supportsDeleteCard();
		$this->assertIsBool($supportsDelete);

		if ($supportsDelete) {
			$this->assertInstanceOf(RequestInterface::class, $this->gateway->deleteCard());
		} else {
			$this->assertFalse(method_exists($this->gateway, 'deleteCard'));
		}
	}

	public function testSupportsUpdateCard()
	{
		$supportsUpdate = $this->gateway->supportsUpdateCard();
		$this->assertIsBool($supportsUpdate);

		if ($supportsUpdate) {
			$this->assertInstanceOf(RequestInterface::class, $this->gateway->updateCard());
		} else {
			$this->assertFalse(method_exists($this->gateway, 'updateCard'));
		}
	}

	public function testAuthorizeParameters()
	{
		if ($this->gateway->supportsAuthorize()) {
			foreach ($this->gateway->getDefaultParameters() as $key => $default) {
				// set property on gateway
				$getter = 'get'.ucfirst($this->camelCase($key));
				$setter = 'set'.ucfirst($this->camelCase($key));
				$value = uniqid('', true);
				$this->gateway->$setter($value);

				// request should have matching property, with correct value
				$request = $this->gateway->authorize();
				$this->assertSame($value, $request->$getter());
			}
		} else {
			$this->expectNotToPerformAssertions();
		}
	}


	public function testCompleteAuthorizeParameters()
	{
		if ($this->gateway->supportsCompleteAuthorize()) {
			foreach ($this->gateway->getDefaultParameters() as $key => $default) {
				// set property on gateway
				$getter = 'get'.ucfirst($this->camelCase($key));
				$setter = 'set'.ucfirst($this->camelCase($key));
				$value = uniqid('', true);
				$this->gateway->$setter($value);

				// request should have matching property, with correct value
				$request = $this->gateway->completeAuthorize();
				$this->assertSame($value, $request->$getter());
			}
		} else {
			$this->expectNotToPerformAssertions();
		}
	}


	public function testCaptureParameters()
	{
		if ($this->gateway->supportsCapture()) {
			foreach ($this->gateway->getDefaultParameters() as $key => $default) {
				// set property on gateway
				$getter = 'get'.ucfirst($this->camelCase($key));
				$setter = 'set'.ucfirst($this->camelCase($key));
				$value = uniqid('', true);
				$this->gateway->$setter($value);

				// request should have matching property, with correct value
				$request = $this->gateway->capture();
				$this->assertSame($value, $request->$getter());
			}
		} else {
			$this->expectNotToPerformAssertions();
		}
	}


	public function testPurchaseParameters()
	{
		if ($this->gateway->supportsPurchase()) {
			foreach ($this->gateway->getDefaultParameters() as $key => $default) {
				// set property on gateway
				$getter = 'get'.ucfirst($this->camelCase($key));
				$setter = 'set'.ucfirst($this->camelCase($key));
				$value = uniqid('', true);
				$this->gateway->$setter($value);

				// request should have matching property, with correct value
				$request = $this->gateway->purchase();
				$this->assertSame($value, $request->$getter());
			}
		}
	}


	public function testCompletePurchaseParameters()
	{
		if ($this->gateway->supportsCompletePurchase()) {
			foreach ($this->gateway->getDefaultParameters() as $key => $default) {
				// set property on gateway
				$getter = 'get'.ucfirst($this->camelCase($key));
				$setter = 'set'.ucfirst($this->camelCase($key));
				$value = uniqid('', true);
				$this->gateway->$setter($value);

				// request should have matching property, with correct value
				$request = $this->gateway->completePurchase();
				$this->assertSame($value, $request->$getter());
			}
		}
		$this->expectNotToPerformAssertions();
	}

	public function testRefundParameters()
	{
		if ($this->gateway->supportsRefund()) {
			foreach ($this->gateway->getDefaultParameters() as $key => $default) {
				// set property on gateway
				$getter = 'get'.ucfirst($this->camelCase($key));
				$setter = 'set'.ucfirst($this->camelCase($key));
				$value = uniqid('', true);
				$this->gateway->$setter($value);

				// request should have matching property, with correct value
				$request = $this->gateway->refund();
				$this->assertSame($value, $request->$getter());
			}
		}
	}


	public function testVoidParameters()
	{
		if ($this->gateway->supportsVoid()) {
			foreach ($this->gateway->getDefaultParameters() as $key => $default) {
				// set property on gateway
				$getter = 'get'.ucfirst($this->camelCase($key));
				$setter = 'set'.ucfirst($this->camelCase($key));
				$value = uniqid('', true);
				$this->gateway->$setter($value);

				// request should have matching property, with correct value
				$request = $this->gateway->void();
				$this->assertSame($value, $request->$getter());
			}
		}
	}


	public function testCreateCardParameters()
	{
		if ($this->gateway->supportsCreateCard()) {
			foreach ($this->gateway->getDefaultParameters() as $key => $default) {
				// set property on gateway
				$getter = 'get'.ucfirst($this->camelCase($key));
				$setter = 'set'.ucfirst($this->camelCase($key));
				$value = uniqid('', true);
				$this->gateway->$setter($value);

				// request should have matching property, with correct value
				$request = $this->gateway->createCard();
				$this->assertSame($value, $request->$getter());
			}
		}
	}


	public function testDeleteCardParameters()
	{
		if ($this->gateway->supportsDeleteCard()) {
			foreach ($this->gateway->getDefaultParameters() as $key => $default) {
				// set property on gateway
				$getter = 'get'.ucfirst($this->camelCase($key));
				$setter = 'set'.ucfirst($this->camelCase($key));
				$value = uniqid('', true);
				$this->gateway->$setter($value);

				// request should have matching property, with correct value
				$request = $this->gateway->deleteCard();
				$this->assertSame($value, $request->$getter());
			}
		}
		$this->expectNotToPerformAssertions();
	}


	public function testUpdateCardParameters()
	{
		if ($this->gateway->supportsUpdateCard()) {
			foreach ($this->gateway->getDefaultParameters() as $key => $default) {
				// set property on gateway
				$getter = 'get'.ucfirst($this->camelCase($key));
				$setter = 'set'.ucfirst($this->camelCase($key));
				$value = uniqid('', true);
				$this->gateway->$setter($value);

				// request should have matching property, with correct value
				$request = $this->gateway->updateCard();
				$this->assertSame($value, $request->$getter());
			}
		}
		$this->expectNotToPerformAssertions();
	}
}
