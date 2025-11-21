<?php

namespace Tito10047\BatchSelectionBundle\Tests\Integration\Twig;

use Tito10047\BatchSelectionBundle\BatchSelectionBundle;
use Tito10047\BatchSelectionBundle\Enum\SelectionMode;
use Tito10047\BatchSelectionBundle\Service\SelectionInterface;
use Tito10047\BatchSelectionBundle\Service\SelectionManagerInterface;
use Tito10047\BatchSelectionBundle\Tests\Integration\Kernel\AssetMapperKernelTestCase;
use Twig\Environment;

class SelectionExtensionTest extends AssetMapperKernelTestCase
{
    public function testTwigFunctionsAreRegistered(): void
    {
        $container = self::getContainer();
        /** @var Environment $twig */
        $twig = $container->get('twig');

        $this->assertInstanceOf(Environment::class, $twig);

        foreach ([
            'batch_is_selected',
            'batch_row_selector',
            'batch_row_selector_all',
            'batch_selection_total',
            'batch_selection_count',
            'batch_stimulus_controller',
        ] as $functionName) {
            $this->assertNotNull($twig->getFunction($functionName), sprintf('Twig function %s should be registered.', $functionName));
        }
    }

    public function testTwigFunctionsBehavior(): void
    {
		$controllerName = BatchSelectionBundle::STIMULUS_CONTROLLER;
        $container = self::getContainer();

        /** @var SelectionManagerInterface $manager */
        $manager = $container->get(SelectionManagerInterface::class);

        // Prepare simple object list with "id" property
        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $o = new \stdClass();
            $o->id = $i;
            $o->name = 'Item '.$i;
            $items[] = $o;
        }

        // Register source under a key
        $selection = $manager->registerSource('twig_key', $items);
        $this->assertInstanceOf(SelectionInterface::class, $selection);
        $this->assertSame(3, $selection->getTotal(), 'Total after registerSource should reflect all items.');

        // Select one item (object with id=2)
        $selection->select($items[1]);

        /** @var Environment $twig */
        $twig = $container->get('twig');

        // batch_is_selected for id=2 should be true, for id=1 should be false
        $tpl = $twig->createTemplate(
            "{{ batch_is_selected('twig_key', item) ? 'YES' : 'NO' }}"
        );
        $outSelected = $tpl->render(['item' => $items[1]]); // id=2
        $outNotSelected = $tpl->render(['item' => $items[0]]); // id=1
        $this->assertSame('YES', $outSelected);
        $this->assertSame('NO', $outNotSelected);

        // batch_row_selector should include checked attribute only for selected item
        $tpl = $twig->createTemplate("{{ batch_row_selector('twig_key', item) }}");
        $htmlSelected = $tpl->render(['item' => $items[1]]);
        $htmlNotSelected = $tpl->render(['item' => $items[0]]);
        $this->assertStringContainsString('type=\'checkbox\'', $htmlSelected);
        $this->assertStringContainsString("data-{$controllerName}-target=\"checkbox\"", $htmlSelected);
        $this->assertStringContainsString('checked="checked"', $htmlSelected);
        $this->assertStringNotContainsString('checked="checked"', $htmlNotSelected);

        // batch_selection_total and batch_selection_count
        $tplTotal = $twig->createTemplate("{{ batch_selection_total('twig_key') }}");
        $tplCount = $twig->createTemplate("{{ batch_selection_count('twig_key') }}");
        $this->assertSame('3', trim($tplTotal->render()));
        $this->assertSame('1', trim($tplCount->render()));

        // batch_row_selector_all should not be checked in default INCLUDE mode
        $tplAll = $twig->createTemplate("{{ batch_row_selector_all('twig_key') }}");
        $htmlAll = $tplAll->render();
        $this->assertStringNotContainsString('checked="checked"', $htmlAll);

        // Switch to EXCLUDE mode; with current Selection::isSelectedAll() implementation,
        // the checkbox remains unchecked unless all items are excluded (which we don't do here)
        $selection->setMode(SelectionMode::EXCLUDE);
        $htmlAllExclude = $tplAll->render();
        $this->assertStringNotContainsString('checked="checked"', $htmlAllExclude);

        // batch_stimulus_controller should contain controller name and required URLs
        $tplStimulus = $twig->createTemplate("{{ batch_stimulus_controller('twig_key') }}");
        $attrs = $tplStimulus->render();
        $this->assertStringContainsString("data-controller=\"{$controllerName}\"", $attrs);
        $this->assertStringContainsString("data-{$controllerName}-url-toggle-value=\"", $attrs);
        $this->assertStringContainsString('/_batch-selection/toggle', $attrs);
        $this->assertStringContainsString("data-{$controllerName}-url-select-all-value=\"", $attrs);
        $this->assertStringContainsString('/_batch-selection/select-all', $attrs);
        $this->assertStringContainsString("data-{$controllerName}-key-value=\"twig_key\"", $attrs);
        $this->assertStringContainsString("data-{$controllerName}-manager-value=\"default\"", $attrs);
    }
}
