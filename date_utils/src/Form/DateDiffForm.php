<?php

namespace Drupal\date_utils\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\date_utils\DateUtils;

/**
 * The DateDiffForm form class.
 */
class DateDiffForm extends FormBase {

  /**
   * The date utils service.
   *
   * @var \Drupal\date_utils\DateUtils
   */
  protected $dateUtils;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'date_utils_date_diff_form';
  }

  /**
   * StuffMediaEditForm constructor.
   *
   * @param Drupal\date_utils\DateUtils $date_utils
   *   The date_utils service.
   */
  public function __construct(DateUtils $date_utils) {
    $this->dateUtils = $date_utils;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date_utils.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['date'] = [
      '#type' => 'container',
      '#description' => $this->t('Find number of days between dates.'),
      '#prefix' => '<div id="dates-wrapper">',
        '#suffix' => '</div>',
    ];

    $input = $form_state->getUserInput();
    $start = $input['start'];
    $end_attributes = [];
    if ($start) {
      $end_attributes = [
        'type' => 'date',
        'min' => $start
      ];
    }

    $values = $form_state->getValues();
    if (isset($values['result'])) {
      $form['date']['result'] = [
        '#markup' => $this->t('There are %days days between the given dates.', ['%days' => $values['result']])
      ];
    }

    $form['date']['start'] = [
      '#type' => 'date',
      '#title' => $this->t('Start Date'),
      '#ajax' => [
        'callback' => '::dateCallback',
        'wrapper' => 'dates-wrapper',
      ],
    ];

    $form['date']['end'] = [
      '#type' => 'date',
      '#title' => $this->t('Start Date'),
      '#attributes' => $end_attributes,
    ];

    $form['#validate'][] = [$this, 'validateForm'];

    $form['actions']['wrapper'] = [
      '#type' => 'container',
    ];

    $form['actions']['wrapper']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Calculate'),
    ];

    return $form;
  }

  /**
   * Entity type select ajax callback.
   */
  public function dateCallback(array &$form, FormStateInterface $form_state) {
    return $form['date'];
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $start = $values['start'];
    $end = $values['end'];
    if (($start instanceof DrupalDateTime && $end instanceof DrupalDateTime) && ($start > $end)) {
      $form_state->setError($form['date']['end'], \t('End date should be greater than start date.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $start = $values['start'];
    $end = $values['end'];

    // Calculate number of days between given dates.
    $num_days = $this->dateUtils->DateNumDays($start, $end);

    $form_state->setValue('result', $num_days);
    $form_state->setRebuild();
  }
}
