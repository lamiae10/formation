<?php

declare(strict_types=1);

namespace Drupal\hello\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\Type\DateTimeInterface;
use Symfony\Component\Routing\Route;
use Drupal\Component\Datetime\Time;
/**
 * Checks if passed parameter matches the route configuration.
 *
 * Usage example:
 * @code
 * foo.example:
 *   path: '/example/{parameter}'
 *   defaults:
 *     _title: 'Example'
 *     _controller: '\Drupal\hello\Controller\HelloController'
 *   requirements:
 *     _access_hello: 'some value'
 * @endcode
 */
final class AccessHelloAccessChecker implements AccessInterface {
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Time $time) {
    $this->entityTypeManager = $entity_type_manager;
    $this->time = $time;
  }

  /**
   * Access callback.
   *
   * @DCG
   * Drupal does some magic when resolving arguments for this callback. Make
   * sure the parameter name matches the name of the placeholder defined in the
   * route, and it is of the same type.
   * The following additional parameters are resolved automatically.
   *   - \Drupal\Core\Routing\RouteMatchInterface
   *   - \Drupal\Core\Session\AccountInterface
   *   - \Symfony\Component\HttpFoundation\Request
   *   - \Symfony\Component\Routing\Route
   */
  public function access(Route $route, AccountInterface $account): AccessResult {
    if ($account->isAnonymous()) {
      return AccessResult::forbidden();
    }
    $nbr_heures= $route->getRequirement('_access_hello');

    /** @var \Drupal\user\Entity\User $user */
    $user =\Drupal::entityTypeManager()->getStorage('user')->load($account->id());
    //dump($user);
    $created = $user->getCreatedTime();
    $now = \Drupal::time()->getRequestTime();

    return AccessResult::allowedif($now - $created > $nbr_heures * 3600);

  }
}
