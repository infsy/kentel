<?php

namespace Nurun\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NurunUserBundle extends Bundle
{
  public function getParent()
      {
          return 'FOSUserBundle';
      }
}
