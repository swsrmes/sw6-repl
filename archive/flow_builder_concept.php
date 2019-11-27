<?php


require_once __DIR__ . '/boot/boot.php';


class Sequence
{
    public ?string $ruleId = null;
    public ?string $action = null;
    public ?array $config = null;
    public ?Sequence $falseCase = null;
    public ?Sequence $trueCase = null;

    public function isIf(): bool
    {
        return $this->ruleId !== null;
    }

    public function isAction(): bool
    {
        return $this->action !== null;
    }

    public static function createIF(string $ruleId, ?Sequence $true, ?Sequence $false): self
    {
        $sequence = new Sequence();
        $sequence->ruleId = $ruleId;
        $sequence->trueCase = $true;
        $sequence->falseCase = $false;

        return $sequence;
    }

    public static function createAction(string $action, ?array $config = null): self
    {
        $sequence = new Sequence();
        $sequence->action = $action;
        $sequence->config = $config;

        return $sequence;
    }
}


class FlowState
{
    public bool $stop = false;
}


function executeFlow(array $sequences, FlowState $state, \Shopware\Core\Framework\Context $context)
{
    /** @var Sequence $sequence */
    foreach ($sequences as $sequence) {
        executeSequence($sequence, $state, $context);

        if ($state->stop) {
            return;
        }
    }
}

function executeSequence(?Sequence $sequence, FlowState $state, \Shopware\Core\Framework\Context $context)
{
    if ($sequence === null) {
        return;
    }

    if ($sequence->isIf()) {
        executeIf($sequence, $state, $context);
        return;
    }

    executeAction($sequence, $state, $context);
}

function executeAction(Sequence $sequence, FlowState $state, \Shopware\Core\Framework\Context $context)
{
    echo PHP_EOL . $sequence->action . ' executed';
    // build business event - see \Shopware\Core\Framework\Event\BusinessEventDispatcher::callActions
}

function executeIf(Sequence $sequence, FlowState $state, \Shopware\Core\Framework\Context $context)
{
    if (in_array($sequence->ruleId, $context->getRuleIds(), true)) {
        executeSequence($sequence->trueCase, $state, $context);

        return;
    }

    executeSequence($sequence->falseCase, $state, $context);
}

$flow = [
    Sequence::createIF(
        'is-new-customer',
        Sequence::createAction('add-tag', ['tag' => '...']),
        null
    ),
    Sequence::createIF(
        'is-german-customer',
        Sequence::createIF(
            'is-merchant',
            Sequence::createAction('add-tag', ['tag' => 'merchant']),
            Sequence::createAction('add-tag', ['tag' => 'normal'])
        ),
        Sequence::createAction('stop-flow')
    ),
];

$context = \Shopware\Core\Framework\Context::createDefaultContext();
$context->setRuleIds(['is-german-customer']);

executeFlow($flow, new FlowState(), $context);





















