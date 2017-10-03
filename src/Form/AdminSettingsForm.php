<?php

namespace Drupal\transcode_profile\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityTypeManager;

/**
 * Class AdminSettingsForm.
 */
class AdminSettingsForm extends ConfigFormBase {
  // I added this since i think it makes sense.
  // This also means i can use AdminSettingsForm::CONFIG_NAME
  // to get the config name outside the class, and we
  // don't need to instantiate the class to to do so.
  const CONFIG_NAME = 'transcode_profile.adminsettings';

  /**
   * Drupal\Core\Config\ConfigFactory definition.
   *
   * @var Drupal\Core\Config\ConfigFactory
   */
  protected $config_factory;

  /**
   * Drupal\Core\Entity\EntityTypeManager definition.
   *
   * @var Drupal\Core\Entity\EntityTypeManager
   */
  protected $entity_type_manager;

  /**
   * AdminSettingsForm constructor.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   * @param \Drupal\Core\Entity\EntityTypeManager $entity_type_manager
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    //ConfigFactory $config_factory,
    EntityTypeManager $entity_type_manager
  ) {
    parent::__construct($config_factory);
    $this->config_factory = $config_factory;
    $this->entity_type_manager = $entity_type_manager;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      //$container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    // We could return the name of any config we want here,
    // but obviously we want the config associated with this form.
    return [
      self::CONFIG_NAME,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get the saved config if it exists.
    $config = $this->config(self::CONFIG_NAME);

    // Load the available profiles.
    $transcode_profiles = $this->entity_type_manager
      ->getStorage('transcode_profile')->loadMultiple();

    // Build drop down options from the available profiles.
    $select_options = [];
    foreach ($transcode_profiles as $profile) {
      $select_options[$profile->id()] = $profile->label();
    }

    // Custom form elements.
    $form['profile_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Profile ID'),
      '#description' => $this->t('Video transcode profile ID'),
      '#default_value' => $config->get('profile_id'),
      '#options' => $select_options,
    ];
    $form['enable_transcoding'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable transcoding'),
      '#description' => $this->t('Enables video transcoding'),
      '#default_value' => $config->get('enable_transcoding'),
    ];

    // The rest of the form building needs
    // to happen after we define our form fields.
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // The drupal heavy lifting of form submission.
    parent::submitForm($form, $form_state);

    // Our stuff. Just update config.
    $this->config(self::CONFIG_NAME)
      ->set('profile_id', $form_state->getValue('profile_id'))
      ->set('enable_transcoding', $form_state->getValue('enable_transcoding'))
      ->save();
  }

}
