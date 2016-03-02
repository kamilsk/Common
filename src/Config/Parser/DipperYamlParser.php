<?php

namespace OctoLab\Common\Config\Parser;

use secondparty\Dipper\Dipper;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class DipperYamlParser implements ParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($content)
    {
        return Dipper::parse($content);
    }
}
