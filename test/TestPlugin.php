<?php

final class TestPlugin extends Avorg\TestCase
{
	private $mediaPageInsertCall = array("wp_insert_post", array(
		"post_content" => "Media Detail",
		"post_title" => "Media Detail",
		"post_status" => "publish",
		"post_type" => "page"
	), true);
	
	private function assertPlayerUiInjected()
	{
		$haystack = $this->makePlayerUiHaystack();
		
		$this->assertContains("playerUI", $haystack);
	}
	
	private function makePlayerUiHaystack()
	{
		$this->mockTwig->setReturnValue("render", "playerUI");
		
		return $this->mockedPlugin->addMediaPageUI("");
	}
	
	protected function setUp()
	{
		parent::setUp();
		$this->mockWordPress->setReturnValue("call", 5);
	}
	
	public function testInsertsMediaDetailsPage()
	{
		$this->mockWordPress->setReturnValue("call", false);
		
		$this->mockedPlugin->activate();
		
		$this->assertCalledWith($this->mockWordPress, "call", ...$this->mediaPageInsertCall);
	}
	
	public function testDoesNotInsertPageTwice()
	{
		$this->mockWordPress->setReturnValue("call", ["post"]);
		
		$this->mockedPlugin->activate();
		
		$this->assertNotCalledWith($this->mockWordPress, "call", ...$this->mediaPageInsertCall);
	}
	
	public function testAddsMediaPageUI()
	{
		$this->assertPlayerUiInjected();
	}
	
	public function testPassesPageContent()
	{
		$haystack = $this->mockedPlugin->addMediaPageUI("content");
		
		$this->assertContains("content", $haystack);
	}
	
	public function testUsesTwig()
	{
		$this->mockedPlugin->addMediaPageUI("content");
		
		$this->assertCalledWith($this->mockTwig, "render", "organism-recording.twig", ["presentation" => null], true);
	}
	
	public function testOnlyOutputsMediaPageUIOnMediaPage()
	{
		$this->mockWordPress->setReturnValues("call", [1, 10]);
		
		$haystack = $this->makePlayerUiHaystack();
		
		$this->assertNotContains("playerUI", $haystack);
	}
	
	public function testPassesPresentationToTwig()
	{
		$this->mockAvorgApi->setReturnValue("getPresentation", "presentation");
		
		$this->mockedPlugin->addMediaPageUI("content");
		
		$this->assertCalledWith($this->mockTwig, "render", "organism-recording.twig", ["presentation" => "presentation"], true);
	}
	
	public function testGetsQueryVar()
	{
		$this->mockedPlugin->addMediaPageUI("content");
		
		$this->assertCalledWith($this->mockWordPress, "call", "get_query_var", "presentation_id");
	}
	
	public function testGetsPresentation()
	{
		$this->mockWordPress->setReturnValues("call", [7, 7, "54321"]);
		
		$this->mockedPlugin->addMediaPageUI("content");
		
		$this->assertCalledWith($this->mockAvorgApi, "getPresentation", "54321");
	}
	
	public function testInsertsMediaDetailsPageOnInit()
	{
		$this->mockWordPress->setReturnValue("call", false);
		
		$this->mockedPlugin->init();
		
		$this->assertCalledWith($this->mockWordPress, "call", ...$this->mediaPageInsertCall);
	}
	
	public function testActivatesRouterOnPluginActivate()
	{
		$this->mockedPlugin->activate();
		
		$this->assertCalledWith($this->mockRouter, "activate");
	}
	
	public function testSavesMediaPageId()
	{
		$this->mockWordPress->setReturnValues("call", [false, false, 7]);
		
		$this->mockedPlugin->createMediaPage();
		
		$this->assertCalledWith(
			$this->mockWordPress,
			"call",
			"update_option",
			"avorgMediaPageId",
			7
		);
	}
	
	public function testGetsMediaPageId()
	{
		$this->mockedPlugin->createMediaPage();
		
		$this->assertCalledWith($this->mockWordPress, "call", "get_option", "avorgMediaPageId");
	}
	
	public function testCreatesPageIfNoPageStatus()
	{
		$this->mockWordPress->setReturnValues("call", [7, false]);
		
		$this->mockedPlugin->createMediaPage();
		
		$this->assertCalledWith($this->mockWordPress, "call", ...$this->mediaPageInsertCall);
	}
	
	public function testChecksPostStatus()
	{
		$this->mockWordPress->setReturnValue("call", 7);
		
		$this->mockedPlugin->createMediaPage();
		
		$this->assertCalledWith($this->mockWordPress, "call", "get_post_status", 7);
	}
	
	public function testUntrashesMediaPage()
	{
		$this->mockWordPress->setReturnValues("call", [7, "trash"]);
		
		$this->mockedPlugin->createMediaPage();
		
		$this->assertCalledWith($this->mockWordPress, "call", "wp_publish_post", 7);
	}
	
	public function testConvertsMediaPageIdStringToNumber()
	{
		$this->mockWordPress->setReturnValues("call", ["5", 5]);
		
		$this->assertPlayerUiInjected();
	}
	
	public function testInitInitsContentBits()
	{
		$this->mockedPlugin->init();
		
		$this->assertCalled($this->mockContentBits, "init");
	}
	
	public function testInitInitsRouter()
	{
		$this->mockedPlugin->init();
		
		$this->assertCalled($this->mockRouter, "addRewriteRules");
	}
	
	public function testEnqueueScripts()
	{
		$this->mockedPlugin->enqueueScripts();
		
		$this->assertWordPressFunctionCalled("wp_enqueue_style");
	}
	
	public function testEnqueueScriptsGetsStylesheetUrl()
	{
		$this->mockedPlugin->enqueueScripts();
		
		$this->assertWordPressFunctionCalled("plugins_url");
	}
	
	public function testEnqueueScriptsUsesPathWhenEnqueuingStyle()
	{
		$this->mockWordPress->setReturnValue("call", "path");
		
		$this->mockedPlugin->enqueueScripts();
		
		$this->assertCalledWith($this->mockWordPress, "call", "wp_enqueue_style", "avorgStyle", "path");
	}
	
	public function testInitsListShortcode()
	{
		$this->mockedPlugin->init();
		
		$this->assertCalled($this->mockListShortcode, "addShortcode");
	}
	
	public function testEnqueuesVideoJsStyles()
	{
		$this->mockedPlugin->enqueueScripts();
		
		$this->assertWordPressFunctionCalledWith(
			"wp_enqueue_style",
			"avorgVideoJsStyle",
			"//vjs.zencdn.net/7.0/video-js.min.css"
		);
	}
	
	public function testEnqueuesVideoJsScript()
	{
		$this->mockedPlugin->enqueueScripts();
		
		$this->assertWordPressFunctionCalledWith(
			"wp_enqueue_script",
			"avorgVideoJsScript",
			"//vjs.zencdn.net/7.0/video.min.js"
		);
	}
	
	public function testEnqueuesVideoJsHlsScript()
	{
		$this->mockedPlugin->enqueueScripts();
		
		$this->assertWordPressFunctionCalledWith(
			"wp_enqueue_script",
			"avorgVideoJsHlsScript",
			"https://cdnjs.cloudflare.com/ajax/libs/videojs-contrib-hls/5.14.1/videojs-contrib-hls.min.js"
		);
	}
}