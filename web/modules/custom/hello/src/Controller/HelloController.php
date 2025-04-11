<?php

declare(strict_types=1);

namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Hello routes.
 */
final class HelloController extends ControllerBase {

  /**
   * The controller constructor.
   */
  public function __construct(
    private readonly Connection $connection,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('database'),
    );
  }

  /**
   * Builds the response.
   */
  public function __invoke(int $number =10): array
  {
    ksm(\Drupal::entityTypeManager()->getDefinitions());
    $database = $this->connection;
    // Get watchdog messages.
    $result = $database->select('watchdog', 'w')
      ->fields('w', ['wid', 'type', 'message'])
      ->orderBy('wid', 'DESC')
      ->range(0, $number)
      ->execute();
    // Display the messages into a table
    $header = [
      'wid' => $this->t('ID'),
      'type' => $this->t('Type'),
      'message' => $this->t('Message'),
    ];

    $rows= [];
    foreach ($result as $result) {
      $rows[] =[
        'wid' => $result->wid,
        'type' => $result->type,
        'message' => $result->message,
      ];
    }
    $build['table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No messages found.'),

    ];
    $build['#cache'] =[
      'contexts' => ['user'],
      'tags' => ['test:hello'],
      'max-age' => 10,


    ];
    return $build;
  }

}
