<?php


use Avorg\BlockLoader;

final class TestBlockLoader extends Avorg\TestCase
{
	/** @var BlockLoader $repository */
	protected $repository;

	public function setUp()
	{
		parent::setUp();

		$this->repository = $this->factory->secure("Avorg\\BlockLoader");
	}

	// Helpers

	private function assertSystemJsRegistered()
	{
		$this->mockWordPress->assertMethodCalledWith('wp_register_script',
			'system-js', AVORG_BASE_URL . "/node_modules/systemjs/dist/system.js");
	}

	private function assertTypeScriptLoaderRegistered($handle)
	{
		$this->mockWordPress->assertMethodCalledWith("wp_register_script",
			$handle,
			AVORG_BASE_URL . 'script/ts_loader.js',
			['wp-blocks', 'wp-element', 'system-js']);
	}

	// Tests

	public function testInitRegistersTypeScriptLoader()
	{
		$this->repository->enqueueBlockEditorAssets();

		$this->assertTypeScriptLoaderRegistered('avorg_block_editor_scripts');
	}

	public function testRegistersSystemJs()
	{
		$this->repository->enqueueBlockEditorAssets();

		$this->assertSystemJsRegistered();
	}

	public function testEnqueuesTypescriptLoader()
	{
		$this->repository->enqueueBlockEditorAssets();

		$this->mockWordPress->assertMethodCalledWith('wp_enqueue_script', 'avorg_block_editor_scripts');
	}

	public function testGetsBlockIndices()
	{
		$this->repository->enqueueBlockEditorAssets();

		$this->mockFilesystem->assertMethodCalledWith('getMatchingPathsRecursive',
			'component/block', '/index\.js$/');
	}

	public function testLocalizesLoaderScript()
	{
		$this->mockWordPress->setReturnValue('get_all_query_vars', ['GET']);

		$this->mockFilesystem->setReturnValue('getMatchingPathsRecursive', [
			AVORG_BASE_PATH . 'component/block/layer/name/index.js'
		]);

		$this->repository->enqueueBlockEditorAssets();

		$this->mockWordPress->assertMethodCalledWith('wp_localize_script',
			'avorg_block_editor_scripts', 'avorg_scripts', [
				'urls' => [AVORG_BASE_URL . 'component/block/layer/name/index.js'],
				'query' => ['GET']
			]);
	}

	public function testEnqueuingFrontendAssets()
	{
		$this->repository->enqueueBlockFrontendAssets();

		$this->assertSystemJsRegistered();
	}

	public function testEnqueueFrontendAssetsRegistersTypeScriptLoader()
	{
		$this->repository->enqueueBlockFrontendAssets();

		$this->assertTypeScriptLoaderRegistered('avorg_block_frontend_scripts');
	}

	public function testEnqueueFrontendAssetsLocalizesTypeScriptLoader()
	{
		$this->mockWordPress->setReturnValue('get_all_query_vars', ['GET']);

		$this->mockFilesystem->setReturnValue('getMatchingPathsRecursive', [
			AVORG_BASE_PATH . 'component/block/layer/name/frontend.js'
		]);

		$this->repository->enqueueBlockFrontendAssets();

		$this->mockWordPress->assertMethodCalledWith('wp_localize_script',
			'avorg_block_frontend_scripts', 'avorg_scripts', [
				'urls' => [AVORG_BASE_URL . 'component/block/layer/name/frontend.js'],
				'query' => ['GET']
			]);
	}

	public function testEnqueueFrontendAssetsEnqueuesTypescriptLoader()
	{
		$this->repository->enqueueBlockFrontendAssets();

		$this->mockWordPress->assertMethodCalledWith('wp_enqueue_script', 'avorg_block_frontend_scripts');
	}
}