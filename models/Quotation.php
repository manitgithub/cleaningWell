<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "quotations".
 *
 * @property int $id
 * @property string $code
 * @property int $project_id
 * @property int $customer_id
 * @property string $date
 * @property string $expire_date
 * @property string $subject
 * @property string $notes
 * @property float $sub_total
 * @property float $discount_total
 * @property float $vat_rate
 * @property float $vat_amount
 * @property float $wht_rate
 * @property float $wht_amount
 * @property float $grand_total
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Project $project
 * @property Customer $customer
 * @property QuotationItem[] $items
 */
class Quotation extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'quotations';
    }

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

    public function rules()
    {
        return [
            [['code', 'project_id', 'customer_id', 'date'], 'required'],
            [['project_id', 'customer_id', 'status'], 'integer'],
            [['date', 'expire_date', 'created_at', 'updated_at'], 'safe'],
            [['sub_total', 'discount_total', 'vat_rate', 'vat_amount', 'wht_rate', 'wht_amount', 'grand_total'], 'number'],
            [['code'], 'string', 'max' => 32],
            [['subject', 'notes'], 'string'],
            [['code'], 'unique'],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'เลขที่ใบเสนอราคา',
            'project_id' => 'โครงการ',
            'customer_id' => 'ลูกค้า',
            'date' => 'วันที่',
            'expire_date' => 'วันหมดอายุ',
            'subject' => 'เรื่อง',
            'notes' => 'หมายเหตุ',
            'sub_total' => 'รวมก่อนลด',
            'discount_total' => 'ส่วนลด',
            'vat_rate' => 'VAT (%)',
            'vat_amount' => 'VAT',
            'wht_rate' => 'WHT (%)',
            'wht_amount' => 'WHT',
            'grand_total' => 'รวมสุทธิ',
            'status' => 'สถานะ',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
        ];
    }

    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getItems()
    {
        return $this->hasMany(QuotationItem::class, ['quotation_id' => 'id']);
    }
}
