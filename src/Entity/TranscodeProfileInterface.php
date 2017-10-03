<?php

namespace Drupal\transcode_profile\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Transcode profile entities.
 */
interface TranscodeProfileInterface extends ConfigEntityInterface {

  /**
   * Get the codec for a transcode profile.
   *
   * @return string
   */
  public function getCodec();

  /**
   * Set the codec for a transcode profile.
   *
   * @param string $codec
   *   The codec to set.
   *
   * @return TranscodeProfile
   */
  public function setCodec($codec);
}
