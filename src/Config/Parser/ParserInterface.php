<?php

namespace OctoLab\Common\Config\Parser;

/**
 * @deprecated
 * @todo will removed since 2.0 version
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
interface ParserInterface
{
    /**
     * @param string $content
     *
     * @return mixed
     *
     * @api
     */
    public function parse($content);
}
