<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "customers".
 *
 * @property int $id
 * @property int $customer_type_id
 * @property string $name
 * @property string|null $branch
 * @property string|null $tax_id
 * @property string|null $citizen_id
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $contact_name
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * 
 * @property CustomerType $customerType
 */
class Customer extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'customers';
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
                'value' => function() {
                    return date('Y-m-d H:i:s');
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_type_id', 'name'], 'required'],
            [['customer_type_id', 'status'], 'integer'],
            [['address'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['branch', 'contact_name', 'email'], 'string', 'max' => 128],
            [['tax_id', 'citizen_id', 'phone'], 'string', 'max' => 32],
            [['email'], 'email'],
            [['customer_type_id'], 'exist', 'targetClass' => CustomerType::class, 'targetAttribute' => 'id'],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_type_id' => 'ประเภทลูกค้า',
            'name' => 'ชื่อลูกค้า',
            'branch' => 'สาขา',
            'tax_id' => 'เลขประจำตัวผู้เสียภาษี',
            'citizen_id' => 'เลขบัตรประชาชน',
            'address' => 'ที่อยู่',
            'phone' => 'โทรศัพท์',
            'email' => 'อีเมล',
            'contact_name' => 'ผู้ติดต่อ',
            'status' => 'สถานะ',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
        ];
    }

    /**
     * Get customer type relation
     * @return \yii\db\ActiveQuery
     */
    public function getCustomerType()
    {
        return $this->hasOne(CustomerType::class, ['id' => 'customer_type_id']);
    }

    /**
     * Get status options
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => 'ใช้งาน',
            self::STATUS_INACTIVE => 'ไม่ใช้งาน',
        ];
    }

    /**
     * Get status label
     * @return string
     */
    public function getStatusLabel()
    {
        $statuses = self::getStatusOptions();
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get status badge
     * @return string
     */
    public function getStatusBadge()
    {
        $class = $this->status == self::STATUS_ACTIVE ? 'success' : 'secondary';
        return '<span class="badge badge-' . $class . '">' . $this->getStatusLabel() . '</span>';
    }

    /**
     * Get customer type label
     * @return string
     */
    public function getTypeLabel()
    {
        return $this->customerType ? $this->customerType->name : '-';
    }

    /**
     * Get customer type badge
     * @return string
     */
    public function getTypeBadge()
    {
        if (!$this->customerType) {
            return '<span class="badge badge-secondary">-</span>';
        }
        
        $badges = [
            1 => 'info',      // Individual
            2 => 'primary',   // Company
            3 => 'warning',   // Government
            9 => 'secondary', // Other
        ];
        
        $class = $badges[$this->customer_type_id] ?? 'secondary';
        return '<span class="badge badge-' . $class . '">' . $this->customerType->name . '</span>';
    }

    /**
     * Get display name (name with branch if available)
     * @return string
     */
    public function getDisplayName()
    {
        return $this->name . ($this->branch ? ' (' . $this->branch . ')' : '');
    }

    /**
     * Before save
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && $this->status === null) {
                $this->status = self::STATUS_ACTIVE;
            }
            return true;
        }
        return false;
    }
}
