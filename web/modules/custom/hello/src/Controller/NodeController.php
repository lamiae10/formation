<?php

declare(strict_types=1);

namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Hello routes.
 */
final class NodeController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    $nodeStorage = $this->entityTypeManager()->getStorage('node');
    $query = $nodeStorage->getQuery();
    $query->accessCheck(FALSE);
    $query->pager(12);
    $nids = $query->execute();
   // ksm($nids);
    $node = $nodeStorage->loadMultiple($nids);
    $rows = [];
    foreach ($node as $nid => $node) {
      $rows[] =[
        'nid' => $node->id(),
        'title' => $node->getTitle(),
        'type' => $node->bundle(),
      ];
    }
    $headers = [
      $this->t('Id'),
      $this->t('title'),
      $this->t('type')
    ];

    $build['content'] = [
      '#type' => 'table',
      '#rows' => $rows,
      '#header' => $headers,
    ];
    $build['pager'] = [
      '#type' => 'pager',

    ];
    $build['#cache']['tags'] = ['node_list'];

    return $build;
  }

}
