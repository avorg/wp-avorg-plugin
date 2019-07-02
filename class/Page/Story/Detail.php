<?php


namespace Avorg\Page\Story;

use Avorg\DataObject;
use Avorg\DataObjectRepository\StoryRepository;
use Avorg\Page;
use Avorg\Renderer;
use Avorg\WordPress;
use function defined;
use Exception;

if (!defined('ABSPATH')) exit;

class Detail extends Page
{
	/** @var StoryRepository $storyRepository */
	private $storyRepository;

	protected $defaultPageTitle = "Story";
	protected $defaultPageContent = "Story";
	protected $twigTemplate = "page-story.twig";

	public function __construct(
		Renderer $renderer,
		StoryRepository $storyRepository,
		WordPress $wp
	)
	{
		parent::__construct($renderer, $wp);

		return $this->storyRepository = $storyRepository;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	protected function getData()
	{
		return [
			"story" => $this->getEntity()
		];
	}

	/**
	 * @return mixed
	 * @throws Exception
	 */
	protected function getTitle()
	{
		return $this->getEntity()->title;
	}

	/**
	 * @return DataObject
	 * @throws Exception
	 */
	protected function getEntity()
	{
		return $this->storyRepository->getStory($this->getEntityId());
	}
}