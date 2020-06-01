<?php


namespace Devin345458\Api\View\Helper;

use Cake\View\Helper;
use Cake\Collection\Collection;


class SwaggerHelper extends Helper
{

    /**
     * Renders a map of DocBlock property types as an array of
     * `@property` hints.
     *
     * @param string[] $properties A key value pair where key is the name of a property and the value is the type.
     * @return array
     */
    public function swaggerHints(array $properties): array
    {
        $lines = [];
        foreach ($properties as $property => $type) {
            $swagger_type = $type ? $this->_typeConversion($type): '';
            $line = "@OA\Property( type=\"{$swagger_type}\", property=\"{$property}\", description=\"{$property}\"";
            if ($type['kind'] === 'association') {
                $line .= ' ref="#/components/schemas/' . str_replace('\App\Model\Entity\\', '', $type['type']) . '"';
            }
            $lines[] = $line . ')';
        }

        return $lines;
    }

    protected function _typeConversion($type) {
        $result = $type['type'];
        switch ($type['type']) {
            case 'uuid':
                $result = 'string';
                break;
        }

        if ($type['kind'] === 'association') {
            $result = 'object';
        }

        return $result;
    }


    /**
     * Writes the DocBlock header for a class which includes the property and method declarations. Annotations are
     * sorted and grouped by type and value. Groups of annotations are separated by blank lines.
     *
     * @param string $className The class this comment block is for.
     * @param string $classType The type of class (example, Entity)
     * @param array $annotations An array of PHP comment block annotations.
     * @param array $swagger An array of PHP Swagger comment block annotations
     * @return string The DocBlock for a class header.
     */
    public function swaggerClassDescription(string $className, string $classType, array $annotations, array $swagger):
    string
    {
        $lines = [];
        if ($className && $classType) {
            $lines[] = "{$className} {$classType}";
        }

        if ($annotations && $lines) {
            $lines[] = '';
        }

        $previous = false;
        foreach ($annotations as $annotation) {
            if (strlen($annotation) > 1 && $annotation[0] === '@' && strpos($annotation, ' ') > 0) {
                $type = substr($annotation, 0, strpos($annotation, ' '));
                if (
                    $this->_annotationSpacing &&
                    $previous !== false &&
                    $previous !== $type
                ) {
                    $lines[] = '';
                }
                $previous = $type;
            }
            $lines[] = $annotation;
        }

        if ($className && $classType) {
            $lines[] = "@OA\Schema(title=\"{$className}\", description=\"{$classType}\",";
        }
        $numItems = count($swagger);
        foreach ($swagger as $key => $annotation ) {
            if ($key < $numItems) {
                $lines[] = $annotation . ',';
            } else {
                $lines[] = $annotation;
            }
        }

        $lines[] = ")";



        $lines = array_merge(["/**"], (new Collection($lines))->map(function ($line) {
            return rtrim(" * {$line}");
        })->toArray(), [" */"]);

        return implode("\n", $lines);
    }
}