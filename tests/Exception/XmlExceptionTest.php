<?php

declare(strict_types = 1);

namespace OctoLab\Common\Exception;

use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class XmlExceptionTest extends TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage $errors contains not LibXMLError.
     */
    public function construct()
    {
        new XmlException([new \stdClass()]);
    }

    /**
     * @test
     */
    public function getErrors()
    {
        $exception = new XmlException([new \LibXMLError()]);
        self::assertNotEmpty($exception->getErrors());
    }
}
