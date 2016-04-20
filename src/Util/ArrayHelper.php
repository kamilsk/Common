<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class ArrayHelper
{
    /**
     * @param array $scope
     * @param string $path
     *
     * @return mixed null if not exists
     *
     * @api
     *
     * @quality:method [B]
     */
    public static function findByPath(array $scope, string $path)
    {
        if (mb_strpos($path, ':')) {
            $chain = explode(':', $path);
            foreach ($chain as $i => $key) {
                if (!isset($scope[$key])) {
                    return null;
                }
                $scope = $scope[$key];
            }
            return $scope;
        }
        return $scope[$path] ?? null;
    }

    /**
     * Merges two or more arrays into one recursively.
     *
     * Based on yii\helpers\BaseArrayHelper::merge.
     *
     * @param array[] ...$args
     *
     * @return array
     *
     * @author Qiang Xue <qiang.xue@gmail.com>
     *
     * @api
     *
     * @quality:method [B]
     */
    public static function merge(array ...$args): array
    {
        $res = array_shift($args);
        while ($args !== []) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (is_int($k)) {
                    $res[] = $v;
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = static::merge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }

    /**
     * @param array $target
     * @param array $placeholders
     *
     * @api
     *
     * @quality:method [B]
     */
    public static function transform(array &$target, array $placeholders)
    {
        $wrap = function (&$value) {
            $value = sprintf('/%s/', (string)$value);
        };
        array_walk_recursive($target, function (&$param) use ($wrap, $placeholders) {
            if (!is_string($param)) {
                return;
            } elseif (preg_match('/^const\((.*)\)$/', $param, $matches)) {
                $param = constant($matches[1]);
            } elseif (preg_match_all('/%([^%]+)%/', $param, $matches)) {
                array_walk($matches[0], $wrap);
                $pattern = $matches[0];
                $replacement = array_intersect_key($placeholders, array_flip($matches[1]));
                $param = preg_replace($pattern, $replacement, $param);
            }
        });
    }
}
