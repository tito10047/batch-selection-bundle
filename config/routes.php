<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Tito10047\BatchSelectionBundle\Controller\SelectController;

/**
 * Definícia rout pre BatchSelectionBundle.
 *
 * @link https://symfony.com/doc/current/bundles/best_practices.html#routing
 */
return static function (RoutingConfigurator $routes): void {
    // Toggle jedného riadku (prepnutie select/unselect)
    $routes
        ->add('batch_selection_toggle', '/_batch-selection/toggle')
            ->controller([SelectController::class, 'rowSelectorToggle'])
            ->methods(['GET'])
    ;

    // Označiť/odznačiť všetky riadky podľa kľúča
    $routes
        ->add('batch_selection_select_all', '/_batch-selection/select-all')
            ->controller([SelectController::class, 'rowSelectorSelectAll'])
            ->methods(['GET'])
    ;

    // Označiť/odznačiť viac ID naraz (POST body: id[]=1&id[]=2...)
    $routes
        ->add('batch_selection_select_range', '/_batch-selection/select-range')
            ->controller([SelectController::class, 'rowSelectorSelectRange'])
            ->methods(['POST'])
    ;
};
