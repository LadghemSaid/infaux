<?php

// src/Twig/AppRuntime.php
namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // this simple example doesn't define any dependency, but in your own
        // extensions, you'll need to inject services using this constructor
    }

    public function formatCommentFunction($arrayComment)
    {
        usort($arrayComment, function ($item1, $item2) {
            if ($item1->getCreatedAt() == $item2->getCreatedAt()) return 0;
            return $item1->getCreatedAt() < $item2->getCreatedAt() ? -1 : 1;
        });

        return $arrayComment;

    }
}
