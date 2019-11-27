<?php

namespace Script;

use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Loader\ArrayLoader;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Node;

class Main
{
    /**
     * @var ContainerInterface
     */
    private $c;

    public function __construct(ContainerInterface $c)
    {
        $this->c = $c;
    }

    public function run()
    {
        $templates = $this->c
            ->get('mail_template.repository')
            ->search(new Criteria(), Context::createDefaultContext());

        /** @var MailTemplateEntity $template */

        $result = [];
        foreach ($templates as $template) {
            $result[] = [
                'variables' => array_values($this->getVariablesOfMail($template)),
                'template' => $template->getTranslation('subject')
            ];
        }
        dd($result);
    }

    private function getVariablesOfMail(MailTemplateEntity $template): array
    {
        $loader = new ArrayLoader([
            'mail.html.twig' => $template->getContentHtml(),
        ]);

        $source = $loader->getSourceContext('mail.html.twig');

        $twig = $this->c->get('twig');

        $stream = $twig->tokenize($source);

        $parsed = $twig->parse($stream);

        return $this->getVariables($parsed);
    }

    private function getVariables(iterable $nodes): array
    {
        $variables = [];
        foreach ($nodes as $node) {
            if ($node instanceof \Twig_Node_Expression_Name) {
                $name = $node->getAttribute('name');
                $variables[$name] = $name;

                continue;
            }

            if ($node instanceof ConstantExpression && $nodes instanceof GetAttrExpression) {
                $value = $node->getAttribute('value');
                if (!empty($value) && is_string($value)) {
                    $variables[$value] = $value;
                }

                continue;
            }

            if ($node instanceof GetAttrExpression) {
                $path = implode('.', $this->getVariables($node));
                if (!empty($path)) {
                    $variables[$path] = $path;
                }

                continue;
            }

            if ($node instanceof Node) {
                $variables += $this->getVariables($node);
                continue;
            }
        }

        return $variables;
    }
}

$x = new Main(require  __DIR__ . '/boot/boot.php');
$x->run();

//$loader1 = new Twig_Loader_Array([
//    'blub.html' => '{{ twig.template.code }}',
//]);
//$twig = new Twig_Environment($loader1);
//$tokens = $twig->tokenize($loader1->getSource('blub.html'));
//$nodes = $twig->getParser()->parse($tokens);
//
//var_dump($this->getTwigVariableNames($nodes));
//
//






