<?php

namespace Avorg\Page;

use Avorg\AvorgApi;
use Avorg\Page;
use Avorg\PresentationRepository;
use Avorg\Renderer;
use Avorg\WordPress;

if (!\defined('ABSPATH')) exit;

class Media extends Page
{
    /** @var AvorgApi $avorgApi */
    protected $avorgApi;

    /** @var PresentationRepository $presentationRepository */
    protected $presentationRepository;

    protected $defaultPageTitle = "Media Detail";
    protected $defaultPageContent = "Media Detail";
    protected $twigTemplate = "organism-recording.twig";
    protected $route = AVORG_BASE_ROUTE_TOKEN . "/sermons/recordings/" . AVORG_ENTITY_ID_TOKEN . "/" . AVORG_VARIABLE_FRAGMENT_TOKEN;

    public function __construct(
    	AvorgApi $avorgApi,
		PresentationRepository $presentationRepository,
		Renderer $renderer,
		WordPress $wordPress
	)
    {
        parent::__construct($renderer, $wordPress);

        $this->avorgApi = $avorgApi;
        $this->presentationRepository = $presentationRepository;
    }
	
	public function throw404($query)
	{
		try {
			$this->getEntity();
		} catch (\Exception $e) {
			$this->set404($query);
		}
	}

	/**
	 * @param $title
	 * @return string
	 */
	public function setTitle($title)
	{
		$presentation = $this->getEntitySafe();

		return $presentation ? "{$presentation->getTitle()} - AudioVerse" : $title;
	}

	/**
	 * @return array
	 */
	protected function getTwigData()
	{
		$entity = $this->getEntitySafe();

		return ["presentation" => $entity];
	}

	/**
	 * @return \Avorg\Presentation|null
	 */
	private function getEntitySafe()
	{
		try {
			return $this->getEntity();
		} catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * @return \Avorg\Presentation|null
	 * @throws \Exception
	 */
	private function getEntity()
	{
		$entityId = $this->getEntityId();

		return $this->presentationRepository->getPresentation($entityId);
	}


}