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

    public function formatCommentFunction($arrayComment,$order)
    {
        if($order=='DESC'){
            usort($arrayComment, function ($item1, $item2) {
                if ($item1->getCreatedAt() == $item2->getCreatedAt()) return 0;
                return $item1->getCreatedAt() < $item2->getCreatedAt() ? -1 : 1;
            });
        }else{
            usort($arrayComment, function ($item1, $item2) {
                if ($item1->getCreatedAt() == $item2->getCreatedAt()) return 0;
                return $item1->getCreatedAt() > $item2->getCreatedAt() ? -1 : 1;
            });
        }


        return $arrayComment;

    }
    public function castToArrayFunction($stdClassObject)
    {
        $response = array();
        foreach ($stdClassObject as $key => $value) {
            $response[] = array($key, $value);
        }
        return $response;

    }

}
