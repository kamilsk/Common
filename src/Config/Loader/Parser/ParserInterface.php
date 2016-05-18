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
     *
     * @api
     */
    public function parse(string $content);

    /**
     * @param string $extension
     *
     * @return bool
     *
     * @api
     */
    public function supports(string $extension): bool;
}
