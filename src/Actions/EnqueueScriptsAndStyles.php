<?php

declare(strict_types=1);


namespace StellarWP\AdminNotices\Actions;

use StellarWP\AdminNotices\AdminNotice;
use StellarWP\AdminNotices\Traits\HasNamespace;

class EnqueueScriptsAndStyles
{
    use HasNamespace;

    public function __invoke(AdminNotice ...$notices)
    {
        foreach ($notices as $notice) {
            $script = $notice->getScriptToEnqueue();
            $style = $notice->getStyleToEnqueue();

            if (($script || $style) && (new NoticeShouldRender($this->namespace))($notice)) {
                if ($script) {
                    $script->enqueue("stellarwp-{$this->namespace}-{$notice->getId()}");
                }

                if ($style) {
                    $style->enqueue("stellarwp-{$this->namespace}-{$notice->getId()}");
                }
            }
        }
    }
}
