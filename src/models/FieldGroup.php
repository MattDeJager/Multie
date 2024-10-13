<?php

namespace boost\multie\models;

use Craft;
use craft\base\Model;
use craft\records\FieldGroup as FieldGroupRecord;
use craft\validators\UniqueValidator;

class FieldGroup extends Model
{

    public $id = null;

    public $name = "";
    public $fieldTypes = [];

    public function attributeLabels(): array
    {
        return [
            'name' => Craft::t('app', 'Name'),
        ];
    }

    public function getFieldTypes(): array
    {
        return $this->fieldTypes;
    }

    public function setFieldTypes(array $fieldTypes): void
    {
        $this->fieldTypes = $fieldTypes;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['name'], 'string', 'max' => 255];
        $rules[] = [['name'], UniqueValidator::class, 'targetClass' => FieldGroupRecord::class];
        $rules[] = [['name'], 'required'];
        return $rules;
    }

    /**
     * Use the group name as the string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->name ?: static::class;
    }


}