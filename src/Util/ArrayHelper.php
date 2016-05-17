<?php

declare(strict_types = 1);

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class ArrayHelper
{
    /**
     * @param string $path
     * @param array $scope
     *
     * @return mixed null if not exists
     *
     * @api
     */
    public static function findByPath(string $path, array $scope)
    {
        if (mb_strpos($path, ':')) {
            foreach (explode(':', $path) as $i => $key) {
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
        foreach ($args as $next) {
            foreach ($next as $k => $v) {
                if (is_int($k)) {
                    $res[] = $v;
                } else {
                    $res[$k] = is_array($v) && isset($res[$k]) && is_array($res[$k]) ? static::merge($res[$k], $v) : $v;
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
     */
    public static function transform(array &$target, array $placeholders)
    {
        $wrap = function (string &$value) {
            $value = sprintf('/%s/', $value);
        };
        array_walk_recursive($target, function (&$param) use ($wrap, $placeholders) {
            if (!is_string($param)) {
                return;
            } elseif (preg_match('/^const\((.*)\)$/', $param, $matches)) {
                $param = constant($matches[1]);
            } elseif (preg_match_all('/%(.+?)%/', $param, $matches)) {
                array_walk($matches[0], $wrap);
                $pattern = $matches[0];
                $replacement = array_intersect_key($placeholders, array_flip($matches[1]));
                $param = preg_replace($pattern, $replacement, $param);
            }
        });
    }
}
