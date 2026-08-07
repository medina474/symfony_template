<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class HighlightExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('highlight', [$this, 'highlight'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function highlight(?string $text, ?string $search): string
    {
        if ($text === null) {
            return '';
        }
        
        if ($search === null || trim($search) === '') {
            return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        $escapedText = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $escapedSearch = preg_quote(
            htmlspecialchars($search, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            '/'
        );

        return preg_replace(
            "/($escapedSearch)/iu",
            '<strong>$1</strong>',
            $escapedText
        );
    }
}
