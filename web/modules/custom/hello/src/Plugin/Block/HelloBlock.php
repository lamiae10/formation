<?php

declare(strict_types=1);

namespace Drupal\hello\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\Plugin\views\filter\Access;
use Drupal\Core\Access\AccessResult;
/**
 * Provides a hello block.
 */
#[Block(
  id: 'hello_hello',
  admin_label: new TranslatableMarkup('Hello'),
  category: new TranslatableMarkup('Hello'),
)]
final class HelloBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'example' => $this->t('Hello world!'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['example'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Example'),
      '#default_value' => $this->configuration['example'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['example'] = $form_state->getValue('example');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $message = \Drupal::config('hello.settings')->get('message');

      $build['content'] = [
          '#theme' => 'hello_block',
          '#message' => $message,
          '#cache' => [
              'tags' => ['config:hello.settings'],
          ]
      ];

      return $build;
  }
    protected function blockAccess(AccountInterface $account): \Drupal\Core\Access\AccessResult {
        // Exemple simple : seuls les utilisateurs connectés peuvent voir le bloc
        return AccessResult::allowedIfHasPermission($account, 'access hello');
    }

}
