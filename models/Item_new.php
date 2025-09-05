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
 * @property string $unit
 * @property string $base_price
 * @property int $vat_applicable
 * @property string $wht_default
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
            [['base_price', 'wht_default'], 'number'],
            [['vat_applicable', 'is_active'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['unit'], 'string', 'max' => 32],
            ['vat_applicable', 'in', 'range' => [0, 1]],
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
            'unit' => 'หน่วย',
            'base_price' => 'ราคาหน่วย',
            'vat_applicable' => 'คิด VAT',
            'wht_default' => 'หัก ณ ที่จ่าย (%)',
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
     * Get VAT applicable badge
     */
    public function getVatBadge()
    {
        return $this->vat_applicable == 1 
            ? '<span class="badge badge-info">VAT</span>' 
            : '<span class="badge badge-light">No VAT</span>';
    }

    /**
     * Get formatted price
     */
    public function getFormattedPrice()
    {
        return number_format($this->base_price, 2) . ' บาท';
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
     * Get VAT options for dropdown
     */
    public static function getVatOptions()
    {
        return [
            1 => 'คิด VAT',
            0 => 'ไม่คิด VAT',
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
            if ($insert && !isset($this->vat_applicable)) {
                $this->vat_applicable = 1;
            }
            if ($insert && !isset($this->wht_default)) {
                $this->wht_default = 0;
            }
            if ($insert && empty($this->unit)) {
                $this->unit = 'หน่วย';
            }
            return true;
        }
        return false;
    }
}
