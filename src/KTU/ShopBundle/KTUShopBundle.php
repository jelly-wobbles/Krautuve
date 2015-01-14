<?php

namespace KTU\ShopBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class KTUShopBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
