<?php

declare(strict_types=1);

namespace Jug\Twig;

use Twig\Environment;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Expression\Binary\AbstractBinary;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\Node\SetNode;

class Parser
{
    public function __construct(
        private readonly Environment $twig
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function parse(string $twigTemplateName): array
    {
        $source = $this->twig->getLoader()->getSourceContext($twigTemplateName);
        $node = $this->twig->parse($this->twig->tokenize($source));

        $variables = [];

        $this->process($node, $variables);

        return $variables;
    }

    /**
     * @param array<string, mixed> $variables
     */
    private function process(Node $node, array &$variables): void
    {
        if ($node instanceof SetNode) {
            $variableNameNodes = $this->getNodes(
                $node->getNode('names'),
                AssignNameExpression::class
            );

            $variableValueNodes = $this->getNodes(
                $node->getNode('values'),
                ConstantExpression::class
            );

            foreach ($variableNameNodes as $index => $variableNameNode) {
                $definedVariableName = $variableNameNode->getAttribute('name');
                $correspondingValue = $variableValueNodes[$index];

                if (is_array($correspondingValue)) {
                    foreach ($correspondingValue as $nestedIndex => $nestedValue) {
                        /*
                         * Twig returns all ArrayExpressions with both the keys and
                         * the values in the same list. To get the correct end result
                         * for both objects and arrays, we have to loop over the array
                         * and match all the key/value pairs. We assume that the first
                         * element is the first key, and from the on, each uneven element
                         * contains the value for the key that comes right before it.
                         */
                        if ($nestedIndex !== 0 && $nestedIndex % 2 !== 0) {
                            /** @var ConstantExpression $keyNode */
                            $keyNode = $correspondingValue[$nestedIndex - 1];
                            /** @var ConstantExpression $nestedValue */
                            $key = $keyNode->getAttribute('value');
                            $value = $nestedValue->getAttribute('value');
                            $variables[$definedVariableName][$key] = $value; // @phpstan-ignore parameterByRef.type
                        }
                    }
                } else {
                    /** @var ConstantExpression $correspondingValue */
                    $variables[$definedVariableName] = // @phpstan-ignore parameterByRef.type
                        $correspondingValue->getAttribute('value');
                }
            }
        }

        foreach ($node as $child) {
            $this->process($child, $variables);
        }
    }

    /** @phpstan-ignore-next-line */
    private function getNodes(Node $parent, string $nodeClass): array
    {
        $matches = [];

        foreach ($parent as $child) {
            if ($child instanceof ArrayExpression) {
                $matches[] = $this->getNodes($child, ConstantExpression::class);
            } elseif ($child instanceof AbstractBinary) {
                $matches[] = $this->getNodes($child, ConstantExpression::class);
            } else {
                if ($child instanceof $nodeClass) {
                    $matches[] = $child;
                }
            }
        }

        return $matches;
    }
}
