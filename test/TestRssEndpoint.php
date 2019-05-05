<?php

final class TestRssEndpoint extends Avorg\TestCase
{
	/** @var \Avorg\Endpoint\RssEndpoint $rssEndpoint */
	protected $rssEndpoint;

	public function setUp()
	{
		parent::setUp();

		$this->rssEndpoint = $this->factory->secure("Endpoint\\RssEndpoint\\RssLatest");
	}

	public function testGetRouteFormat()
	{
		$result = $this->rssEndpoint->getRoute();

		$this->assertInstanceOf("Avorg\\Route\\EndpointRoute", $result);
	}

	public function testRouteRedirect()
	{
		$route = $this->rssEndpoint->getRoute();

		$redirect = $route->getRewriteRules()["English"]["redirect"];

		$this->assertStringStartsWith(
			"endpoint.php?endpoint_id=Avorg_Endpoint_RssEndpoint_RssLatest", $redirect);
	}

	public function testRouteFormatSet()
	{
		$route = $this->rssEndpoint->getRoute();

		$regex = $route->getRewriteRules()["English"]["regex"];

		$this->assertContains("podcast", $regex);
	}

	public function testGetOutput()
	{
		$this->rssEndpoint->getOutput();

		$this->mockTwig->assertTwigTemplateRendered("page-feed.twig");
	}

	public function testReturnsOutput()
	{
		$this->mockTwig->setReturnValue("render", "rendered_template");

		$result = $this->rssEndpoint->getOutput();

		$this->assertEquals("rendered_template", $result);
	}

	public function testSetsHeader()
	{
		$this->rssEndpoint->getOutput();

		$this->mockPhp->assertMethodCalledWith(
			"header", 'Content-Type: application/rss+xml; charset=utf-8');
	}

	public function testSetsImage()
	{
		$this->rssEndpoint->getOutput();

		$this->mockTwig->assertTwigTemplateRenderedWithData("page-feed.twig", [
			"image" => AVORG_LOGO_URL
		]);
	}
}