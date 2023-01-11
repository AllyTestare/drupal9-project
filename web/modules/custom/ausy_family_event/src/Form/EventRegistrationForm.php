<?php

namespace Drupal\ausy_family_event\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\Core\Messenger\MessengerTrait;

/**
 * Configuration settings form for event registrations.
 */
class EventRegistrationForm extends ConfigFormBase {

  use MessengerTrait;

  /**
   * {@inheritdoc}
   *
   * @return mixed[]
   *   Get config names.
   */
  protected function getEditableConfigNames() {
    return [
      'ausy_family_event.form',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ausy_family_event_form';
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
    $config = $this->config('ausy_family_event.form');

    // Retrieve the department from the URL.
    $current_path = \Drupal::service('path.current')->getPath();
    $path_args = explode('/', $current_path);
    $department = $path_args[count($path_args) - 1];

    $form['department'] = [
      '#type' => 'hidden',
      '#default_value' => $department];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of the employee'),
      '#default_value' => $config->get('name'),
      '#required' => TRUE,
    ];

    $form['one_plus'] = [
      '#type' => 'select',
      '#title' => $this->t('One Plus'),
      '#default_value' => $config->get('one_plus'),
      '#options' => [
        'yes' => $this->t('Yes'),
        'no' => $this->t('No'),
      ],
      '#required' => TRUE,
    ];

    $form['kids'] = [
      '#type' => 'number',
      '#title' => $this->t('Amount of kids'),
      '#default_value' => $config->get('kids'),
      '#required' => TRUE,
    ];

    $form['vegetarians'] = [
      '#type' => 'number',
      '#title' => $this->t('Amount of vegetarians'),
      '#default_value' => $config->get('vegetarians'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#description' => $this->t('Email address'),
      '#title' => $this->t('Email'),
      '#default_value' => $config->get('email'),
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

    // Check all required fields.
    if (!$form_state->getValue('name')) {
      $form_state->setErrorByName('name', $this->t('Name is required'));
    }
    if (!$form_state->getValue('email')) {
      $form_state->setErrorByName('email', $this->t('Email is required'));
    }
    if (!$form_state->getValue('kids')) {
      $form_state->setErrorByName('kids', $this->t('Amount of kids is required'));
    }
    if (!$form_state->getValue('vegetarians')) {
      $form_state->setErrorByName('vegetarians', $this->t('Amount of vegetarians is required'));
    }
    if (!$form_state->getValue('one_plus')) {
      $form_state->setErrorByName('one_plus', $this->t('One plus is required'));
    }

    // Check if the email address is valid.
    if (!\Drupal::service('email.validator')->isValid($form_state->getValue('email'))) {
      $form_state->setErrorByName('email', $this->t('Invalid email address'));
    }

    // Check if the number of vegetarians is higher than the total number of people.
    if ($form_state->getValue('vegetarians') > $form_state->getValue('kids') + 1) {
      $form_state->setErrorByName('vegetarians', $this->t('The number of vegetarians can not be higher than the total number of people'));
    }

    // Check if the employee has already registered with the same email.
    if (!$this->validateUniqueEmail($form_state->getValue('email'))) {
      $form_state->setErrorByName('email', $this->t('An employee with this email has already registered'));
    }

    // Check if the department is valid.
    if (!$form_state->getValue('department')) {
      $form_state->setErrorByName('department', $this->t('Department is required'));
  }

  }

  /**
   * Validate that an email address has not already been used.
   *
   * @param string $email
   *   The email address to validate.
   *
   * @return bool
   *   TRUE if the email address is unique, FALSE otherwise.
   */
  function validateUniqueEmail($email) {
    // Drupal will run the query for each submit.
    // For optimization I included a unique constraint,
    // on the email field in the registration content type.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'registration')
      ->condition('field_email_address', $email)
      ->range(0, 1);
    $result = $query->execute();
    return empty($result);
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

    $department = $form_state->getValue('department');

    // Check if department is empty.
    if(empty($department)) {
      $form_state->setErrorByName('department', $this->t('Department is required'));
      return;
    }
    $values = $form_state->getValues();
    // Creating a new node
    $node = Node::create([
      'type' => 'registration',
      'title' => 'Registration',
      'field_name_of_the_employee' => $values['name'],
      'field_one_plus' => $values['one_plus'],
      'field_amount_of_kids' => $values['kids'],
      'field_amount_of_vegetarians' => $values['vegetarians'],
      'field_email_address' => $values['email'],
    ]);
    $node->set('field_department', $department);
    $node->save();
    $this->messenger()->addMessage($this->t('Your registration was saved'));
  }

}
