<?php

namespace Jug\Twig;

use Twig\Environment;
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
            $nameMatches = $this->getNodes(
                $node->getNode('names'),
                AssignNameExpression::class
            );

            $valueMatches = $this->getNodes(
                $node->getNode('values'),
                ConstantExpression::class
            );

            foreach ($nameMatches as $index => $match) {
                $variables[$match->getAttribute('name')] = $valueMatches[$index]->getAttribute('value');
            }
        }

        foreach ($node as $child) {
            if ($child instanceof Node) {
                $this->process($child, $variables);
            }
        }
    }

    /**
     * @return array<Node>
     */
    private function getNodes(Node $parent, string $nodeClass): array
    {
        $matches = [];

        foreach ($parent as $child) {
            if (
                $child instanceof $nodeClass &&
                is_subclass_of($child, Node::class)
            ) {
                $matches[] = $child;
            }
        }

        return $matches;
    }
}
