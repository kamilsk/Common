<?php

namespace OctoLab\Common\Config\Parser;

use Symfony\Component\Yaml\Parser as BaseYamlParser;

/**
 * @deprecated use {@link Symfony\Component\Yaml\Parser} instead
 * @todo will removed since 2.0 version
 *
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class SymfonyYamlParser extends BaseYamlParser implements ParserInterface
{
}
