<?php

namespace Statamic\Addons\TranslationManager\Exporting\Preparators;

use Statamic\Addons\TranslationManager\Helpers\Field;
use Statamic\Addons\TranslationManager\Helpers\Locale;
use Statamic\Addons\TranslationManager\Exporting\Preparators\Fields\ArrayField;
use Statamic\Addons\TranslationManager\Exporting\Preparators\Fields\TableField;
use Statamic\Addons\TranslationManager\Exporting\Preparators\Fields\StringField;
use Statamic\Addons\TranslationManager\Exporting\Preparators\Fields\ReplicatorField;

class FieldPreparator
{
    /**
     * The processed fields, mapped into an exportable structure.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Prepares the fields in the page or entry to be exported.
     *
     * @param object $item
     * @return array
     */
    public function prepare($item)
    {
        foreach ($item->data() as $fieldName => $value) {
            $field = new Field($item, $fieldName);

            // Determine whether the field should be translated, or skipped.
            if (!$fieldName || !$field->shouldBeTranslated()) {
                continue;
            }

            // Handle the various field types. They all store the actual
            // values in different ways, so we have to map them into a
            // common structure before exporting them.
            $this->handleFieldTypes($field, [
                'original_value' => $item->in(Locale::default())->get($fieldName),
                'localized_value' => $value,
                'field_name' => $fieldName,
                'field_type' => $field->type,
            ]);
        }

        return $this->fields;
    }

    /**
     * Parses the various field types into a common structure.
     *
     * @param Field $field
     * @param array $fieldData
     * @return void
     */
    protected function handleFieldTypes($field, $fieldData)
    {
        switch ($field->type) {
            // Untranslatable fields. These do not include the
            // actual label in the page data.
            case 'suggest':
            case 'radio':
            case 'checkboxes':
                continue;
                break;

            case 'array':
            case 'collection':
            case 'list':
            case 'tags':
            case 'checkbox':
                $this->fields = (new ArrayField($this->fields))->map($fieldData);
                break;

            case 'table':
                $this->fields = (new TableField($this->fields))->map($fieldData);
                break;

            case 'replicator':
                $this->fields = (new ReplicatorField($this->fields))->map($fieldData);
                break;

            // "Default" fields include:
            // - Bard
            // - Regular string values
            default:
                $this->fields = (new StringField($this->fields))->map($fieldData);
                break;
        }
    }
}
