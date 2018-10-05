<?php

namespace Drupal\theme_converter\Commands;

use Drush\Commands\DrushCommands;

/**
 * Drush command file.
 */
class ThemeConverterCommands extends DrushCommands {
  /**
   * Command generates custom theme from HTML design.
   *
   * @param string $name
   *   Theme name provided to the drush command.
   *
   * @command theme_converter:convert-theme
   * @aliases ctheme
   * @usage theme_converter:convert-theme newtheme
   *   Generated new theme files and displays success message.
   */
  public function convert_theme($name) {
    $target = 'themes/custom/' . $name;
    if (!\Drupal::service('file_system')->mkdir($target, 0777, true)) {
      $this->output()->writeln($name . " theme directory already exists.");
      return;
    }
    else {
      $this->output()->writeln($name . ' theme directory successfully generated.');
    }

    // Generate .info.yml file.
    $file = $name . '.info.yml';
    $handle = fopen(getcwd() . '/' . $target . '/' . $file, 'w') or die('Cannot open file:  ' . $file);
    $data = 'name: ' . $name . '
type: theme
description: \'Theme generated by Theme Converter.\'
package: Custom
core: 8.x
libraries:
  - ' . $name . '/global-styling
regions:
      header: header
      content: content
      sidebar: sidebar
      footer: Footer
base theme: classy
#Using Classy as a base theme https://www.drupal.org/theme-guide/8/classy';
    fwrite($handle, $data);

    // Generate .libraries.yml file.
    $file = $name . '.libraries.yml';
    $handle = fopen(getcwd() . '/' . $target . '/' . $file, 'w') or die('Cannot open file:  ' . $file);
    $data = 'global-styling:
   version: VERSION
   css:
     theme:
       includes/bootstrap/css/bootstrap.css: {}
       css/style.css: {}';
    fwrite($handle, $data);

    // Generate templates directory.
    $template_dir = 'themes/custom/' . $name . '/templates';
    if (!\Drupal::service('file_system')->mkdir($template_dir, 0777, true)) {
      $this->output()->writeln($name . " template directory already exists.");
    }
    else {
      $this->output()->writeln($name . ' template directory successfully generated.');
    }

    // Generate page.html.twig
    $file = 'page.html.twig';
    $handle = fopen(getcwd() . '/' . $template_dir . '/' . $file, 'w') or die('Cannot open file:  ' . $file);
    $data = '{{ page.header }}
{{ page.content }}
    {% if page.sidebar %}
        {{ page.sidebar }}
    {% endif %}
{% if page.footer %}
    {{ page.footer }}
{% endif %}';
    fwrite($handle, $data);

  }
}
