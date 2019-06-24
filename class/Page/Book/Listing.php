<?php


namespace Avorg\Page\Book;

use Avorg\AvorgApi;
use Avorg\BookRepository;
use Avorg\Page;
use Avorg\Renderer;
use Avorg\RouteFactory;
use Avorg\WordPress;
use function defined;

if (!defined('ABSPATH')) exit;

class Listing extends Page
{
	/** @var BookRepository $bookRepository */
	private $bookRepository;

	protected $defaultPageTitle = "Books";
	protected $defaultPageContent = "Books";
	protected $twigTemplate = "page-books.twig";

	public function __construct(BookRepository $bookRepository, Renderer $renderer, WordPress $wp)
	{
		parent::__construct($renderer, $wp);

		$this->bookRepository = $bookRepository;
	}

	public function throw404($query)
	{
		// TODO: Implement throw404() method.
	}

	protected function getData()
	{
		return [
			"books" => $this->bookRepository->getBooks()
		];
	}

	protected function getEntityTitle()
	{
		// TODO: Implement getEntityTitle() method.
	}
}