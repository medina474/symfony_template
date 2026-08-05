<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

final class PageController extends AbstractController
{
    #[Route(
        '/pages/{path}', 
        name: 'page', 
        requirements: ['path' => '.+'],
    )]
    public function __invoke(string $path, Environment $twig): Response
    {
        if (str_contains($path, '..')) {
            throw $this->createNotFoundException();
        }
        
        $loader = $twig->getLoader();
        $template = "pages/$path.html.twig";
   
        if (!$loader->exists($template)) {
            $template = "pages/$path/index.html.twig";

            if (!$loader->exists($template)) {
                throw $this->createNotFoundException();
            }
        }

        return $this->render($template);
    }
}
