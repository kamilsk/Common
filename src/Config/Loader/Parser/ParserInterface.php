<?php declare(strict_types = 1);

namespace OctoLab\Common\Config\Loader\Parser;

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
     * @throws \Exception
     */
    public function parse($content);

    /**
     * @param string $extension
     *
     * @return bool
     */
    public function supports($extension);
}
