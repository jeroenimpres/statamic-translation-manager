<?php

namespace Statamic\src\Exporting\Preparators;

use Statamic\src\Exporting\Preparators\Fields\ArrayField;
use Statamic\src\Exporting\Preparators\Fields\ReplicatorField;
use Statamic\src\Exporting\Preparators\Fields\StringField;
use Statamic\src\Helpers\Field;
use Statamic\src\Helpers\Locale;

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
        // Reset the fields for each item.
        $this->fields = [];

        $localizedItem = $item->in(Locale::default());

        $data = $localizedItem->data();

        if ($slug = @$localizedItem->slug()) {
            $data['slug'] = $slug;
        }

        foreach ($data as $fieldName => $value) {
            $field = new Field($item, $fieldName);

            // Determine whether the field should be translated, or skipped.
            if (!$fieldName || !$field->shouldBeTranslated()) {
                continue;
            }

            // Handle the various field types. They all store the actual
            // values in different ways, so we have to map them into a
            // common structure before exporting them.
            $this->handleFieldTypes($field, [
                'original_value' => $value,
                'localized_value' => $item->get($fieldName) ?: '',
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
                return;

            case 'array':
            case 'collection':
            case 'list':
            case 'tags':
            case 'checkbox':
                $this->fields = (new ArrayField($this->fields))->map($fieldData);
                break;

            case 'table':
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
