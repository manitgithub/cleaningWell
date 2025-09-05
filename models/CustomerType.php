<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "customer_types".
 *
 * @property int $id
 * @property string $name
 * @property int $is_active
 * 
 * @property Customer[] $customers
 */
class CustomerType extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customer_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id', 'is_active'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['is_active'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ชื่อประเภท',
            'is_active' => 'สถานะ',
        ];
    }

    /**
     * Get customers relation
     * @return \yii\db\ActiveQuery
     */
    public function getCustomers()
    {
        return $this->hasMany(Customer::class, ['customer_type_id' => 'id']);
    }

    /**
     * Get active customer types
     * @return array
     */
    public static function getActiveTypes()
    {
        return self::find()
            ->where(['is_active' => self::STATUS_ACTIVE])
            ->orderBy(['id' => SORT_ASC])
            ->all();
    }

    /**
     * Get type options for dropdown
     * @return array
     */
    public static function getTypeOptions()
    {
        $types = self::getActiveTypes();
        $options = [];
        foreach ($types as $type) {
            $options[$type->id] = $type->name;
        }
        return $options;
    }

    /**
     * Get status label
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน';
    }

    /**
     * Get status badge
     * @return string
     */
    public function getStatusBadge()
    {
        $class = $this->is_active ? 'success' : 'secondary';
        return '<span class="badge badge-' . $class . '">' . $this->getStatusLabel() . '</span>';
    }
}
