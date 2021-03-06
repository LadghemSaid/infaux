<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            // the logic of this filter is now implemented in a different class
            new TwigFilter('getComment', [AppRuntime::class, 'getCommentFunction']),
            new TwigFilter('commentMostLike', [AppRuntime::class, 'commentMostLikeFunction']),
        ];
    }


}
