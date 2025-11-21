<?php

namespace Tito10047\BatchSelectionBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SelectionExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('batch_is_selected', [SelectionRuntime::class, 'isSelected']),
            new TwigFunction('batch_row_selector', [SelectionRuntime::class, 'rowSelector'], ['is_safe' => ['html']]),
            new TwigFunction('batch_row_selector_all', [SelectionRuntime::class, 'rowSelectorAll'], ['is_safe' => ['html']]),
            new TwigFunction('batch_selection_total', [SelectionRuntime::class, 'getTotal']),
            new TwigFunction('batch_selection_count', [SelectionRuntime::class, 'getSelectedCount']),
            new TwigFunction('batch_stimulus_controller', [SelectionRuntime::class, 'getStimulusController'], ['is_safe' => ['html']]),
        ];
    }
}
