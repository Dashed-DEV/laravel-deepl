<?php

namespace Dashed\Deepl;

use Dashed\Deepl\Clients\DeeplClient;

class Deepl
{
    /**
     * Return Deepl client
     * @return DeeplClient
     */
    public function api(): DeeplClient
    {
        return new DeeplClient();
    }
}
