<?php

namespace gf_anti_spam;

/**
 * User: leochabu
 * Date: 10/18/2024
 * Time: 7:46 PM
 */

class GF_Field_Mapper {

    /**
     * Holds the instance of the GF_Field_Mapper singleton.
     *
     * @var GF_Field_Mapper
     */
    private static $instance = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {}

    /**
     * Retrieves the singleton instance of the GF_Field_Mapper.
     *
     * @return GF_Field_Mapper The singleton instance.
     */
    public static function get_instance(): ?GF_Field_Mapper
    {
        if (self::$instance === null) {
            self::$instance = new GF_Field_Mapper();
        }
        return self::$instance;
    }

    /**
     * Returns the ID of a field in a Gravity Form based on the input name.
     *
     * @param array $form The Gravity Form or entry array.
     * @param string $input_name The input name (attribute) of the field.
     * @return int|null The ID of the field if found, or null if not found.
     */
    public function field_id_by_name($form, $input_name): ?int
    {
        if (!isset($form['fields'])) {
            return null;
        }

        foreach ($form['fields'] as $field) {
            if (isset($field->inputName) && $field->inputName === $input_name) {
                return $field->id;
            }
        }

        return null;
    }

    /**
     * Returns the ID of a field in a Gravity Form based on the CSS class.
     *
     * @param array $form The Gravity Form or entry array.
     * @param string $css_class The CSS class associated with the field.
     * @return int|null The ID of the field if found, or null if not found.
     */
    public function field_id_by_css_class($form, $css_class): ?int
    {
        if (!isset($form['fields'])) {
            return null;
        }

        foreach ($form['fields'] as $field) {
            if (isset($field->cssClass) && strpos($field->cssClass, $css_class) !== false) {
                return $field->id;
            }
        }

        return null;
    }
}