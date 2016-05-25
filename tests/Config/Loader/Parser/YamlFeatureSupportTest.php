<?php

declare(strict_types = 1);

namespace OctoLab\Common\Config\Loader\Parser;

use OctoLab\Common\TestCase;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 *
 * @see http://symfony.com/doc/current/components/yaml/yaml_format.html
 * @see https://habrahabr.ru/post/270097/
 * @see http://yaml.org
 */
class YamlFeatureSupportTest extends TestCase
{
    /**
     * @test
     */
    public function comment()
    {
        $yaml = <<<'YAML'
# this is a comment for component block
component:
  key: value # this is a comment for key field
YAML;
        self::assertEquals(['component' => ['key' => 'value']], $this->getParser()->parse($yaml));
    }

    /**
     * @test
     */
    public function inheritance()
    {
        $yaml = <<<'YAML'
_basic: &basic
  property1: 1
  property2: 2

inheritance:
  first:
    <<: *basic
    property2: 1
  # not supported - first: { <<: *basic, property2: 1 } 

  second:
    <<: *basic
    property1: 2
YAML;
        $result = $this->getParser()->parse($yaml)['inheritance'];
        self::assertEquals($result['first']['property1'], $result['first']['property2']);
        self::assertEquals($result['second']['property1'], $result['second']['property2']);
    }

    /**
     * @test
     */
    public function multiline()
    {
        $yaml = <<<'YAML'
multilines:
  # ugly
  first: "line1\nline2\n"

  # with line ending
  second: |
    line1
    line2

  # without line ending
  third: |-
    line1
    line2
YAML;
        $result = $this->getParser()->parse($yaml)['multilines'];
        self::assertEquals($result['first'], $result['second']);
        self::assertEquals($result['first'], $result['third'] . "\n");
    }

    /**
     * @test
     */
    public function reference()
    {
        $yaml = <<<'YAML'
reference:
  first: &ref value
  second: *ref
YAML;
        $result = $this->getParser()->parse($yaml)['reference'];
        self::assertEquals($result['first'], $result['second']);
    }

    /**
     * @test
     */
    public function singleline()
    {
        $yaml = <<<'YAML'
singlelines:
  # simple
  first: single line text
  # not supported
  # first:
  #   single
  #   line
  #   text

  # with line ending
  second: >
    single
    line
    text

  # without line ending
  third: >-
    single
    line
    text
YAML;
        $result = $this->getParser()->parse($yaml)['singlelines'];
        self::assertEquals($result['first'] . "\n", $result['second']);
        self::assertEquals($result['first'], $result['third']);
    }

    /**
     * @test
     */
    public function style()
    {
        $yaml = <<<'YAML'
styles:
  # classic
  first:
    property: value

  # json-like
  second: { property: value }

  matrices:
    # classic
    first: 
    - [1, 0, 0]
    - [0, 1, 0]
    - [0, 0, 1]

    # json-like - not supported
    # second: [
    #   [1, 0, 0],
    #   [0, 1, 0],
    #   [0, 0, 1],
    # ]
YAML;
        $result = $this->getParser()->parse($yaml)['styles'];
        self::assertEquals($result['first'], $result['second']);
        // self::assertEquals($result['matrices']['first'], $result['matrices']['second']);
    }

    /**
     * @return YamlParser
     */
    private function getParser(): YamlParser
    {
        return new YamlParser();
    }
}
