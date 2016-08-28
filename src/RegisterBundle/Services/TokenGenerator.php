<?php

namespace RegisterBundle\Services;
/**
 * Token Generator
 */
class TokenGenerator
{
    /**
     * @return string
     */
    public function generate()
    {
        return md5(uniqid() . uniqid());
    }
}
