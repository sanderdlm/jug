<?php

namespace Jug\Twig;

use Twig\Environment;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\AssignNameExpression;
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
     * @return array<string, string>
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
     * @param array<string, string> $variables
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
                            $key = $correspondingValue[$nestedIndex - 1]->getAttribute('value');
                            $value = $nestedValue->getAttribute('value');
                            $variables[$definedVariableName][$key] = $value;
                        }
                    }
                } else {
                    $variables[$definedVariableName] = $correspondingValue->getAttribute('value');
                }
            }
        }

        foreach ($node as $child) {
            if ($child instanceof Node) {
                $this->process($child, $variables);
            }
        }
    }

    private function getNodes(Node $parent, string $nodeClass): array
    {
        $matches = [];

        foreach ($parent as $child) {
            if ($child instanceof ArrayExpression) {
                $matches[] = $this->getNodes($child, ConstantExpression::class);
            } else {
                if (
                    $child instanceof $nodeClass &&
                    is_subclass_of($child, Node::class)
                ) {
                    $matches[] = $child;
                }
            }
        }

        return $matches;
    }
}
