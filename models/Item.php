<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "items".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $unit
 * @property string $unit_price
 * @property int $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class Item extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'items';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['unit_price'], 'number'],
            [['is_active'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string'],
            [['unit'], 'string', 'max' => 32],
            ['is_active', 'in', 'range' => [0, 1]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'ชื่อสินค้า/บริการ',
            'description' => 'รายละเอียด',
            'unit' => 'หน่วย',
            'unit_price' => 'ราคาหน่วย',
            'is_active' => 'สถานะ',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
        ];
    }

    /**
     * Get active status badge
     */
    public function getStatusBadge()
    {
        return $this->is_active == 1 
            ? '<span class="badge badge-success">Active</span>' 
            : '<span class="badge badge-secondary">Inactive</span>';
    }



    /**
     * Get formatted price
     */
    public function getFormattedPrice()
    {
        return number_format($this->unit_price, 2) . ' บาท';
    }

    /**
     * Get status options for dropdown
     */
    public static function getStatusOptions()
    {
        return [
            1 => 'Active',
            0 => 'Inactive',
        ];
    }



    /**
     * Get unit options for dropdown
     */
    public static function getUnitOptions()
    {
        return [
            'หน่วย' => 'หน่วย',
            'ชิ้น' => 'ชิ้น',
            'ครั้ง' => 'ครั้ง',
            'วัน' => 'วัน',
            'เดือน' => 'เดือน',
            'ปี' => 'ปี',
            'ชุด' => 'ชุด',
            'กิโลกรัม' => 'กิโลกรัม',
            'เมตร' => 'เมตร',
            'ตารางเมตร' => 'ตารางเมตร',
            'ลิตร' => 'ลิตร',
        ];
    }

    /**
     * Before save
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && !isset($this->is_active)) {
                $this->is_active = 1;
            }
            if ($insert && empty($this->unit)) {
                $this->unit = 'หน่วย';
            }
            if ($insert && empty($this->unit_price)) {
                $this->unit_price = 0;
            }
            return true;
        }
        return false;
    }
}
