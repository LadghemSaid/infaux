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
            new TwigFilter('formatComment', [AppRuntime::class, 'formatCommentFunction']),
            new TwigFilter('cast_to_array', [AppRuntime::class, 'castToArrayFunction']),
            new TwigFilter('commentMostLike', [AppRuntime::class, 'commentMostLikeFunction']),
        ];
    }


}
