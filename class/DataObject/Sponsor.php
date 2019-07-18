<?php

namespace Avorg\DataObject;


use Avorg\DataObject;
use Avorg\DataObjectRepository\PresentationRepository;
use Avorg\Router;

if (!defined('ABSPATH')) exit;

class Sponsor extends DataObject
{
	/** @var PresentationRepository $recordingRepository */
	private $recordingRepository;

	protected $detailClass = "Avorg\Page\Sponsor\Detail";

	public function __construct(PresentationRepository $recordingRepository, Router $router)
	{
		parent::__construct($router);

		$this->recordingRepository = $recordingRepository;
	}

	public function getRecordings()
	{
		return $this->recordingRepository->getSponsorPresentations($this->getId());
	}
}