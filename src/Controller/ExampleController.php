<?php
namespace Schachbulle\ContaoFernschachBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/iccfimport', name: ExampleController::class)]
class ExampleController
{
	public function __invoke(Request $request): Response
	{
		return new Response('Hello World!');
	}
}
