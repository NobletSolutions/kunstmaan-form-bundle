<?php

namespace NS\KunstmaanFormBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FormBundle extends Bundle
{
    public function getParent()
    {
        return 'KunstmaanFormBundle';
    }
}
