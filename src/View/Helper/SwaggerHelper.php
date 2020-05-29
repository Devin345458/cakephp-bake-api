<?php


namespace Devin345458\Api\View\Helper;

use Cake\View\Helper;


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
            $type = $type ? $type . ' ' : '';
            $lines[] = "@OA\Property( type=\"{$type}\", property=\"{$property}\", description=\"{$property}\"),";
        }

        return $lines;
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
        $numItems = count($annotation);
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