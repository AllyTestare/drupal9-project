<?php

namespace Drupal\ausy_family_event\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration settings form for add department.
 */
class AddDepartmentForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   *
   * @return mixed[]
   *   Get config names.
   */
  protected function getEditableConfigNames() {
    return [
      'ausy_family_event.add_department',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ausy_family_event_add_department';
  }

  /**
   * {@inheritdoc}
   *
   * @param mixed[] $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return mixed[]
   *   Build form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ausy_family_event.add_department');

    $form['machine_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Machine name'),
        '#default_value' => $config->get('machine_name'),
        '#required' => TRUE,
    ];
    $form['human_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Human name'),
        '#default_value' => $config->get('human_name'),
        '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   *
   * @param mixed[] $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

  }

  /**
   * {@inheritdoc}
   *
   * @param mixed[] $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);

    $this->config('ausy_family_event.add_department')
      ->set('machine_name', $form_state->getValue('machine_name'))
      ->set('human_name', $form_state->getValue('human_name'))
      ->save();
  }

}
