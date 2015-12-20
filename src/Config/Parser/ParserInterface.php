<?php

namespace OctoLab\Common\Config\Parser;

/**
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
