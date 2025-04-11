<?php

declare(strict_types=1);

namespace Drupal\hello\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a Hello form.
 */
final class ExampleForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'hello_example';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $last_submit_time = \Drupal::state()->get('hello_form_submit');

    if ($last_submit_time) {
      $formatted_time = \Drupal::service('date.formatter')->format($last_submit_time, 'short');
      $form['last_submission'] = [
        '#markup' => '<p><strong>' . $this->t('Last submission: @date', ['@date' => $formatted_time]) . '</strong></p>',
      ];
    }

    $users = \Drupal::entityTypeManager()->getStorage('user')->loadMultiple();
    $options = [];
    foreach ($users as $user) {
      $options[$user->id()] = $user->getdisplayName();
    }
    $form['auteur'] = [
      '#type' => 'select',
      '#options' => $options,
      '#title' => $this->t('Author'),
      '#required' => TRUE,
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Titre'),
      '#required' => TRUE,
      '#maxlength' => 15,
    ];

    $form['actions'] = [
      '#type' => 'actions',
      'submit' => [
        '#type' => 'submit',
        '#value' => $this->t('Send'),
      ],
    ];

    return $form;
  }



  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $node = \Drupal::entityTypeManager()->getStorage('node')->create([
      'type' => 'article',
      'status' => 1,
      'title' => $form_state->getValue('title'),
      'uid' => $form_state->getValue('auteur'),
    ]);
    $node->save();
    $form_state->setRedirect('entity.node.canonical', ['node' => $node->id()]);

    $now = \Drupal::time()->getRequestTime();
    \Drupal::state()->set('hello_form_submit', $now);
  }

}
